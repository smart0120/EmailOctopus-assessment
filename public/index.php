<?php

// Route API requests to api.php
if (strpos($_SERVER['REQUEST_URI'], '/api') === 0) {
    require_once __DIR__ . '/api.php';
    exit;
}

// Serve the web interface for all other requests
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mailing List Manager</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Mailing List Manager</h1>
            <p>Manage your email contacts with ease</p>
        </header>

        <main>
            <!-- Add Contact Form -->
            <section class="add-contact">
                <h2>Add New Contact</h2>
                <form id="addContactForm">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" required maxlength="200">
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Contact</button>
                </form>
                <div id="formMessage" class="message"></div>
            </section>

            <!-- Contacts List -->
            <section class="contacts-list">
                <h2>Mailing List Contacts</h2>
                <div class="contacts-header">
                    <span>Total: <span id="contactCount">0</span></span>
                    <button id="refreshBtn" class="btn btn-secondary">Refresh</button>
                </div>
                <div id="contactsContainer">
                    <div class="loading">Loading contacts...</div>
                </div>
            </section>
        </main>
    </div>

    <script src="js/app.js"></script>
</body>
</html> 