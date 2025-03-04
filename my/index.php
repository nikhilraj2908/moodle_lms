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
 * My Moodle -- a user's personal dashboard
 *
 * @package    moodlecore
 * @subpackage my
 * @copyright  2010 Remote-Learner.net
 * @author     Hubert Chathi <hubert@remote-learner.net>
 * @author     Olav Jordan <olav.jordan@remote-learner.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/my/lib.php');

redirect_if_major_upgrade_required();

// TODO Add sesskey check to edit
$edit   = optional_param('edit', null, PARAM_BOOL);    // Turn editing on and off
$reset  = optional_param('reset', null, PARAM_BOOL);

require_login();

$hassiteconfig = has_capability('moodle/site:config', context_system::instance());
if ($hassiteconfig && moodle_needs_upgrading()) {
    redirect(new moodle_url('/admin/index.php'));
}

$strmymoodle = get_string('myhome');

if (empty($CFG->enabledashboard)) {
    // Dashboard is disabled, so the /my page shouldn't be displayed.
    $defaultpage = get_default_home_page();
    if ($defaultpage == HOMEPAGE_MYCOURSES) {
        // If default page is set to "My courses", redirect to it.
        redirect(new moodle_url('/my/courses.php'));
    } else {
        // Otherwise, raise an exception to inform the dashboard is disabled.
        throw new moodle_exception('error:dashboardisdisabled', 'my');
    }
}

if (isguestuser()) {  // Force them to see system default, no editing allowed
    // If guests are not allowed my moodle, send them to front page.
    if (empty($CFG->allowguestmymoodle)) {
        redirect(new moodle_url('/', array('redirect' => 0)));
    }

    $userid = null;
    $USER->editing = $edit = 0;  // Just in case
    $context = context_system::instance();
    $PAGE->set_blocks_editing_capability('moodle/my:configsyspages');  // unlikely :)
    $strguest = get_string('guest');
    $pagetitle = "$strmymoodle ($strguest)";

} else {        // We are trying to view or edit our own My Moodle page
    $userid = $USER->id;  // Owner of the page
    $context = context_user::instance($USER->id);
    $PAGE->set_blocks_editing_capability('moodle/my:manageblocks');
    $pagetitle = $strmymoodle;
}

// Get the My Moodle page info.  Should always return something unless the database is broken.
if (!$currentpage = my_get_page($userid, MY_PAGE_PRIVATE)) {
    throw new \moodle_exception('mymoodlesetup');
}

// Start setting up the page
$params = array();
$PAGE->set_context($context);
$PAGE->set_url('/my/index.php', $params);
$PAGE->set_pagelayout('mydashboard');
$PAGE->add_body_class('limitedwidth');
$PAGE->set_pagetype('my-index');
$PAGE->blocks->add_region('content');
$PAGE->set_subpage($currentpage->id);

//////////////////////////////dashboard heading hatane k liye comnt kr diya///////////////////////
// $PAGE->set_title($pagetitle);
// $PAGE->set_heading($pagetitle);

if (!isguestuser()) {   // Skip default home page for guests
    if (get_home_page() != HOMEPAGE_MY) {
        if (optional_param('setdefaulthome', false, PARAM_BOOL)) {
            set_user_preference('user_home_page_preference', HOMEPAGE_MY);
        } else if (!empty($CFG->defaulthomepage) && $CFG->defaulthomepage == HOMEPAGE_USER) {
            $frontpagenode = $PAGE->settingsnav->add(get_string('frontpagesettings'), null, navigation_node::TYPE_SETTING, null);
            $frontpagenode->force_open();
            $frontpagenode->add(get_string('makethismyhome'), new moodle_url('/my/', array('setdefaulthome' => true)),
                    navigation_node::TYPE_SETTING);
        }
    }
}

// Toggle the editing state and switches
if (empty($CFG->forcedefaultmymoodle) && $PAGE->user_allowed_editing()) {
    if ($reset !== null) {
        if (!is_null($userid)) {
            require_sesskey();
            if (!$currentpage = my_reset_page($userid, MY_PAGE_PRIVATE)) {
                throw new \moodle_exception('reseterror', 'my');
            }
            redirect(new moodle_url('/my'));
        }
    } else if ($edit !== null) {             // Editing state was specified
        $USER->editing = $edit;       // Change editing state
    } else {                          // Editing state is in session
        if ($currentpage->userid) {   // It's a page we can edit, so load from session
            if (!empty($USER->editing)) {
                $edit = 1;
            } else {
                $edit = 0;
            }
        } else {
            // For the page to display properly with the user context header the page blocks need to
            // be copied over to the user context.
            if (!$currentpage = my_copy_page($USER->id, MY_PAGE_PRIVATE)) {
                throw new \moodle_exception('mymoodlesetup');
            }
            $context = context_user::instance($USER->id);
            $PAGE->set_context($context);
            $PAGE->set_subpage($currentpage->id);
            // It's a system page and they are not allowed to edit system pages
            $USER->editing = $edit = 0;          // Disable editing completely, just to be safe
        }
    }

    // Add button for editing page
    $params = array('edit' => !$edit);

    $resetbutton = '';
    $resetstring = get_string('resetpage', 'my');
    $reseturl = new moodle_url("$CFG->wwwroot/my/index.php", array('edit' => 1, 'reset' => 1));

    if (!$currentpage->userid) {
        // viewing a system page -- let the user customise it
        $editstring = get_string('updatemymoodleon');
        $params['edit'] = 1;
    } else if (empty($edit)) {
        $editstring = get_string('updatemymoodleon');
    } else {
        $editstring = get_string('updatemymoodleoff');
        $resetbutton = $OUTPUT->single_button($reseturl, $resetstring);
    }

    $url = new moodle_url("$CFG->wwwroot/my/index.php", $params);
    $button = '';
    if (!$PAGE->theme->haseditswitch) {
        $button = $OUTPUT->single_button($url, $editstring);
    }
    $PAGE->set_button($resetbutton . $button);

} else {
    $USER->editing = $edit = 0;
}

echo $OUTPUT->header();

if (core_userfeedback::should_display_reminder()) {
    core_userfeedback::print_reminder_block();
}

if (has_capability('moodle/site:manageblocks', context_system::instance())) {
    echo $OUTPUT->addblockbutton('content');
}

// Prepare Dashboard Data for Mustache Template
$templatecontext = [
    'username' => fullname($USER),
    'completedCourses' => 0, // Initialize
    'totalCourses' => 0, // Initialize
    'learningPathPercentage' => 0, // Initialize
    'curriculumPercentage' => 0, // Initialize
    'totalPoints' => 0, // Initialize
    'courses' => []
];

// Fetch User Progress Data
if (isloggedin() && !isguestuser()) {
    global $DB;

    $imageurl = $OUTPUT->image_url('Asset1', 'theme_academi');

    $courseenrolledgif = $OUTPUT->image_url('graduate', 'theme_academi')->out();
    $awardgif = $OUTPUT->image_url('award', 'theme_academi')->out();


    $userid = $USER->id;
    $username = $USER->username;
    $displayname = fullname($USER);
    $userpicture = $OUTPUT->user_picture($USER, ['size' => 70]);

    // -- NEW QUERY WITH CTE -----------------------------------------------
    $userData = $DB->get_record_sql("
        WITH UserID AS (
            SELECT id AS userid FROM mdl_user WHERE username = ?
        ),
        TotalCourses AS (
            SELECT COUNT(c.id) AS total_assigned_courses
            FROM mdl_course c
            JOIN mdl_enrol e ON c.id = e.courseid
            JOIN mdl_user_enrolments ue ON e.id = ue.enrolid
            WHERE ue.userid = (SELECT userid FROM UserID)
        ),
        CompletedCourses AS (
            SELECT COUNT(DISTINCT cm.course) AS total_completed_courses
            FROM mdl_course_modules_completion cmc
            JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
            WHERE cmc.userid = (SELECT userid FROM UserID) 
                AND cmc.completionstate = 1
        ),
        TotalPoints AS (
            SELECT COALESCE(SUM(g.finalgrade), 0) AS total_points_earned
            FROM mdl_grade_grades g
            WHERE g.userid = (SELECT userid FROM UserID)
        ),
        MaxPoints AS (
            SELECT COALESCE(SUM(g.rawgrademax), 0) AS max_total_points
            FROM mdl_grade_grades g
            WHERE g.userid = (SELECT userid FROM UserID)
        )
        SELECT 
            (SELECT total_assigned_courses FROM TotalCourses) AS total_courses_assigned,
            (SELECT total_completed_courses FROM CompletedCourses) AS total_courses_completed,
            ((SELECT total_assigned_courses FROM TotalCourses) 
                - (SELECT total_completed_courses FROM CompletedCourses)) AS total_courses_overdue,
            (SELECT total_points_earned FROM TotalPoints) AS total_points_earned,
            (SELECT max_total_points FROM MaxPoints) AS total_possible_points
        FROM dual
    ", [$username]);
    // ---------------------------------------------------------------------

    $totalCourses = $userData->total_courses_assigned ?? 0;
    $completedCourses = $userData->total_courses_completed ?? 0;
    $totalOverdue = $userData->total_courses_overdue ?? 0;
    $totalPoints = $userData->total_points_earned ?? 0;
    $totalPossiblePoints = $userData->total_possible_points ?? 0;

    $formattedTotalPoints = rtrim(rtrim(number_format($totalPoints, 2, '.', ''), '0'), '.');
    $formattedTotalPossiblePoints = rtrim(rtrim(number_format($totalPossiblePoints, 2, '.', ''), '0'), '.');

    $learningPathPercentage = ($totalCourses > 0)
        ? round(($completedCourses / $totalCourses) * 100, 2)
        : 0;

    $templatecontext['completedCourses'] = $completedCourses;
    $templatecontext['totalCourses'] = $totalCourses;
    $templatecontext['learningPathPercentage'] = $learningPathPercentage;
    $templatecontext['curriculumPercentage'] = $learningPathPercentage;
    $templatecontext['totalPoints'] = $formattedTotalPoints;
    $templatecontext['totalPossiblePoints'] = $formattedTotalPossiblePoints;
    $templatecontext['totalOverdue'] = $totalOverdue;
    $templatecontext['userpicture'] = $userpicture;
    $templatecontext['asset1_image_url'] = $imageurl;
    $templatecontext['course_enrolled_url'] = $courseenrolledgif;
    $templatecontext['course_award_url'] = $awardgif;



    $sql = "
    WITH UserID AS (
        SELECT id AS userid FROM mdl_user WHERE username = ?
    ),
    CompletedCourses AS (
        SELECT
            c.id AS course_id,
            c.fullname AS course_name,
            SUM(g.finalgrade) AS earned_points, -- Sum of earned points for the course
            SUM(g.rawgrademax) AS total_points_assigned -- Sum of max possible points
        FROM mdl_course c
        JOIN mdl_enrol e ON c.id = e.courseid
        JOIN mdl_user_enrolments ue ON e.id = ue.enrolid
        JOIN mdl_grade_items gi ON c.id = gi.courseid
        JOIN mdl_grade_grades g ON gi.id = g.itemid AND g.userid = ue.userid
        WHERE ue.userid = (SELECT userid FROM UserID)
        GROUP BY c.id, c.fullname
    )
    SELECT 
        course_id, 
        course_name,
        earned_points, 
        total_points_assigned,
        ROUND((earned_points / NULLIF(total_points_assigned, 0)) * 100, 0) AS percentage_points_earned -- Round to integer
    FROM CompletedCourses
    ORDER BY earned_points DESC;
    ";
    
    $userCourses = $DB->get_records_sql($sql, [$USER->username]);
    $courses_data = [];
    foreach ($userCourses as $course) {
        // Ensure values are not NULL and convert to integers
        $earned_points = intval($course->earned_points ?? 0);
        $total_points = intval($course->total_points_assigned ?? 1); // Avoid division by zero
        $percentage = intval(round(($earned_points / $total_points) * 100, 0)); // Round to integer
    
        // Assign colors based on percentage
        if ($percentage >= 70) {
            $bar_color = "#204070"; // Dark Blue
        } elseif ($percentage >= 40) {
            $bar_color = "#3C6894"; // Light Blue
        } else {
            $bar_color = "#808080"; // Gray
        }
    
        // Store data for Mustache template
        $courses_data[] = [
            'course_name' => $course->course_name,
            'earned_points' => $earned_points,
            'total_points' => $total_points,
            'percentage' => $percentage,
            'bar_color' => $bar_color,
            'points_display' => $earned_points . '/' . $total_points // Format: earned_points/total_points
        ];
    }
    
    // Assign data to Mustache context
    $templatecontext['courses'] = $courses_data;


}

// Render Dashboard Mustache Template
echo $OUTPUT->render_from_template('core/dashboard', $templatecontext);

echo $OUTPUT->custom_block_region('content');

echo $OUTPUT->footer();

// Trigger dashboard has been viewed event.
$eventparams = array('context' => $context);
$event = \core\event\dashboard_viewed::create($eventparams);
$event->trigger();