<?php
require_once('config.php'); // Load Moodle config
require_once($CFG->libdir . '/moodlelib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->dirroot . '/lib/moodlelib.php'); // For random_string()

global $DB, $CFG;

// Suppress debugging messages to prevent JSON errors
@ini_set('display_errors', 0);
error_reporting(0);
header('Content-Type: application/json');

// Set Moodle page context
$PAGE->set_context(context_system::instance());

// Get form data safely
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$email_confirm = isset($_POST['email_confirm']) ? trim($_POST['email_confirm']) : '';
$firstname = isset($_POST['firstname']) ? trim($_POST['firstname']) : '';
$lastname = isset($_POST['lastname']) ? trim($_POST['lastname']) : '';
$city = isset($_POST['city']) ? trim($_POST['city']) : '';
$country = isset($_POST['country']) ? $_POST['country'] : '';

// Validate inputs
if (empty($username) || empty($password) || empty($email) || empty($email_confirm) || empty($firstname) || empty($lastname)) {
    die(json_encode(["status" => "error", "message" => "All required fields must be filled."]));
}

if ($email !== $email_confirm) {
    die(json_encode(["status" => "error", "message" => "Emails do not match."]));
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die(json_encode(["status" => "error", "message" => "Invalid email format."]));
}

// Password validation (8+ chars, 1 digit, 1 uppercase, 1 special character)
if (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}$/', $password)) {
    die(json_encode(["status" => "error", "message" => "Password must have at least 8 characters, 1 digit, 1 uppercase, and 1 special character."]));
}

// Check if username or email already exists in Moodle
if ($DB->record_exists('user', ['username' => $username])) {
    die(json_encode(["status" => "error", "message" => "Username already exists."]));
}
if ($DB->record_exists('user', ['email' => $email])) {
    die(json_encode(["status" => "error", "message" => "Email already exists."]));
}

// Create new Moodle user object with all necessary fields
$user = new stdClass();
$user->auth = 'manual'; // Moodle's default authentication method
$user->confirmed = 0;  // Set to 0, so user needs to confirm email
$user->mnethostid = $CFG->mnet_localhost_id;
$user->username = $username;
$user->password = hash_internal_user_password($password);
$user->firstname = $firstname;
$user->lastname = $lastname;
$user->email = $email;
$user->city = $city;
$user->country = $country;
$user->timecreated = time();
$user->timemodified = time();
$user->secret = random_string(15); // Generate unique confirmation key

// Fix missing fields error
$user->firstnamephonetic = '';
$user->lastnamephonetic = '';
$user->middlename = '';
$user->alternatename = '';

// Insert user into Moodle's database
$user->id = $DB->insert_record('user', $user);

if ($user->id) {
    // Send confirmation email
    if (!send_confirmation_email($user)) {
        error_log("Moodle: Failed to send confirmation email to " . $user->email);
        die(json_encode(["status" => "error", "message" => "Could not send confirmation email. Please contact support."]));
    }

    echo json_encode([
        "status" => "success",
        "message" => "Registration successful! Please check your email to confirm your account.",
        "redirect" => $CFG->wwwroot . "/login/index.php"
    ]);
} else {
    die(json_encode(["status" => "error", "message" => "Error registering user. Please try again."]));
}
?>
