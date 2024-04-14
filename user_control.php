
<?php
require 'connection.php'; // Ensure this is the path to your database connection setup

// Handle POST requests for user operations
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['add_user'])) {
        $result = addUser($_POST['username'], $_POST['email'], $_POST['password'], $_POST['type'], $pdo);
        $_SESSION['message'] = $result ? "User added successfully!" : "Error adding user.";
    } elseif (isset($_POST['update_user'])) {
        $result = updateUser($_POST['user_id'], $_POST['username'], $_POST['email'], $_POST['type'], $pdo);
        $_SESSION['message'] = $result ? "User updated successfully!" : "Error updating user.";
    } elseif (isset($_POST['delete_user'])) {
        $result = deleteUser($_POST['user_id'], $pdo);
        $_SESSION['message'] = $result ? "User deleted successfully!" : "Error deleting user.";
    }
    header("Location: admin_panel.php");
    exit;
}

// User managemet functions
function addUser($username, $email, $password, $type, $pdo) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, type) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $email, $hashedPassword, $type]);
    return $stmt->rowCount();
}

function updateUser($userId, $username, $email, $type, $pdo) {
    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, type = ? WHERE user_id = ?");
    $stmt->execute([$username, $email, $type, $userId]);
    return $stmt->rowCount();
}

function deleteUser($userId, $pdo) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->rowCount();
}

function getUserById($userId, $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getAllUsers($offset, $limit, $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM users ORDER BY user_id ASC LIMIT ?, ?");
    $stmt->execute([$offset, $limit]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch user details if an edit is requested
$userToEdit = null;
if (isset($_GET['editUserId'])) {
    $editUserId = $_GET['editUserId'];
    $userToEdit = getUserById($editUserId, $pdo);
}


// Pagination setup
$usersPerPage = 10;
$userPage = isset($_GET['userPage']) ? (int)$_GET['userPage'] : 1;
$userPage = max($userPage, 1);
$userOffset = ($userPage - 1) * $usersPerPage;

// Fetch users with limit and offset for pagination
$userStmt = $pdo->prepare("SELECT * FROM users ORDER BY user_id ASC LIMIT :offset, :usersPerPage");
$userStmt->bindParam(':offset', $userOffset, PDO::PARAM_INT);
$userStmt->bindParam(':usersPerPage', $usersPerPage, PDO::PARAM_INT);
$userStmt->execute();
$users = $userStmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total pages for users
$totalUsersStmt = $pdo->query("SELECT COUNT(*) FROM users");
$totalUsers = $totalUsersStmt->fetchColumn();
$totalUserPages = ceil($totalUsers / $usersPerPage);

// Optional: display the user management HTML
if (isset($displayUserManagement) && $displayUserManagement) {
?>

<section class="user-management">
    <h2>Manage Users</h2>

    <!-- Form for adding a new user -->
    <form action="user_control.php" method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="type">
            <option value="normal">Normal</option>
            <option value="admin">Admin</option>
        </select>
        <button type="submit" name="add_user">Add User</button>
    </form>

    <?php if ($userToEdit): // Display the edit form if a user is to be edited ?>
    <form action="user_control.php" method="post">
        <input type="hidden" name="user_id" value="<?= htmlspecialchars($userToEdit['user_id']) ?>">
        <input type="text" name="username" value="<?= htmlspecialchars($userToEdit['username']) ?>" required>
        <input type="email" name="email" value="<?= htmlspecialchars($userToEdit['email']) ?>" required>
        <select name="type">
            <option value="normal" <?= $userToEdit['type'] == 'normal' ? 'selected' : '' ?>>Normal</option>
            <option value="admin" <?= $userToEdit['type'] == 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>
        <button type="submit" name="update_user">Update User</button>
    </form>
    <?php endif; ?>

    <!-- User list and actions -->
    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['user_id']) ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td>
                    <a href="?editUserId=<?= $user['user_id'] ?>&userPage=<?= $userPage ?>">Edit</a> |
                    <form action="user_control.php" method="post" onsubmit="return confirm('Are you sure you want to delete this user?');">
                        <input type="hidden" name="user_id" value="<?= $user['user_id']; ?>">
                        <input type="hidden" name="delete_user" value="1">
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <!-- Pagination links -->
    <nav>
        <ul class="pagination">
            <?php for ($i = 1; $i <= $totalUserPages; $i++): ?>
                <li class="<?= $i == $userPage ? 'active' : ''; ?>">
                    <a href="?userPage=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</section>

<?php
}
?>