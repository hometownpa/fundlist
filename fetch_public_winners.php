<?php
// fetch_public_winners.php
// This script fetches winner data for the public frontend.
// It can fetch all winners for the table, or a specific winner for search results.

header('Content-Type: application/json'); // Ensure the response is JSON

// Path to config.php (assuming it's in the same directory as this script)
require_once 'config.php';

$response = [
    'success' => false,
    'message' => 'An unknown error occurred.',
    'winners' => [], // For fetching all
    'winner' => null  // For single search result
];

try {
    // Check if a search query is provided
    if (isset($_GET['query']) && !empty(trim($_GET['query']))) {
        $search_query = trim($_GET['query']);

        // Prepare a statement to search by winner_name or winning_code
        // Using `LIKE` for partial matches, `%` is the wildcard.
        // It's good practice to bind parameters for LIKE too.
        $stmt = $conn->prepare("SELECT winner_name, winner_location, winning_code, winner_fblink, winner_status, winning_amount, winner_paymentfee, currency, image_path FROM frontend_winners WHERE winner_name LIKE ? OR winning_code LIKE ? LIMIT 1");

        if (!$stmt) {
            throw new Exception("Database prepare failed: " . $conn->error);
        }

        $search_param = '%' . $search_query . '%';
        $stmt->bind_param("ss", $search_param, $search_param);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $winner_data = $result->fetch_assoc();

            // *** CORRECTION APPLIED HERE ***
            // If image_path in DB is just "filename.jpeg" and 'uploads/' is the subfolder:
            $winner_data['image_url'] = !empty($winner_data['image_path']) ? 'uploads/' . $winner_data['image_path'] : '';

            // Example of what it creates:
            // If winner_data['image_path'] is "my_winner.jpg"
            // Then winner_data['image_url'] becomes "uploads/my_winner.jpg"
            // This is the correct relative path for the browser.

            $response['success'] = true;
            $response['message'] = 'Winner found.';
            $response['winner'] = $winner_data; // Single winner object
        } else {
            $response['message'] = 'No winner found matching your criteria.';
        }
        $stmt->close();

    } else {
        // No search query, fetch all winners for the main table
        // Order by latest winners first
        $stmt = $conn->prepare("SELECT winner_name, winner_location, winning_code, winner_fblink, winner_status, winning_amount, winner_paymentfee, currency FROM frontend_winners ORDER BY id DESC");

        if (!$stmt) {
            throw new Exception("Database prepare failed: " . $conn->error);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $winners = [];
            while ($row = $result->fetch_assoc()) {
                // Do NOT include image_path here for the main table view
                $winners[] = $row;
            }
            $response['success'] = true;
            $response['message'] = 'Winners fetched successfully.';
            $response['winners'] = $winners; // Array of winners for the table
        } else {
            $response['message'] = 'No winners currently available.';
        }
        $stmt->close();
    }

} catch (Exception $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
    // Log the error for debugging, but don't expose sensitive info to the user
    error_log('Frontend Winner Fetch Error: ' . $e->getMessage());
} finally {
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}

echo json_encode($response);