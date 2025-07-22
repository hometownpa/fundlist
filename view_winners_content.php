<?php
// view_winners_content.php
if (!isset($conn)) {
    require_once 'config.php';
}

// Fetch all winners from the database
$winners = [];
// Select all relevant columns from the updated table structure, excluding 'created_at'
$sql = "SELECT id, winner_name, winner_location, winning_code, winner_fblink, winner_status, winning_amount, winner_paymentfee, currency, image_path FROM frontend_winners ORDER BY id DESC"; // ORDER BY id DESC as created_at is removed
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $winners[] = $row;
    }
    $result->free(); // Free result set
} else {
    // Error handling would be propagated via JS displayMessage
    error_log("Error fetching winners in view_winners_content.php: " . $conn->error);
}

?>

<div class="container mx-auto p-4">
    <h3 class="text-2xl font-semibold text-gray-800 mb-6">View & Edit Winners</h3>

    <div class="form-message-container"></div>

    <?php if (empty($winners)): ?>
        <p class="text-gray-600">No winners found. Add a new winner using the "Winner Form" in the sidebar.</p>
    <?php else: ?>
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            ID
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Image
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Winner Name
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Location
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Winning Code
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            FB Link
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Winning Amount
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Payment Fee
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Currency
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($winners as $winner): ?>
                        <tr>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($winner['id']); ?></p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <?php if (!empty($winner['image_path'])): ?>
                                    <img src="uploads/<?php echo basename(htmlspecialchars($winner['image_path'])); ?>" alt="Winner Image" class="w-16 h-16 object-cover rounded-md">
                                <?php else: ?>
                                    <span class="text-gray-400">No Image</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($winner['winner_name'] ?? 'N/A'); ?></p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($winner['winner_location'] ?? 'N/A'); ?></p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($winner['winning_code'] ?? 'N/A'); ?></p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <?php if (!empty($winner['winner_fblink'])): ?>
                                    <a href="<?php echo htmlspecialchars($winner['winner_fblink']); ?>" target="_blank" class="text-blue-500 hover:underline">Link</a>
                                <?php else: ?>
                                    <span class="text-gray-400">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <span class="relative inline-block px-3 py-1 font-semibold leading-tight">
                                    <span aria-hidden="true" class="absolute inset-0 <?php
                                        // Tailwind CSS classes provided for different statuses
                                        switch ($winner['winner_status']) {
                                            case 'Delivered': echo 'bg-green-200 text-green-800'; break;
                                            case 'Claimed': echo 'bg-blue-200 text-blue-800'; break;
                                            case 'Not Yet Claimed': echo 'bg-red-200 text-red-800'; break;
                                            case 'Rejected': echo 'bg-red-200 text-red-800'; break;
                                            case 'Processing': echo 'bg-yellow-200 text-yellow-800'; break;
                                            case 'Pending': default: echo 'bg-gray-200 text-gray-800'; break;
                                        }
                                    ?> opacity-50 rounded-full"></span>
                                    <span class="relative text-gray-900"><?php echo htmlspecialchars(ucfirst($winner['winner_status'])); ?></span>
                                </span>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars(number_format($winner['winning_amount'], 2)); ?></p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars(number_format($winner['winner_paymentfee'], 2)); ?></p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($winner['currency']); ?></p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <div class="flex space-x-2">
                                    <a href="#" class="text-blue-600 hover:text-blue-900 font-semibold edit-winner-btn" data-id="<?php echo htmlspecialchars($winner['id']); ?>">
                                        View & Update
                                    </a>
                                    <button type="button" class="text-red-600 hover:text-red-900 font-semibold cursor-pointer focus:outline-none delete-winner-btn" data-id="<?php echo htmlspecialchars($winner['id']); ?>">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
// Attach edit button listeners dynamically here, as this content is loaded via AJAX.
// The main dashboard.js file will call attachDeleteListeners() after loading this content.
document.querySelectorAll('.edit-winner-btn').forEach(button => {
    button.addEventListener('click', function(event) {
        event.preventDefault();
        const winnerId = this.dataset.id;
        // Trigger the loadContent function from dashboard.js with edit action and ID
        loadContent('winner_form_content', { action: 'edit', id: winnerId });
    });
});
</script>