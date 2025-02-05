<?php
require_once('config.php'); // Load Moodle config
require_once($CFG->libdir.'/moodlelib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->dirroot . '/user/profile/lib.php');

// Moodle Header
$PAGE->set_url(new moodle_url('/custom_signup.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title('User Signup');
// $PAGE->set_heading('Create a New Account');

echo $OUTPUT->header();

$countries = get_string_manager()->get_list_of_countries(); // Get country list from Moodle
?>

<style>
    /* Background Image for the Whole Page */
    /* Ensure the page can scroll */
 body {
    height: 100%;
    overflow: auto; /* Allow scrolling */
    font-family: 'Arial', sans-serif;
    background: url('./pix/2.png') no-repeat center center fixed;
    background-size: cover;
    margin: 0;
    padding: 0;
}
.main-inner {
    background: rgba(255, 255, 255, 0) !important; /* Light transparency */
}

#region-main {
    background: rgba(255, 255, 255, 0.1) !important; /* Same transparency */
}
/* Dark overlay to improve readability */
.overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5); 
    z-index: 1;
}

/* Centering the form properly */
.form-container {
    position: relative; /* Changed from absolute to prevent overlap issues */
    width: 35%;
    margin: 50px auto; /* Adds space and prevents overlap */
    background: rgba(255, 255, 255, 0.95); /* Slight transparency */
    padding: 30px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3);
    border-radius: 10px;
    text-align: center;
    z-index: 2;
}

/* Ensure inputs are well spaced and visible */
input, select {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
    transition: 0.3s;
}

/* Improve focus effect */
input:focus, select:focus {
    border-color: #0073e6;
    box-shadow: 0px 0px 8px rgba(0, 115, 230, 0.5);
    outline: none;
}

/* Styling for Register Button */
button {
    width: 100%;
    padding: 12px;
    background-color: #0073e6;
    color: white;
    font-size: 18px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background-color: #005bb5;
}

/* Styling for the Back Button */




/* Ensure proper layout on smaller screens */
@media (max-width: 1024px) {
    .form-container {
        width: 60%;
    }
}

@media (max-width: 768px) {
    .form-container {
        width: 80%;
        margin: 30px auto;
    }
}

@media (max-width: 480px) {
    .form-container {
        width: 90%;
        padding: 20px;
    }
}
#page-footer {
  
    z-index: 1;
}

/* Improve footer text visibility */



</style>

<!-- Background Overlay -->
<div class="overlay"></div>

<!-- Signup Form -->
<div class="form-container">
    <h3 style="color: #0073e6;">Register Yourself</h3>
    
    <form id="registerForm">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password (8+ chars, 1 uppercase, 1 digit, 1 special char)" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="email" name="email_confirm" placeholder="Email (Again)" required>
        <input type="text" name="firstname" placeholder="First Name" required>
        <input type="text" name="lastname" placeholder="Last Name" required>
        <input type="text" name="city" placeholder="City/Town">

        <select name="country" required>
            <option value="">Select Country</option>
            <?php foreach ($countries as $code => $name): ?>
                <option value="<?= $code ?>"><?= $name ?></option>
            <?php endforeach; ?>
        </select>

        <button type="button" onclick="submitSignup(event)">Register</button>
        <p class="error" id="error-msg"></p>
    </form>

    <!-- Back Button -->
  <!-- Back Button -->
<a href="<?php echo $CFG->wwwroot; ?>/login/index.php" class="back-btn">⬅ Back to Login Page</a>

</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function submitSignup(event) {
        event.preventDefault();
        $.ajax({
            url: "custom_signup_process.php",
            type: "POST",
            data: $("#registerForm").serialize(),
            dataType: "json",
            success: function(response) {
                if (response.status === "success") {
                    alert(response.message);
                    window.location.href = response.redirect;
                } else {
                    $("#error-msg").text(response.message);
                }
            },
            error: function() {
                $("#error-msg").text("An error occurred. Please try again.");
            }
        });
    }
    function goBack() {
        // If user came from another page, go back
        if (document.referrer) {
            window.history.back();
        } else {
            // Otherwise, go to login page
            window.location.href = "<?php echo $CFG->wwwroot; ?>/login/index.php";
        }
    }
</script>

<?php
// Moodle Footer
echo $OUTPUT->footer();
?>
