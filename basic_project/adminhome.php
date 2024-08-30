<?php
// Include the database connection file
include("connection.php");

// Start the session
session_start();

// Check if the user is logged in; if not, redirect to login page
if (!isset($_SESSION['firstname'])) {
    header("Location: adminlogin.php");
    exit();
}

// Get the user's first name
$firstname = $_SESSION['firstname'];

// Fetch the total number of registered users
$query = "SELECT COUNT(*) as total_users FROM signupusers";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$total_users = $row['total_users'];

// Pagination settings
$limit = 5; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Sorting settings
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Filter settings
$search_query = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%';

// Fetch total number of users for pagination
$total_result = $conn->query("SELECT COUNT(*) as total FROM signupusers WHERE email LIKE '$search_query'");
$total_row = $total_result->fetch_assoc();
$total_users_filtered = $total_row['total'];
$total_pages = ceil($total_users_filtered / $limit);

// Fetch users with pagination, sorting, and filtering
$query = $conn->prepare("SELECT id, firstname, lastname, email, contact FROM signupusers WHERE email LIKE ? ORDER BY $sort_column $sort_order LIMIT ?, ?");
$query->bind_param('sii', $search_query, $start, $limit);
$query->execute();
$result = $query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Home</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .header {
            padding: 20px;
            background-color: black;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            margin: 0;
            font-size: 1.8em;
        }

        .header input[type="search"] {
            padding: 8px 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1em;
            outline: none;
        }

        .user-menu {
            position: relative;
            display: flex;
            align-items: center;
        }

        .user-menu img {
            width: 24px;
            height: 24px;
            margin-right: 10px;
            cursor: pointer;
        }

        .dropdown {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
            overflow: hidden;
            z-index: 1000;
        }

        .dropdown a {
            display: block;
            padding: 10px;
            color: #333;
            text-decoration: none;
            width: 80px;
        }

        .dropdown a:hover {
            background-color: #f4f4f4;
        }

        .user-menu:hover .dropdown {
            display: block;
        }

        .container {
            display: flex;
            width: 100%;
            height: calc(100vh - 70px); /* Adjust based on header height */
        }

        .sidebar {
            width: 20%;
            background-color: #333;
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            margin-bottom: 15px;
            font-size: 1.2em;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .sidebar a.active {
            background-color: #007BFF;
            color: white;
        }

        .sidebar a:hover {
            background-color: #444;
            padding-left: 15px;
        }

        .content {
            width: 80%;
            padding: 40px;
            background-color: white;
            overflow-y: auto;
            box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .content h2 {
            color: #333;
            margin-bottom: 20px;
        }

        .content p {
            font-size: 1.1em;
            line-height: 1.6;
            color: #555;
        }

        .hidden {
            display: none;
        }

        /* Manage Users Section */
        #manage-users {
            padding: 20px;
            background-color: #f4f4f4;
        }

        #manage-users h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        .add-user {
            margin-bottom: 20px;
        }

        .add-user a {
            display: inline-block;
            padding: 10px 15px;
            background-color: #237dda;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }

        .add-user a:hover {
            background-color: #0b7dda;
        }

        .filter-sort {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .filter-sort form {
            display: flex;
            gap: 10px;
        }

        .filter-sort input[type="text"] {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        


        .filter-sort button{
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            background-color:#157af6;
            color:white;

        }

        .table-container {
            overflow-x: auto;
        }

        .table-container table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-container th,
        .table-container td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .table-container th {
            background-color: #e0e0e0;
            color: #333;
            font-weight: bold;
        }

        .table-container tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .table-container .actions a {
            margin-right: 10px;
            color: #2196F3;
            text-decoration: none;
        }

        .table-container .actions a:hover {
            text-decoration: underline;
        }

        .pagination {
            margin-top: 20px;
            text-align: center;
        }

        .pagination a {
            display: inline-block;
            padding: 8px 12px;
            margin: 0 4px;
            background-color: #2196F3;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .pagination a.active,
        .pagination a:hover {
            background-color: #0b7dda;
        }

        .pagination a.disabled {
            background-color: #ddd;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome <?php echo htmlspecialchars($firstname); ?>!</h1>
        
        <div class="user-menu">
            <img src="images/icons8-user-24.png" alt="User Icon">
            <div class="dropdown">
                <a href="view_site.php">View Site</a>
                <a href="adminlogin.php">Logout</a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="sidebar">
            <a href="#" id="dashboard-tab" class="active">Dashboard</a>
            <a href="#" id="manage-users-tab">Manage Users</a>
            <a href="add_activities.php">Add Activities</a>
            <a href="adminlogin.php">Sign Out</a>
        </div>

        <div class="content">
            <!-- Dashboard content -->
            <div id="dashboard-content">
                <h2>Dashboard Overview</h2>
                <p>Total Number of Registered Users: <strong><?php echo $total_users; ?></strong></p>
            </div>

            <!-- Manage Users content -->
            <div id="manage-users" class="hidden">
                <h1>Manage Users</h1>
                <div class="add-user">
                    <a href="add_users.php"> +Add User</a>
                </div>
                <div class="filter-sort">
                    <form method="GET">
                        <input type="text" name="search" placeholder="Search by email" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                        <select name="sort">
                            <option value="id" <?php if ($sort_column === 'id') echo 'selected'; ?>>ID</option>
                            <option value="firstname" <?php if ($sort_column === 'firstname') echo 'selected'; ?>>First Name</option>
                            <option value="lastname" <?php if ($sort_column === 'lastname') echo 'selected'; ?>>Last Name</option>
                            <option value="email" <?php if ($sort_column === 'email') echo 'selected'; ?>>Email</option>
                            <option value="contact" <?php if ($sort_column === 'contact') echo 'selected'; ?>>Contact</option>
                        </select>
                        <select name="order">
                            <option value="ASC" <?php if ($sort_order === 'ASC') echo 'selected'; ?>>Ascending</option>
                            <option value="DESC" <?php if ($sort_order === 'DESC') echo 'selected'; ?>>Descending</option>
                        </select>
                        <button type="submit">Click to Filter / Sort</button>
                    </form>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['firstname']); ?></td>
                                    <td><?php echo htmlspecialchars($row['lastname']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['contact']); ?></td>
                                    <td class="actions">
                                        <a href="edit_user.php?id=<?php echo $row['id']; ?>">Edit</a>
                                        <a href="delete_user.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>&sort=<?php echo $sort_column; ?>&order=<?php echo $sort_order; ?>" class="<?php echo $i == $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var dashboardTab = document.getElementById('dashboard-tab');
            var manageUsersTab = document.getElementById('manage-users-tab');
            var dashboardContent = document.getElementById('dashboard-content');
            var manageUsersContent = document.getElementById('manage-users');

            function showDashboard() {
                dashboardTab.classList.add('active');
                manageUsersTab.classList.remove('active');
                dashboardContent.classList.remove('hidden');
                manageUsersContent.classList.add('hidden');
                localStorage.setItem('activeTab', 'dashboard');
            }

            function showManageUsers() {
                dashboardTab.classList.remove('active');
                manageUsersTab.classList.add('active');
                dashboardContent.classList.add('hidden');
                manageUsersContent.classList.remove('hidden');
                localStorage.setItem('activeTab', 'manage-users');
            }

            dashboardTab.addEventListener('click', showDashboard);
            manageUsersTab.addEventListener('click', showManageUsers);

            // Check which tab was active before the page refresh
            var activeTab = localStorage.getItem('activeTab');
            if (activeTab === 'manage-users') {
                showManageUsers();
            } else {
                showDashboard();
            }

            // Save the current page number for pagination
            document.querySelectorAll('.pagination a').forEach(function(pageLink) {
                pageLink.addEventListener('click', function() {
                    localStorage.setItem('currentPage', this.textContent);
                });
            });

            // Set the pagination to the stored page
            var currentPage = localStorage.getItem('currentPage');
            if (currentPage) {
                document.querySelectorAll('.pagination a').forEach(function(pageLink) {
                    if (pageLink.textContent === currentPage) {
                        pageLink.classList.add('active');
                    }
                });
            }
        });
    </script>
</body>
</html>

