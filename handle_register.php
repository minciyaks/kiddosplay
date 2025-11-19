<?php
session_start();

// --- 1. INPUT NORMALIZATION & VALIDATION ---

function redirectWithError($message) {
    $_SESSION['register_error'] = $message;
    header("Location: register.php");
    exit();
}

// A. Username Normalization
$username = trim($_POST['username'] ?? '');
if (strlen($username) < 3 || strlen($username) > 20 || !preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    redirectWithError("Username must be 3-20 characters and contain only letters, numbers, or underscores.");
}

// B. Email Normalization & Strict Gmail Enforcement
$email = strtolower(trim($_POST['email'] ?? ''));
if (strpos($email, '@') === false && !empty($email)) {
    $email .= '@gmail.com';
}
if (!preg_match('/^[a-zA-Z0-9._%+-]+@gmail\.com$/', $email)) {
    redirectWithError("Only @gmail.com email addresses are allowed.");
}

// C. Password Strength Validation
// CHANGED: strictly taking 6 characters minimum (was 8)
$password = $_POST['password'] ?? '';
if (strlen($password) < 6 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
     redirectWithError("Password must be at least 6 characters long, with at least one uppercase letter and one number.");
}

// D. Dropdown Whitelist Validation
$allowed_roles = ['parent', 'teacher'];
$role = $_POST['role'] ?? '';
if (!in_array($role, $allowed_roles)) redirectWithError("Invalid role selected.");

$allowed_ages = [2, 3, 4, 5];
$age = intval($_POST['age'] ?? 0);
if (!in_array($age, $allowed_ages)) redirectWithError("Invalid age selected.");

$allowed_docs = ['birth_certificate', 'school_id'];
$document_type = $_POST['document_type'] ?? '';
if (!in_array($document_type, $allowed_docs)) redirectWithError("Invalid document type.");

// --- 2. DATABASE CONNECTION ---
require 'db_config.php';
// Use the $mysqli_conn object created in the included file
$conn = $mysqli_conn;

// --- 3. CHECK FOR DUPLICATES ---
$stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
$stmt->bind_param("ss", $username, $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    redirectWithError("A user with that username or email already exists.");
}
$stmt->close();

// --- 4. HANDLE FILE UPLOAD ---
if (!isset($_FILES['certificate']) || $_FILES['certificate']['error'] != 0) {
     redirectWithError("Certificate upload is required. Please try again.");
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime_type = $finfo->file($_FILES['certificate']['tmp_name']);
$allowed_mimes = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'application/pdf' => 'pdf'
];

if (!array_key_exists($mime_type, $allowed_mimes)) {
    redirectWithError("Invalid file type. Only JPG, PNG, and PDF are allowed.");
}

$upload_dir = 'uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$file_extension = $allowed_mimes[$mime_type];
$unique_filename = uniqid('cert_', true) . '.' . $file_extension;
$target_file_path = $upload_dir . $unique_filename;

if (!move_uploaded_file($_FILES['certificate']['tmp_name'], $target_file_path)) {
     redirectWithError("Sorry, there was an error uploading your certificate.");
}

// --- 5. INSERT DATA ---
$conn->begin_transaction();

try {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $default_status = 'pending';
    
    $stmt1 = $conn->prepare("INSERT INTO users (username, email, password, role, age, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt1->bind_param("ssssis", $username, $email, $hashed_password, $role, $age, $default_status);
    $stmt1->execute();
    $new_user_id = $stmt1->insert_id;
    $stmt1->close();
    
    $stmt2 = $conn->prepare("INSERT INTO certificates (user_id, file_path, document_type) VALUES (?, ?, ?)");
    $stmt2->bind_param("iss", $new_user_id, $target_file_path, $document_type);
    $stmt2->execute();
    $stmt2->close();
    
    $conn->commit();
    
    // CHANGED: Redirect to login page after success
    header("Location: login.php?registration=success");
    exit();

} catch (Exception $exception) {
    $conn->rollback();
    if (file_exists($target_file_path)) { unlink($target_file_path); }
    redirectWithError("A system error occurred. Please try again later.");
}

$conn->close();
?>