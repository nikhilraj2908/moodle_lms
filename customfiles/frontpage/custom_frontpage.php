<?php
require_once('config.php'); // Load Moodle config
require_once($CFG->libdir . '/moodlelib.php');
require_once($CFG->dirroot . '/user/lib.php');

// Set up Moodle page
$PAGE->set_url(new moodle_url('/custom_frontpage.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Welcome to Our Custom Moodle Front Page');
$PAGE->set_heading('Custom Moodle Home Page');

// Load Moodle Header
echo $OUTPUT->header();
?>

<style>
/* General Styling */
body {
    font-family: 'Arial', sans-serif;
    background: url('./pix/background.jpg') no-repeat center center fixed;
    background-size: cover;
    margin: 0;
    padding: 0;
    color: white;
}

/* Centered Container */
.container {
    text-align: center;
    padding: 50px;
}

h1 {
    font-size: 40px;
    margin-bottom: 20px;
}

p {
    font-size: 20px;
    margin-bottom: 20px;
}

/* Buttons */
.button-container {
    margin-top: 30px;
}

.button {
    display: inline-block;
    padding: 15px 30px;
    font-size: 18px;
    color: white;
    background-color: #0073e6;
    border: none;
    border-radius: 5px;
    text-decoration: none;
    transition: 0.3s;
}

.button:hover {
    background-color: #005bb5;
}

/* Footer */
.footer {
    position: fixed;
    bottom: 10px;
    width: 100%;
    text-align: center;
    font-size: 14px;
}
</style>

<!-- Main Content -->
<div class="container">
    <h1>Welcome to Our Moodle Platform</h1>
    <p>Enhance your learning experience with our customized Moodle setup.</p>

    <div class="button-container">
        <a href="<?php echo $CFG->wwwroot; ?>/custom_signup.php" class="button">Register</a>
        <a href="<?php echo $CFG->wwwroot; ?>/login/index.php" class="button">Login</a>
        <a href="<?php echo $CFG->wwwroot; ?>/course/index.php" class="button">Browse Courses</a>
    </div>
</div>

<!-- Footer -->
<div class="footer">
    <p>© 2024 Your Moodle Platform. All Rights Reserved.</p>
</div>

<?php
// Load Moodle Footer
echo $OUTPUT->footer();
?>
