<?php
session_start();

// Assuming config.php is in the same directory as dashboard.php
require_once 'config.php';

// Check if admin is logged in, otherwise redirect to login page
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body class="flex">
    <div id="loadingOverlay" class="loading-overlay">
        <div class="spinner"></div>
    </div>

    <aside class="sidebar shadow-lg" id="sidebar">
        <div class="px-8 mb-8">
            <h1 class="text-3xl font-bold text-blue-400">Admin Panel</h1>
        </div>
        <nav class="flex-grow">
            <ul>
                <li>
                    <a href="#" data-page="winner_form_content" class="sidebar-item active">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        Winner Form
                    </a>
                </li>
                <li>
                    <a href="#" data-page="view_winners_content" class="sidebar-item">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        View & Edit Winners
                    </a>
                </li>
            </ul>
        </nav>
        <div class="logout-btn">
            <a href="logout.php" class="sidebar-item text-red-300 hover:bg-red-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H5a3 3 0 01-3-3V7a3 3 0 013-3h5a3 3 0 013 3v1" />
                </svg>
                Log Out
            </a>
        </div>
    </aside>

    <div class="main-content" id="mainContent">
        <button id="sidebarToggle" class="fixed top-4 left-4 z-50 p-2 rounded-full bg-blue-600 text-white shadow-lg md:hidden">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <header class="bg-white shadow-md p-6 rounded-lg mb-6 flex justify-between items-center">
            <h2 id="mainContentHeader" class="text-3xl font-bold text-gray-800">
                Winner Form
            </h2>
            <div class="text-gray-600">
                Logged in as: <span class="font-semibold text-gray-800"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
            </div>
        </header>

        <div id="dashboardContent" class="bg-white p-8 rounded-lg shadow-md min-h-[60vh]">
            </div>
    </div>

    <script src="dashboard.js"></script>
</body>
</html>