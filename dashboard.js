// dashboard.js

// Get references to DOM elements
const dashboardContent = document.getElementById('dashboardContent');
const mainContentHeader = document.getElementById('mainContentHeader');
const sidebarItems = document.querySelectorAll('.sidebar-item[data-page]');
const loadingOverlay = document.getElementById('loadingOverlay');

// --- START: Sidebar Toggle Elements (from previous discussion) ---
// These elements need to exist in your dashboard.php HTML with these IDs
const sidebar = document.getElementById('sidebar');
const sidebarToggle = document.getElementById('sidebarToggle');
const mainContent = document.getElementById('mainContent');

// Create the overlay element for mobile sidebar
let overlay = document.createElement('div');
overlay.classList.add('overlay'); // This class is defined in dashboard.css
// --- END: Sidebar Toggle Elements ---


// Function to show/hide loading overlay
function showLoading() {
    loadingOverlay.classList.add('visible');
}

function hideLoading() {
    loadingOverlay.classList.remove('visible');
}

// Function to display messages in a consistent way
function displayMessage(message, type) {
    let messageHtml = `
        <div class="px-4 py-3 rounded relative mb-4 ${type === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'}" role="alert">
            <strong class="font-bold">${type.charAt(0).toUpperCase() + type.slice(1)}!</strong>
            <span class="block sm:inline">${message}</span>
        </div>
    `;
    // Find where to insert the message. For forms, directly at the top of the form content.
    // For general dashboard messages, insert before dashboardContent.
    const messageContainer = document.querySelector('.form-message-container') || dashboardContent;
    if (messageContainer) {
        // Clear existing messages first if they are in a dedicated container
        const existingMessages = messageContainer.querySelectorAll('[role="alert"]');
        existingMessages.forEach(msg => msg.remove());
        messageContainer.insertAdjacentHTML('afterbegin', messageHtml);

        // Auto-remove messages after a few seconds
        setTimeout(() => {
            const currentMessage = messageContainer.querySelector('[role="alert"]');
            if (currentMessage) {
                currentMessage.remove();
            }
        }, 5000); // Message disappears after 5 seconds
    }
}

// Function to load content dynamically via AJAX
async function loadContent(page, params = {}) {
    showLoading();
    try {
        // Construct query string from params
        const queryString = new URLSearchParams(params).toString();
        // All dynamic content PHP files are now in the root directory
        const url = `${page}.php${queryString ? '?' + queryString : ''}`;

        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const htmlContent = await response.text();
        dashboardContent.innerHTML = htmlContent;

        // Update header based on loaded page
        if (page === 'winner_form_content') {
            const formTitle = document.querySelector('#dashboardContent h3');
            if (formTitle) {
                mainContentHeader.textContent = formTitle.textContent;
            } else {
                mainContentHeader.textContent = 'Winner Form'; // Fallback if no specific title found
            }
        } else if (page === 'view_winners_content') {
            mainContentHeader.textContent = 'View & Edit Winners';
        } else {
            mainContentHeader.textContent = 'Admin Dashboard'; // Default
        }

        // After content is loaded, re-attach event listeners for forms, delete, and EDIT buttons
        attachFormListeners();
        attachDeleteListeners();
        attachEditButtonListeners();

    } catch (error) {
        console.error('Error loading content:', error);
        dashboardContent.innerHTML = `<p class="text-red-500">Failed to load content: ${error.message}</p>`;
        displayMessage(`Failed to load content: ${error.message}`, 'error');
    } finally {
        hideLoading();
    }
}

// Function to attach listeners to the winner form (for AJAX submission)
function attachFormListeners() {
    const winnerForm = dashboardContent.querySelector('#winnerForm'); // Assuming the form will have this ID
    if (winnerForm) {
        // Remove any existing listeners to prevent duplicate submissions if content is reloaded
        winnerForm.removeEventListener('submit', handleWinnerFormSubmit);
        // Add the new listener
        winnerForm.addEventListener('submit', handleWinnerFormSubmit);
    }
}

// Handler function for winner form submission
async function handleWinnerFormSubmit(event) {
    event.preventDefault(); // Prevent default form submission

    showLoading();
    const formData = new FormData(this); // 'this' refers to the form element

    // Log the FormData content before sending for debugging
    console.log("Attempting to submit form with FormData:");
    for (const pair of formData.entries()) {
        console.log(`${pair[0]}: ${pair[1]}`);
    }

    try {
        // The handler is now in the root folder, so the path is directly 'winner_form_handler.php'
        const response = await fetch('winner_form_handler.php', {
            method: 'POST',
            body: formData // FormData handles multipart/form-data for files
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json(); // Expect JSON response

        if (result.success) {
            displayMessage(result.message, 'success');
            // If adding a new winner, clear/reset the form
            if (formData.get('id') === '0' || formData.get('id') === '') { // Check if it was an add operation
                this.reset(); // 'this' refers to the form element
                // Reset image preview if it was there
                const currentImageDisplay = this.querySelector('#currentImageDisplay');
                if (currentImageDisplay) currentImageDisplay.innerHTML = '';
                // Reload the form content to ensure correct state (e.g., no 'Cancel Edit' button)
                loadContent('winner_form_content');
            } else {
                // If editing, can reload the form to show updated data or just display message
                loadContent('winner_form_content', { action: 'edit', id: formData.get('id') });
            }
        } else {
            displayMessage(result.message, 'error');
        }
    } catch (error) {
        console.error('Error submitting form:', error);
        displayMessage(`An error occurred: ${error.message}`, 'error');
    } finally {
        hideLoading();
    }
}


// Function to attach listeners to delete buttons
function attachDeleteListeners() {
    const deleteButtons = dashboardContent.querySelectorAll('.delete-winner-btn');
    deleteButtons.forEach(button => {
        // Remove existing listener to prevent duplicates if content is reloaded
        button.removeEventListener('click', confirmDeleteHandler);
        button.addEventListener('click', confirmDeleteHandler);
    });
}

// Handler for delete button click (used by attachDeleteListeners)
function confirmDeleteHandler(event) {
    const idToDelete = event.currentTarget.dataset.id; // Get ID from data attribute

    // Create the modal HTML
    const modalHtml = `
        <div id="deleteConfirmationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg shadow-xl w-96">
                <h4 class="text-xl font-semibold text-gray-800 mb-4">Confirm Deletion</h4>
                <p class="text-gray-700 mb-6">Are you sure you want to delete this winner record? This action cannot be undone.</p>
                <div class="flex justify-end space-x-4">
                    <button id="cancelDeleteBtn" class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-50">Cancel</button>
                    <button id="confirmDeleteBtn" class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700">Delete</button>
                </div>
            </div>
        </div>
    `;

    // Append the modal HTML to the document body
    document.body.insertAdjacentHTML('beforeend', modalHtml);

    // Get references to the modal and its buttons
    const modal = document.getElementById('deleteConfirmationModal');
    const cancelBtn = document.getElementById('cancelDeleteBtn');
    const confirmBtn = document.getElementById('confirmDeleteBtn');

    // Attach event listeners to the buttons
    cancelBtn.onclick = () => modal.remove(); // Remove modal on cancel
    confirmBtn.onclick = async () => {
        modal.remove(); // Remove modal
        showLoading();
        try {
            // The handler is now in the root folder, so the path is directly 'winner_form_handler.php'
            const response = await fetch('winner_form_handler.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, // For simple POST data
                body: `action=delete&id=${idToDelete}`
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();

            if (result.success) {
                displayMessage(result.message, 'success');
                // Reload the view winners content to reflect the deletion
                loadContent('view_winners_content');
            } else {
                displayMessage(result.message, 'error');
            }
        } catch (error) {
            console.error('Error deleting winner:', error);
            displayMessage(`An error occurred during deletion: ${error.message}`, 'error');
        } finally {
            hideLoading();
        }
    };
}

// Function to attach listeners to the edit buttons (for loading the form in edit mode)
function attachEditButtonListeners() {
    const editButtons = dashboardContent.querySelectorAll('.edit-winner-btn');
    editButtons.forEach(button => {
        // Remove any existing listeners to prevent duplicate submissions if content is reloaded
        button.removeEventListener('click', handleEditButtonClick);
        // Add the new listener
        button.addEventListener('click', handleEditButtonClick);
    });
}

// Handler for edit button click
function handleEditButtonClick(event) {
    event.preventDefault(); // Prevent default link behavior
    const winnerId = this.dataset.id; // Get ID from data attribute
    loadContent('winner_form_content', { action: 'edit', id: winnerId });
}


// Event listeners for sidebar navigation
sidebarItems.forEach(item => {
    item.addEventListener('click', function(event) {
        event.preventDefault(); // Prevent default link behavior

        // Remove 'active' class from all sidebar items
        sidebarItems.forEach(i => i.classList.remove('active'));
        // Add 'active' class to the clicked item
        this.classList.add('active');

        // Get the data-page attribute to determine which content to load
        const pageToLoad = this.dataset.page;

        // Check for edit action if coming from view_winners_content after an edit click
        const urlParams = new URLSearchParams(window.location.search);
        let params = {};
        if (urlParams.has('action') && urlParams.get('action') === 'edit' && urlParams.has('id')) {
            params = { action: 'edit', id: urlParams.get('id') };
            // Clear URL parameters after use to prevent issues on subsequent loads
            history.replaceState(null, '', window.location.pathname);
        }

        loadContent(pageToLoad, params);

        // --- START: Sidebar Toggle on Item Click (for mobile) ---
        // Hide sidebar and overlay when a sidebar item is clicked (useful on mobile)
        if (window.innerWidth <= 768 && sidebar.classList.contains('visible')) {
            sidebar.classList.remove('visible');
            overlay.classList.remove('visible');
            document.body.classList.remove('no-scroll');
        }
        // --- END: Sidebar Toggle on Item Click ---
    });
});

// Initial content load and Sidebar Toggle Initialization when the dashboard first loads
document.addEventListener('DOMContentLoaded', () => {
    // --- START: Sidebar Toggle Initialization (from previous discussion) ---
    // Append overlay to body
    document.body.appendChild(overlay);

    // Initial state setup for larger screens
    if (window.innerWidth > 768) {
        sidebar.classList.remove('hidden-sidebar', 'visible');
        mainContent.classList.remove('sidebar-hidden');
        sidebarToggle.classList.add('hidden'); // Hide toggle button on desktop
        overlay.classList.remove('visible');
    } else {
        sidebar.classList.add('hidden-sidebar'); // Hide sidebar by default on mobile
        // sidebar.classList.remove('visible'); // Ensure it's not showing (already handled by hidden-sidebar initially)
        sidebarToggle.classList.remove('hidden'); // Show toggle button on mobile
    }

    // Toggle sidebar on button click
    sidebarToggle.addEventListener('click', () => {
        if (window.innerWidth <= 768) {
            // Mobile view: slide in/out sidebar and show/hide overlay
            sidebar.classList.toggle('visible');
            overlay.classList.toggle('visible');
            // Prevent body scroll when sidebar is open
            document.body.classList.toggle('no-scroll', sidebar.classList.contains('visible'));
        } else {
            // Desktop view: hide/show sidebar and adjust main content margin
            sidebar.classList.toggle('hidden-sidebar');
            mainContent.classList.toggle('sidebar-hidden');
        }
    });

    // Hide sidebar and overlay when clicking outside (on overlay)
    overlay.addEventListener('click', () => {
        if (sidebar.classList.contains('visible')) {
            sidebar.classList.remove('visible');
            overlay.classList.remove('visible');
            document.body.classList.remove('no-scroll');
        }
    });

    // Handle window resize for responsive behavior
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) {
            // Desktop view
            sidebar.classList.remove('hidden-sidebar', 'visible'); // Ensure sidebar is visible
            mainContent.classList.remove('sidebar-hidden'); // Ensure main content has margin
            sidebarToggle.classList.add('hidden'); // Hide toggle button
            overlay.classList.remove('visible'); // Hide overlay
            document.body.classList.remove('no-scroll');
        } else {
            // Mobile view
            sidebar.classList.add('hidden-sidebar'); // Sidebar hidden by default on mobile
            sidebar.classList.remove('visible'); // Ensure it's not showing
            sidebarToggle.classList.remove('hidden'); // Show toggle button
            overlay.classList.remove('visible'); // Hide overlay
            document.body.classList.remove('no-scroll');
        }
    });
    // --- END: Sidebar Toggle Initialization ---


    // Default to 'winner_form_content' as specified
    let initialPage = 'winner_form_content';
    let initialParams = {};

    // Still check URL params if a specific page was requested externally (e.g., direct link with ?page=view_winners)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('page')) {
        const pageFromUrl = urlParams.get('page');
        if (pageFromUrl === 'winner_form' || pageFromUrl === 'winner_form_content') {
            initialPage = 'winner_form_content';
            if (urlParams.has('action') && urlParams.get('action') === 'edit' && urlParams.get('id')) {
                initialParams = { action: 'edit', id: urlParams.get('id') };
            }
        } else if (pageFromUrl === 'view_winners' || pageFromUrl === 'view_winners_content') {
            initialPage = 'view_winners_content';
        }
    }

    // Set the active sidebar item based on the initial page
    sidebarItems.forEach(item => {
        if (item.dataset.page === initialPage) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });

    loadContent(initialPage, initialParams);
    console.log("Dashboard JavaScript Initialized!");
});