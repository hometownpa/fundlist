<?php
// winner_form_content.php
// This file provides the HTML for the winner form, to be loaded dynamically via AJAX.
// It retrieves existing data if an 'id' is provided for editing.

// Ensure config.php is included (adjust path if needed)
// Path is now relative to the parent folder, so no '../' needed
if (!isset($conn)) {
    require_once 'config.php';
}

// Initialize form fields for add/edit operations
$winner_id = 0; // Use 0 for new entry
$winner_name = '';
$winner_location = '';
$winning_code = '';
$winner_fblink = '';
$winner_status = 'pending'; // Default status
$winning_amount = '';
$winner_paymentfee = '';
$currency = 'USD'; // Default currency
$image_path = ''; // To store the path of an existing image

// --- Handle EDIT form pre-fill ---
// If 'action' is 'edit' and an 'id' is provided, fetch existing winner data to pre-fill the form
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $winner_id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM frontend_winners WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $winner_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $winner_data = $result->fetch_assoc();
            // Sanitize data for output
            $winner_name = htmlspecialchars($winner_data['winner_name'] ?? '');
            $winner_location = htmlspecialchars($winner_data['winner_location'] ?? '');
            $winning_code = htmlspecialchars($winner_data['winning_code'] ?? '');
            $winner_fblink = htmlspecialchars($winner_data['winner_fblink'] ?? '');
            $winner_status = htmlspecialchars($winner_data['winner_status'] ?? 'pending');
            $winning_amount = htmlspecialchars($winner_data['winning_amount']);
            $winner_paymentfee = htmlspecialchars($winner_data['winner_paymentfee']);
            $currency = htmlspecialchars($winner_data['currency']);
            $image_path = htmlspecialchars($winner_data['image_path'] ?? ''); // This should be the full relative path from DB
        } else {
            // If winner not found, reset ID to 0 for new entry, and show message (handled by JS)
            $winner_id = 0;
            // Message would be handled by JS upon AJAX call completion.
        }
        $stmt->close();
    } else {
        // Handle prepare error
        $winner_id = 0; // Treat as new entry
    }
}
?>

<div class="container mx-auto p-4">
    <h3 class="text-2xl font-semibold text-gray-800 mb-6">
        <?php echo ($winner_id > 0) ? 'Edit Winner Details' : 'Add New Winner'; ?>
    </h3>

    <div class="form-message-container"></div>

    <form id="winnerForm" method="POST" enctype="multipart/form-data" class="space-y-6">
        <input type="hidden" name="id" value="<?php echo $winner_id; ?>">
        <input type="hidden" name="current_image_path" value="<?php echo $image_path; ?>">

        <div>
            <label for="winner_name" class="block text-sm font-medium text-gray-700 mb-2">Winner Name</label>
            <input type="text" id="winner_name" name="winner_name" value="<?php echo $winner_name; ?>" required
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>

        <div>
            <label for="winner_location" class="block text-sm font-medium text-gray-700 mb-2">Winner Location (Optional)</label>
            <input type="text" id="winner_location" name="winner_location" value="<?php echo $winner_location; ?>"
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>

        <div>
            <label for="winning_code" class="block text-sm font-medium text-gray-700 mb-2">Winning Code</label>
            <input type="text" id="winning_code" name="winning_code" value="<?php echo $winning_code; ?>" required
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>

        <div>
            <label for="winner_fblink" class="block text-sm font-medium text-gray-700 mb-2">Winner Facebook Link (Optional)</label>
            <input type="url" id="winner_fblink" name="winner_fblink" value="<?php echo $winner_fblink; ?>"
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>

        <div>
            <label for="winner_status" class="block text-sm font-medium text-gray-700 mb-2">Winner Status</label>
            <select id="winner_status" name="winner_status"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <option value="pending" <?php echo ($winner_status === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                <option value="not yet claimed" <?php echo ($winner_status === 'Not Yet Claimed') ? 'selected' : ''; ?>>Not Yet Claimed</option>
                <option value="claimed" <?php echo ($winner_status === 'Claimed') ? 'selected' : ''; ?>>Claimed</option>
                <option value="processing" <?php echo ($winner_status === 'Processing') ? 'selected' : ''; ?>>Processing</option>
                <option value="rejected" <?php echo ($winner_status === 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                <option value="delivered" <?php echo ($winner_status === 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
                </select>
        </div>

        <div>
            <label for="winning_amount" class="block text-sm font-medium text-gray-700 mb-2">Winning Amount</label>
            <input type="number" id="winning_amount" name="winning_amount" step="0.01" value="<?php echo $winning_amount; ?>" required
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>

        <div>
            <label for="winner_paymentfee" class="block text-sm font-medium text-gray-700 mb-2">Payment Fee</label>
            <input type="number" id="winner_paymentfee" name="winner_paymentfee" step="0.01" value="<?php echo $winner_paymentfee; ?>" required
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>

        <div>
            <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
            <select id="currency" name="currency"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <option value="USD" <?php echo ($currency === 'USD') ? 'selected' : ''; ?>>USD</option>
                <option value="EUR" <?php echo ($currency === 'EUR') ? 'selected' : ''; ?>>EUR</option>
                <option value="GBP" <?php echo ($currency === 'GBP') ? 'selected' : ''; ?>>GBP</option>
                <option value="AUD" <?php echo ($currency === 'AUD') ? 'selected' : ''; ?>>AUD</option>
                <option value="CNY" <?php echo ($currency === 'CNY') ? 'selected' : ''; ?>>CNY</option>
                <option value="CAD" <?php echo ($currency === 'CAD') ? 'selected' : ''; ?>>CAD</option>
                <option value="NZD" <?php echo ($currency === 'NZD') ? 'selected' : ''; ?>>NZD</option>
                <option value="CHF" <?php echo ($currency === 'CHF') ? 'selected' : ''; ?>>SWISS FRANC</option>
            </select>
        </div>

        <div class="flex items-center space-x-4">
            <label for="winner_image" class="block text-sm font-medium text-gray-700">Winner Image (Optional)</label>
            <input type="file" id="winner_image" name="winner_image" accept="image/*"
                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
        </div>

        <?php if (!empty($image_path)): // Only display if an image path exists ?>
            <div id="currentImageDisplay" class="mt-4">
                <p class="block text-sm font-medium text-gray-700">Current Image:</p>
                <div class="flex items-center space-x-4 mt-2">
                    <img src="<?php echo htmlspecialchars($image_path); ?>" alt="Winner Image" class="w-32 h-32 object-cover rounded-md border border-gray-200">
                    <label for="remove_image" class="inline-flex items-center text-red-600">
                        <input type="checkbox" id="remove_image" name="remove_image" value="1" class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm font-medium">Remove Current Image</span>
                    </label>
                </div>
            </div>
        <?php endif; ?>

        <div class="flex space-x-4">
            <button type="submit"
                    class="flex-1 justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-lg font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">
                <?php echo ($winner_id > 0) ? 'Update Winner' : 'Add Winner'; ?>
            </button>
            <?php if ($winner_id > 0): ?>
                <a href="#" data-page="winner_form_content" class="flex-1 text-center justify-center py-3 px-4 border border-gray-300 rounded-md shadow-sm text-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out" onclick="event.preventDefault(); loadContent('winner_form_content');">
                    Cancel Edit
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>