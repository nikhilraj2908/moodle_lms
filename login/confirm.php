<?php

require(__DIR__ . '/../config.php');
require_once(__DIR__ . '/lib.php');
require_once($CFG->libdir . '/authlib.php');

$PAGE->set_url('/login/confirm.php');
$PAGE->set_context(context_system::instance());

error_log("------ CONFIRMATION PROCESS STARTED ------");

// Retrieve URL parameters
$data = optional_param('data', '', PARAM_RAW);
$p = optional_param('p', '', PARAM_ALPHANUM);
$s = optional_param('s', '', PARAM_RAW);
$redirect = optional_param('redirect', '', PARAM_LOCALURL);

error_log("Full Request URI: " . $_SERVER['REQUEST_URI']);
error_log("Received data: " . $data);
error_log("Received p: " . $p);
error_log("Received s: " . $s);

if (!$authplugin = signup_get_user_confirmation_authplugin()) {
    error_log("Error: Confirmation plugin not enabled");
    throw new moodle_exception('confirmationnotenabled');
}

// Extract user secret and username
if (!empty($data)) {
    $dataelements = explode('/', $data, 2);
    if (count($dataelements) !== 2) {
        error_log("Error: Invalid data format received: " . $data);
        throw new moodle_exception('invalidconfirmdata');
    }

    $usersecret = trim($dataelements[0]);
    $username = trim($dataelements[1]);

    error_log("Extracted secret: " . $usersecret);
    error_log("Extracted username: " . $username);
} else {
    $usersecret = trim($p);
    $username = trim($s);
}

// Check if user exists
$user = $DB->get_record('user', ['username' => $username], '*');
if (!$user) {
    error_log("Error: User not found in database: " . $username);
    throw new moodle_exception('cannotfinduser', '', '', s($username));
}

// Ensure secret matches database
if ($user->secret !== $usersecret) {
    error_log("Error: Secret key does not match for user: " . $username);
    throw new moodle_exception('invalidconfirmdata');
}

// If user is already confirmed
if ($user->confirmed == 1) {
    error_log("User already confirmed: " . $username);
    throw new moodle_exception('alreadyconfirmed');
}

// 🔥 **Manually Update the User's Confirmation Status**
$user->confirmed = 1;
$DB->update_record('user', $user);
error_log("User successfully confirmed: " . $username);

// 🔥 **Automatically log in the user**
complete_user_login($user);
error_log("User auto-login successful: " . fullname($user));

// ✅ **Redirect User to Dashboard Instead of Login Page**
redirect($CFG->wwwroot . '/my/'); // Redirect to user dashboard instead of login page

error_log("------ CONFIRMATION PROCESS COMPLETED ------");

?>
