## moodle-custom-auth
# Moodle External Registration Page

A custom external registration and profile-view system built outside Moodle, but fully connected to Moodle’s user database and authentication workflow. This page retrieves user information, displays a personalized dashboard, and allows profile management without altering Moodle core files.

Overview

This project provides a modern, responsive user profile dashboard for Moodle users who register or log in through an external PHP application. The page pulls data directly from Moodle’s user table and presents it in a clean layout.

The interface includes:

Welcome header with user avatar, name, and email

Quick access buttons for editing profile and logging out

Account statistics (member since, account status, activity days)

Full profile view: personal, academic, and account details

This helps institutions offer a more user-friendly onboarding and self-service portal outside Moodle while maintaining full compatibility.

Features
1. User Header Card

Displays the logged-in user’s:

Initial-based avatar

First name

Email

Buttons for:

Editing profile

Logging out

2. Statistics Grid

Shows:

Membership date

Account status

Number of active days

3. Profile Information Card

Organized into sections:

Personal Information: full name, email status, ID number, phone

Academic Information: department, institution

Account Details: username, secret code, registration timestamp

4. Security & Data Handling

Protects user output with htmlspecialchars()

Formats dates properly

Provides fallbacks for empty fields

Supports custom styling and theming

Requirements

PHP 7.4+

Moodle database connection (MySQL/MariaDB)

Existing Moodle user session or custom login script

Font Awesome for icons

Custom CSS file for styling (not included in this snippet)

File Structure (Suggested)
/external-registration
│── index.php               # Dashboard page
│── edit_profile.php        # Profile update page
│── logout.php              # Session termination
│── css/style.css           # Custom styling
│── includes/
│     └── db.php            # Moodle DB connection
│── README.md               # Project documentation

Code Explanation

The script retrieves a $user array populated from Moodle’s mdl_user table. Key variables include:

$user['firstname'], $user['lastname'], $user['email']

$user['department'], $user['institution']

$user['phone'], $user['username'], $user['secret']

$user['created_at']

$account_age — number of days since account creation

The interface uses clean HTML, PHP echo blocks, and Font Awesome icons to present a polished dashboard.

How to Use

Connect to Moodle database using your db.php file.

Authenticate user and fetch the user object from mdl_user.

Store user data in the $user array.

Pass $account_age from your PHP logic.

Load the dashboard page.

Security Considerations

Always sanitize user data before output, as shown in the script.

Handle sessions securely (session_start(), session expiration, HTTPS).

Restrict access to authenticated users only.

Avoid exposing sensitive Moodle fields.

Customization

You may customize:

Color scheme

Typography

Grid layout

Additional profile fields from Moodle or custom tables

Credits

Developed for institutions seeking a seamless external onboarding experience integrated with Moodle's user system.