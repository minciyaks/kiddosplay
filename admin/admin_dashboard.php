<?php
session_start();
// Check if admin is logged in
if (!isset($_SESSION['admin_user_id'])) {
    header("Location: admin_login.php");
    exit();
}

require '../db_config.php';
// Use the $mysqli_conn object created in the included file
$conn = $mysqli_conn;

// --- MODIFIED SQL QUERY WITH A LEFT JOIN ---
// This fetches all pending users and joins their corresponding certificate path, if one exists.
$sql = "
    SELECT
        u.user_id,
        u.username,
        u.email,
        u.role,
        c.file_path
    FROM
        users u
    LEFT JOIN
        certificates c ON u.user_id = c.user_id
    WHERE
        u.status = 'pending'
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="dashboard-container">
        <h2>Admin Dashboard</h2>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>! - <a href="admin_logout.php" style="color: #dc3545  ">Logout</a></p>
        <hr>
        <h3>Pending User Approvals</h3>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Certificate</th> <th>Action</th>
                </tr>
            </thead>
            <tbody>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . htmlspecialchars($row['role']) . "</td>";

            // --- THIS IS THE CORRECTED LOGIC FOR THE CERTIFICATE COLUMN ---
            echo "<td>";
            // Check if the 'file_path' is not empty
            if (!empty($row['file_path'])) {
                // If it exists, show the "View" button
                echo '<a href="../' . htmlspecialchars($row['file_path']) . '" target="_blank" class="btn">View</a>';
            } else {
                // If it's empty, show "None"
                echo "None";
            }
            echo "</td>";
            // --- END OF LOGIC ---

            echo '<td>
                    <a href="approve_user.php?id=' . $row['user_id'] . '&status=approved" class="btn approve">Approve</a>
                    <a href="approve_user.php?id=' . $row['user_id'] . '&status=rejected" class="btn reject">Reject</a>
                  </td>';
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='5'>No pending users found.</td></tr>";
    }
    ?>
</tbody>
        </table>
    </div>
</body>
</html>
<?php
$conn->close();
?>