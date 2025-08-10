<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>THE GLOBAL FUND</title>
    <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="main-header">
        <div class="header-content">
            <div class="logo-section">
                <a href="#" class="logo-link">
                    <img src="https://i.imgur.com/EQpOKyF.png" alt="The Global Fund" class="pch-logo-img">
                </a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="form.html">APPLY</a></li>
                    <li><a href="login.php">LOGIN</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <section class="hero-section">
            <div class="container">
                <div class="search-box-container">
                    <input type="text" id="searchInput" class="hero-search-input" placeholder="Search for winnings by Name or Code...">
                    <button id="searchButton" class="hero-search-button">Check Name</button>
                </div>
            </div>
        </section>

        <section class="pch-mission-section">
            <div class="container">
                <h3>Unlock Your Potential: The Global Fund Offers Grants You Never Repay!</h3>
                <p>At The Global Fund, our core mission is simple yet powerful: helping people thrive. We're dedicated to building stronger communities by offering a life-changing Grant Program designed to directly combat poverty and create real opportunity.</p>
                <p>This isn't a loan; it's free grant money. A Global Fund Grant provides direct financial support you'll never have to pay back. We empower individuals and communities by providing the resources they need to achieve lasting positive change.</p>

                <ul class="empowerment-list">
                    <li>Empowering Futures: The Global Fund's grants are building pathways out of poverty, one community at a time.</li>
                    <li>Beyond Charity: The Global Fund invests in thriving communities, creating lasting change through direct grants..</li>
                    <li>Transforming Lives: The Global Fund's grants break the cycle of poverty and ignite genuine opportunity..</li>
                    <li>Igniting Hope, Building Prosperity: The Global Fund delivers life-changing grants to uplift communities..</li>
                    <li>Your Impact, Their Opportunity: The Global Fund is forging stronger communities and a future free from poverty.</li>


                </ul>

                <p class="call-to-action">Dream big, we'll fund it: THE GLOBAL FUND Grants empower your potential with stress-free financial support.</p>

                <a href="form.html" class="apply-button">Click here to Apply Now</a>
            </div>
        </section>

        <section class="winners-list-section">
            <div class="container">
                <h2>Weekly Winners Selection</h2>
                <div id="customMessage" class="message-box-frontend" style="display: none;"></div>
                <div class="table-responsive">
                    <table class="winners-table">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 bg-dark brown-50 text-left text-xs font-small text-white-300 uppercase tracking-wider">Winner Name</th>
                                <th class="px-4 py-3 bg-dark brown-50 text-left text-xs font-medium text-white-300 uppercase tracking-wider">Winners Location</th>
                                <th class="px-4 py-3 bg-dark brown-50 text-left text-xs font-medium text-white-300 uppercase tracking-wider">Winning Code</th>
                                <th class="px-4 py-3 bg-dark brown-50 text-left text-xs font-medium text-white-300 uppercase tracking-wider">Winners FB Link</th>
                                <th class="px-4 py-3 bg-dark brown-50 text-left text-xs font-medium text-white-300 uppercase tracking-wider">Winners Status</th>
                                <th class="px-4 py-3 bg-dark brown-50 text-left text-xs font-medium text-white-300 uppercase tracking-wider">Winning Amount</th>
                                <th class="px-4 py-3 bg-dark brown-50 text-left text-xs font-medium text-white-300 uppercase tracking-wider">Payment Fee</th>
                            </tr>
                        </thead>
                        <tbody class="winners-table-tbody" id="winnersTableTbody">
                            <tr><td colspan="8" class="loading-row">Loading weekly winners...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section class="content-cards">
    <div class="card">
        <img src="https://i.imgur.com/S37uVB1.jpeg" alt="Official Claiming Agent Lawrence Wayne" class="card-image">
        <div class="card-text-content">
            <h4>OUR OFFICIAL CLAIMING AGENT</h4>
            <h3>JAMES RENTROP</h3>
            <p><strong>Guidance on the application process:</strong> If you're applying for a grant, they're there to answer your questions and help you navigate the necessary steps.</p>
            <p><strong>Assistance with claiming your winnings:</strong> Once you're a confirmed winner, our agent will walk you through the claim procedure, ensuring all documentation is correct and submitted efficiently.</p>
            <p><strong>Clarification on any program details:</strong> They possess in-depth knowledge of our grant and prize programs and can provide clear, accurate information.</p>
            <p><strong>Support and reassurance:</strong> They're committed to making your experience positive, addressing any concerns you might have with patience and professionalism.</p>
            <a href="mailto:ggf.agent.02488@gmail.com" target="_blank" class="contact-agent-button">Email Agent</a>
            <a href="tel:+1-555-123-4567" class="contact-agent-button">Call Agent</a>
        </div>
    </div>
</section>

            <div class="winners-card-section bg-white p-10 rounded-xl shadow-lg relative overflow-hidden">
                <div class="confetti">
                    <div class="confetti-piece"></div>
                    <div class="confetti-piece"></div>
                    <div class="confetti-piece"></div>
                    <div class="confetti-piece"></div>
                    <div class="confetti-piece"></div>
                    <div class="confetti-piece"></div>
                    <div class="confetti-piece"></div>
                    <div class="confetti-piece"></div>
                    <div class="confetti-piece"></div>
                </div>

                <h1 class="text-4xl font-bold text-gray-800 mb-6 text-center tracking-wide">LUCKIEST WINNERS</h1>

                <div class="flex space-x-2 mb-8 justify-center">
                    <button class="px-6 py-2 rounded-lg bg-blue-600 text-white font-semibold shadow-md hover:bg-blue-700 transition duration-300">RECENT WINNER</button>
                    <button class="px-6 py-2 rounded-lg bg-gray-200 text-gray-700 font-semibold shadow-md hover:bg-gray-300 transition duration-300">CLAIMED</button>
                </div>

                <div class="space-y-6">
                    <div class="flex items-center bg-gray-50 p-4 rounded-lg shadow-sm border border-gray-200">
                        <div class="flex-grow">
                            <p class="text-xl font-semibold text-gray-800">PAMELA DOUCET</p>
                            <p class="text-sm text-gray-600">Houston, Texas</p>
                            <p class="text-xs text-green-600 font-medium">Claimed</p>
                        </div>
                        <div class="text-1xl font-bold text-blue-600 mr-4">43,370.000</div>
                        <div class="w-20 h-20 flex-shrink-0 rounded-lg overflow-hidden border border-gray-300">
                            <img src="https://i.imgur.com/aiJoLTP.jpeg" alt="Gift Placeholder" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='https://placehold.co/96x96/ADD8E6/000000?text=Gift';">
                        </div>
                    </div>

                    <div class="flex items-center bg-gray-50 p-4 rounded-lg shadow-sm border border-gray-200">
                        <div class="flex-grow">
                            <p class="text-xl font-semibold text-gray-800">EDWARD HARGRAVE</p>
                            <p class="text-sm text-gray-600">Washington DC</p>
                            <p class="text-xs text-green-600 font-medium">Claimed</p>
                        </div>
                        <div class="text-1xl font-bold text-blue-600 mr-4">$1,000,000.00</div>
                        <div class="w-20 h-20 flex-shrink-0 rounded-lg overflow-hidden border border-gray-300">
                            <img src="images/randolph (2).jpeg" alt="Gift Placeholder" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='https://placehold.co/96x96/FFB6C1/000000?text=Gift';">
                        </div>
                    </div>

                    <div class="flex items-center bg-gray-50 p-4 rounded-lg shadow-sm border border-gray-200">
                        <div class="flex-grow">
                            <p class="text-xl font-semibold text-gray-800">VICKI GREIF</p>
                            <p class="text-sm text-gray-600">Atlanta, Georgia</p>
                            <p class="text-xs text-yellow-600 font-medium">Delivered</p>
                        </div>
                        <div class="text-1xl font-bold text-blue-600 mr-4">$25,000.00</div>
                        <div class="w-20 h-20 flex-shrink-0 rounded-lg overflow-hidden border border-gray-300">
                            <img src="https://i.imgur.com/thrwqTu.jpeg" alt="Gift Placeholder" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='https://placehold.co/96x96/FFFACD/000000?text=Gift';">
                        </div>
                    </div>

                    <div class="flex items-center bg-gray-50 p-4 rounded-lg shadow-sm border border-gray-200">
                        <div class="flex-grow">
                            <p class="text-xl font-semibold text-gray-800">ADREA KASS</p>
                            <p class="text-sm text-gray-600">Kansas City</p>
                            <p class="text-xs text-green-600 font-medium">Claimed</p>
                        </div>
                        <div class="text-1xl font-bold text-blue-600 mr-4">$1,000,000.00</div>
                        <div class="w-20 h-20 flex-shrink-0 rounded-lg overflow-hidden border border-gray-300">
                            <img src="https://i.imgur.com/LtFYUab.jpeg" alt="Gift Placeholder" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='https://placehold.co/96x96/DDA0DD/000000?text=Gift';">
                        </div>
                    </div>
                </div>
            </div>
            <div class="winner-list" id="claimed-list" style="display: none;">
                <p class="message-placeholder">Loading claimed winners...</p>
            </div>
        </section>
    </main>

    <div id="messageOverlay" class="message-overlay" style="display:none;">
        <div class="message-box">
            <p id="messageText"></p>
            <button id="messageOkButton">OK</button>
        </div>
    </div>

    <div id="winnerDetailsModal" class="message-overlay winner-details-modal" style="display:none;">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <div id="winnerDetailsContent" class="winner-details-container">
            </div>
        </div>
    </div>

    <footer class="main-footer">
        <div class="container footer-content">
            <div class="footer-section about-us">
                <h4>About The Global Fund</h4>
                <p>The Global Fund has been making dreams come true for decades, offering life-changing prizes and grants. We're committed to our mission of delivering excitement and opportunity.</p>
            </div>
            <div class="footer-section quick-links">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="form.html">Apply for Grant</a></li>
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                </ul>
            </div>
            <div class="footer-section contact-info">
                <h4>Contact Us</h4>
                <p>Email: ggf.agent.02488@gmail.com</p>
                <p>Phone: +1 408-502-9445</p>
                <div class="social-icons">
                    <a href="#" aria-label="Facebook"><img src="images/facebook.png" alt="Facebook"></a>
                    <a href="#" aria-label="Twitter"><img src="images/twitter.png" alt="Twitter"></a>
                    <a href="#" aria-label="Instagram"><img src="images/insta.jpg" alt="Instagram"></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 The Global Fund. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="script.js"></script>

</body>
</html>
