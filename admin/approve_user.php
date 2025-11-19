<?php
session_start();

// 1. Check if Admin is logged in
// This is perfect!
if (!isset($_SESSION['admin_user_id'])) { 
    header("Location: admin_login.php");
    exit();
}

// Get the admin's ID from the session to log who did the review
$admin_id = $_SESSION['admin_user_id'];

require '../db_config.php';
// Use the $mysqli_conn object created in the included file
$conn = $mysqli_conn;

// 2. Check URL parameters
if (isset($_GET['id']) && isset($_GET['status'])) {
    $user_id_to_update = intval($_GET['id']);
    $new_status = $_GET['status']; // e.g., 'approved' or 'rejected'

    // 3. Validate the new status
    if ($new_status == 'approved' || $new_status == 'rejected') {
        
        // --- 4. Start a Transaction ---
        $conn->begin_transaction();

        try {
            // --- First, update the 'users' table ---
            // This is the code you already wrote
            $stmt1 = $conn->prepare("UPDATE users SET status = ? WHERE user_id = ?");
            $stmt1->bind_param("si", $new_status, $user_id_to_update);
            $stmt1->execute();
            $stmt1->close();

            // --- Second, update the 'certificates' table ---
            // This is the new part. It updates the certificate status AND records who reviewed it.
            $stmt2 = $conn->prepare("UPDATE certificates SET status = ?, reviewed_by = ? WHERE user_id = ?");
            $stmt2->bind_param("sii", $new_status, $admin_id, $user_id_to_update);
            $stmt2->execute();
            $stmt2->close();
            
            // --- 5. Commit Transaction ---
            // If both updates worked, make them permanent
            $conn->commit();

            header("Location: admin_dashboard.php");
            exit();

        } catch (mysqli_sql_exception $exception) {
            // --- 6. Rollback Transaction ---
            // If anything failed, undo all changes
            $conn->rollback();
            echo "Error updating records: " . $exception->getMessage();
        }

    } else {
        echo "Invalid status value.";
    }
} else {
    echo "Required parameters are missing.";
}

$conn->close();
?>