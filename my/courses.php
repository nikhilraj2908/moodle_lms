<?php
// Moodle My Courses Page

require_once(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/my/lib.php');
require_once($CFG->dirroot . '/course/lib.php');

redirect_if_major_upgrade_required();
require_login();

$hassiteconfig = has_capability('moodle/site:config', context_system::instance());
if ($hassiteconfig && moodle_needs_upgrading()) {
    redirect(new moodle_url('/admin/index.php'));
}

$context = context_system::instance();

// Get the My Moodle page info
if (!$currentpage = my_get_page(null, MY_PAGE_PUBLIC, MY_PAGE_COURSES)) {
    throw new Exception('mymoodlesetup');
}

// Page setup
$PAGE->set_context($context);
$PAGE->set_url('/my/courses.php');
$PAGE->add_body_classes(['limitedwidth', 'page-mycourses']);
$PAGE->set_pagelayout('mycourses');
$PAGE->set_pagetype('my-index');
$PAGE->blocks->add_region('content');
$PAGE->set_subpage($currentpage->id);
$PAGE->set_title(get_string('mycourses'));
$PAGE->set_heading(get_string('mycourses'));

// ✅ Define alert GIF URL
$alert_gif = $OUTPUT->image_url('alert', 'theme_academi')->out(false);

// No blocks can be edited
$PAGE->force_lock_all_blocks();
$PAGE->theme->addblockposition = BLOCK_ADDBLOCK_POSITION_CUSTOM;

// Course management menu
$coursecat = core_course_category::user_top();
$coursemanagemenu = [];

if (count(enrol_get_all_users_courses($USER->id, true)) > 0) {
    if ($coursecat && ($category = core_course_category::get_nearest_editable_subcategory($coursecat, ['create']))) {
        $coursemanagemenu['newcourseurl'] = new moodle_url('/course/edit.php', ['category' => $category->id]);
    }
    if ($coursecat && ($category = core_course_category::get_nearest_editable_subcategory($coursecat, ['manage']))) {
        $coursemanagemenu['manageurl'] = new moodle_url('/course/management.php', ['categoryid' => $category->id]);
    }
    if ($coursecat) {
        $category = core_course_category::get_nearest_editable_subcategory($coursecat, ['moodle/course:request']);
        if ($category && $category->can_request_course()) {
            $coursemanagemenu['courserequesturl'] = new moodle_url('/course/request.php', ['categoryid' => $category->id]);
        }
    }
}

if (!empty($coursemanagemenu)) {
    $PAGE->add_header_action($OUTPUT->render_from_template('my/dropdown', $coursemanagemenu));
}

global $DB, $USER;

// ✅ Admin: Check last uploaded course
$is_admin = is_siteadmin($USER->id);
$showpopup_admin = false;
$popupmessage_admin = "";

if ($is_admin) {
    $last_course = $DB->get_record_sql("SELECT id, fullname, timecreated FROM {course} ORDER BY timecreated DESC LIMIT 1");

    if ($last_course) {
        $last_upload_time = $last_course->timecreated;
        $current_time = time();
        $one_week = 7 * 24 * 60 * 60; // 1 week in seconds

        if (($current_time - $last_upload_time) >= $one_week) {
            $showpopup_admin = true;
            $popupmessage_admin = "It's been more than a week since a course was last uploaded! Please upload a new course.";
        }
    }
}

// ✅ Regular Users: Check inactivity (Admins are excluded)
$showpopup_user = false;
$popupmessage_user = "";

if (!$is_admin) { // Condition ensures only users (not admins) get this popup
    $last_activity = $DB->get_field_sql("
        SELECT MAX(timeaccess) 
        FROM {user_lastaccess} 
        WHERE userid = ?", [$USER->id]);

    $current_time = time();
    $one_hour = 60 * 60; // 1 hour in seconds

    if ($last_activity) {
        if (($current_time - $last_activity) >= $one_hour) {
            $showpopup_user = true;
            $popupmessage_user = "You have been inactive for over an hour! Continue your learning journey now.";
        }
    } else {
        // If no activity found, show the popup
        $showpopup_user = true;
        $popupmessage_user = "You haven't started any course activity yet. Explore and start learning!";
    }
}

echo $OUTPUT->header();

if (core_userfeedback::should_display_reminder()) {
    core_userfeedback::print_reminder_block();
}

echo $OUTPUT->custom_block_region('content');
?>

<!-- ✅ Admin Popup -->
<?php if ($showpopup_admin) { ?>
    <div id="adminPopup" class="popup-overlay" style="display: none;">
        <div class="popup-content">
            <button class="btn-close" onclick="closePopup('adminPopup')">×</button>
            <img src="<?php echo $alert_gif; ?>" alt="Alert Image" class="popup-image">
            <h3>Important Notice:</h3>
            <p><?php echo htmlspecialchars($popupmessage_admin, ENT_QUOTES, 'UTF-8'); ?></p>
            <button class="create-course-btn" onclick="redirectToCreateCourse()">Create Course</button>
        </div>
    </div>
<?php } ?>

<!-- ✅ User Inactivity Popup (ONLY FOR NON-ADMINS) -->
<?php if ($showpopup_user) { ?>
    <div id="userPopup" class="popup-overlay" style="display: none;">
        <div class="popup-content">
            <button class="btn-close" onclick="closePopup('userPopup')">×</button>
            <img src="<?php echo $alert_gif; ?>" alt="Alert Image" class="popup-image">
            <h3>Hey there!</h3>
            <p><?php echo htmlspecialchars($popupmessage_user, ENT_QUOTES, 'UTF-8'); ?></p>
            <button class="btn-continue" onclick="redirectToCourses()">Go to Courses</button>
        </div>
    </div>
<?php } ?>

<script>
    function closePopup(popupId) {
        document.getElementById(popupId).style.display = "none";
    }

    function redirectToCreateCourse() {
        window.location.href = "<?php echo $CFG->wwwroot; ?>/course/edit.php";
    }

    function redirectToCourses() {
        window.location.href = "<?php echo $CFG->wwwroot; ?>/course";
    }

    document.addEventListener("DOMContentLoaded", function () {
        if (<?php echo json_encode($showpopup_admin); ?>) {
            document.getElementById("adminPopup").style.display = "flex";
        }
        if (<?php echo json_encode($showpopup_user); ?>) {
            document.getElementById("userPopup").style.display = "flex";
        }
    });
</script>

<?php
echo $OUTPUT->footer();

// ✅ Trigger dashboard viewed event
$eventparams = ['context' => $context];
$event = \core\event\mycourses_viewed::create($eventparams);
$event->trigger();
?>
