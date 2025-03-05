<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * My Courses Page.
 *
 * @package    core
 * @subpackage my
 * @copyright  2021 Mathew May
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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

// Start setting up the page.
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

// No blocks can be edited on this page
$PAGE->force_lock_all_blocks();
$PAGE->theme->addblockposition = BLOCK_ADDBLOCK_POSITION_CUSTOM;

// Add course management if the user has the capabilities for it.
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

// ✅ Check if the user is an admin and fetch last uploaded course
global $DB, $USER;

$is_admin = is_siteadmin($USER->id);
$showpopup = false;
$popupmessage = "";

if ($is_admin) {
    $last_course = $DB->get_record_sql("SELECT id, fullname, timecreated FROM {course} ORDER BY timecreated DESC LIMIT 1");

    if ($last_course) {
        $last_upload_time = $last_course->timecreated;
        $current_time = time();
        $one_week = 7 * 24 * 60 * 60; // 1 week in seconds

        if (($current_time - $last_upload_time) >= $one_week) {
            $showpopup = true;
            $popupmessage = "It's been more than a week since a course was last uploaded! Please upload a new course.";
        }
    }
}

echo $OUTPUT->header();

if (core_userfeedback::should_display_reminder()) {
    core_userfeedback::print_reminder_block();
}

echo $OUTPUT->custom_block_region('content');
?>

<!-- ✅ Popup HTML -->
<?php if ($showpopup) { ?>
    <div id="customPopup" class="popup-overlay" style="display: none;">
        <div class="popup-content">
            <button class="btn-close" onclick="closePopup()">×</button>
            <img src="<?php echo $alert_gif; ?>" alt="Alert Image" class="popup-image">
            <h3>Important Notice:</h3>
            <p><?php echo htmlspecialchars($popupmessage, ENT_QUOTES, 'UTF-8'); ?></p>
            
            <button class=" create-course-btn" onclick="redirectToCreateCourse()">Create Course</button>
        </div>
    </div>
<?php } ?>

<script>
    function closePopup() {
        document.getElementById("customPopup").style.display = "none";
    }
    function redirectToCreateCourse() {
    window.location.href = window.location.origin + "/moodle/course/edit.php";
}
    document.addEventListener("DOMContentLoaded", function () {
        var showPopup = <?php echo json_encode($showpopup); ?>;
        if (showPopup) {
            document.getElementById("customPopup").style.display = "flex";
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
