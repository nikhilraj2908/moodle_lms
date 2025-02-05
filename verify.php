<?php
require_once('config.php'); // Load Moodle config
require_once($CFG->libdir.'/moodlelib.php');
require_once($CFG->dirroot . '/user/lib.php');

global $DB, $CFG;

$email = urldecode($_GET['email']);
$token = $_GET['token'];

if (empty($email) || empty($token)) {
    die("Invalid confirmation link.");
}

// Find user by email and token
$user = $DB->get_record('user', ['email' => $email, 'secret' => $token, 'confirmed' => 0]);

if (!$user) {
    die("Invalid or already confirmed link.");
}

// Confirm user
$user->confirmed = 1;
$user->secret = ''; // Remove token after confirmation
$DB->update_record('user', $user);

// Auto-login user after confirmation
complete_user_login($user);

// Redirect to Moodle dashboard
redirect($CFG->wwwroot . "/my/");
?>
