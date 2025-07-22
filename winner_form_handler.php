<?php
// winner_form_handler.php
// This script handles AJAX requests for adding, updating, and deleting winner records.

session_start();
require_once 'config.php'; 

header('Content-Type: application/json');

// --- DEBUGGING LINE ---
// This line will output what PHP sees as the request method.
// You might see it in your browser's network tab response, or if you access directly.
error_log("Request Method received by winner_form_handler.php: " . $_SERVER['REQUEST_METHOD']);
// --- END DEBUGGING LINE ---

$response = ['success' => false, 'message' => 'An unknown error occurred.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- Handle DELETE action ---
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id_to_delete = (int)($_POST['id'] ?? 0);

        if ($id_to_delete > 0) {
            // First, retrieve the image path to delete the associated file from the server
            $stmt = $conn->prepare("SELECT image_path FROM frontend_winners WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param("i", $id_to_delete);
                $stmt->execute();
                $result = $stmt->get_result();
                $winner_data = $result->fetch_assoc();
                $stmt->close();

                if ($winner_data && !empty($winner_data['image_path'])) {
                    $image_file = 'uploads/' . basename($winner_data['image_path']); 
                    if (file_exists($image_file)) {
                        unlink($image_file);
                    }
                }

                $stmt = $conn->prepare("DELETE FROM frontend_winners WHERE id = ?");
                if ($stmt) {
                    $stmt->bind_param("i", $id_to_delete);
                    if ($stmt->execute()) {
                        $response = ['success' => true, 'message' => 'Winner deleted successfully!'];
                    } else {
                        $response = ['success' => false, 'message' => 'Error deleting winner: ' . $stmt->error];
                    }
                    $stmt->close();
                } else {
                     $response = ['success' => false, 'message' => 'Failed to prepare delete statement: ' . $conn->error];
                }
            } else {
                $response = ['success' => false, 'message' => 'Failed to prepare image path retrieval statement: ' . $conn->error];
            }
        } else {
            $response = ['success' => false, 'message' => 'Invalid winner ID for deletion.'];
        }
    }
    // --- Handle ADD/UPDATE action ---
    else {
        $winner_id = (int)($_POST['id'] ?? 0);
        $winner_name = trim($_POST['winner_name'] ?? '');
        $winner_location = trim($_POST['winner_location'] ?? '');
        $winning_code = trim($_POST['winning_code'] ?? '');
        $winner_fblink = trim($_POST['winner_fblink'] ?? '');
        $winner_status = trim($_POST['winner_status'] ?? 'pending');
        $winning_amount = (float)($_POST['winning_amount'] ?? 0.0);
        $winner_paymentfee = (float)($_POST['winner_paymentfee'] ?? 0.0);
        $currency = trim($_POST['currency'] ?? 'USD');
        $current_image_path = trim($_POST['current_image_path'] ?? '');

        $image_path = $current_image_path; 

        if (empty($winner_name) || empty($winning_code) || $winning_amount <= 0 || $winner_paymentfee < 0) {
            $response = ['success' => false, 'message' => 'Please fill all required fields (Winner Name, Winning Code, Winning Amount, Payment Fee) and ensure amounts are valid.'];
            echo json_encode($response);
            close_db_connection($conn);
            exit;
        }

        if (isset($_FILES['winner_image']) && $_FILES['winner_image']['error'] === UPLOAD_ERR_OK) {
            $target_dir = "uploads/"; 
            $image_file_type = strtolower(pathinfo($_FILES['winner_image']['name'], PATHINFO_EXTENSION));
            $unique_file_name = uniqid('winner_', true) . '.' . $image_file_type;
            $target_file = $target_dir . $unique_file_name;
            
            $uploadOk = 1;

            $check = getimagesize($_FILES["winner_image"]["tmp_name"]);
            if($check === false) { $response = ['success' => false, 'message' => "File is not an image."]; $uploadOk = 0; }
            if ($_FILES["winner_image"]["size"] > 5 * 1024 * 1024) { $response = ['success' => false, 'message' => "Sorry, your file is too large. Max 5MB."]; $uploadOk = 0; }
            $allowed_types = ['jpg', 'png', 'jpeg', 'gif'];
            if (!in_array($image_file_type, $allowed_types)) { $response = ['success' => false, 'message' => "Sorry, only JPG, JPEG, PNG & GIF files are allowed."]; $uploadOk = 0; }

            if ($uploadOk == 0) {
                $response['message'] = "Sorry, your file was not uploaded. " . $response['message'];
            } else {
                if (move_uploaded_file($_FILES["winner_image"]["tmp_name"], $target_file)) {
                    $image_path = $unique_file_name;
                    if (!empty($current_image_path) && file_exists($target_dir . basename($current_image_path))) {
                         unlink($target_dir . basename($current_image_path));
                    }
                } else {
                    $response = ['success' => false, 'message' => "Sorry, there was an error uploading your file."];
                    $uploadOk = 0;
                }
            }
        } else if (isset($_POST['remove_image']) && $_POST['remove_image'] === '1') {
            if (!empty($current_image_path)) {
                $target_dir = "uploads/"; 
                if (file_exists($target_dir . basename($current_image_path))) {
                    unlink($target_dir . basename($current_image_path));
                }
            }
            $image_path = null;
        }

        if (!isset($response['success']) || $response['success'] !== false || (isset($uploadOk) && $uploadOk === 1)) {
            if ($winner_id > 0) {
                $stmt = $conn->prepare("UPDATE frontend_winners SET winner_name = ?, winner_location = ?, winning_code = ?, winner_fblink = ?, winner_status = ?, winning_amount = ?, winner_paymentfee = ?, currency = ?, image_path = ? WHERE id = ?");
                if ($stmt) {
                    $stmt->bind_param("ssssssdssi", 
                        $winner_name, $winner_location, $winning_code, $winner_fblink, $winner_status,
                        $winning_amount, $winner_paymentfee, $currency, $image_path, $winner_id);
                    if ($stmt->execute()) {
                        $response = ['success' => true, 'message' => 'Winner updated successfully!'];
                    } else {
                        $response = ['success' => false, 'message' => 'Error updating winner: ' . $stmt->error];
                    }
                    $stmt->close();
                } else {
                    $response = ['success' => false, 'message' => 'Failed to prepare update statement: ' . $conn->error];
                }
            } else {
                $stmt = $conn->prepare("INSERT INTO frontend_winners (winner_name, winner_location, winning_code, winner_fblink, winner_status, winning_amount, winner_paymentfee, currency, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param("ssssssdsd", 
                        $winner_name, $winner_location, $winning_code, $winner_fblink, $winner_status,
                        $winning_amount, $winner_paymentfee, $currency, $image_path);
                    if ($stmt->execute()) {
                        $response = ['success' => true, 'message' => 'New winner added successfully!'];
                    } else {
                        $response = ['success' => false, 'message' => 'Error adding winner: ' . $stmt->error];
                    }
                    $stmt->close();
                } else {
                    $response = ['success' => false, 'message' => 'Failed to prepare insert statement: ' . $conn->error];
                }
            }
        }
    }
} else {
    $response = ['success' => false, 'message' => 'Invalid request method.'];
}

echo json_encode($response);
close_db_connection($conn);