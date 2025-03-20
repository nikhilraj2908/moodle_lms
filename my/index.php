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

// Prepare Dashboard Data for Mustache Template
$templatecontext = [
    'username' => fullname($USER),
    'completedCourses' => 0, // Initialize
    'totalCourses' => 0, // Initialize
    'totalOverdue' => 0, // Initialize
    'totalPoints' => 0, // Initialize
    'totalPossiblePoints' => 0, // Initialize
    'learningPathPercentage' => 0, // Initialize
    'curriculumPercentage' => 0, // Initialize
    'hoursActivity' => json_encode([]), // Initialize for weekly activity
    'percentageChange' => 0, // Initialize
    'isIncrease' => true, // Initialize
    'currentWeekTotal' => 0, // Initialize
    'previousWeekTotal' => 0, // Initialize
    'courses' => []
];

// Fetch User Progress Data and Weekly Activity
if (isloggedin() && !isguestuser()) {
    global $DB, $USER, $OUTPUT, $CFG;

    $userid = $USER->id;
    $username = $USER->username;
    $displayname = fullname($USER);
    $userpicture = $OUTPUT->user_picture($USER, ['size' => 70]);

    $imageurl = $OUTPUT->image_url('Asset1', 'theme_academi');
    $courseenrolledgif = $OUTPUT->image_url('graduate', 'theme_academi')->out();
    $awardgif = $OUTPUT->image_url('award', 'theme_academi')->out();

    // ======================== 📌 Weekly Activity Data (Day-by-Day for Current Week) ========================
    $currentWeekSql = "
    WITH RECURSIVE week_days AS (
        SELECT 
            DATE_SUB(CURDATE(), INTERVAL (DAYOFWEEK(CURDATE()) - 2) DAY) AS week_day
        UNION ALL
        SELECT 
            DATE_ADD(week_day, INTERVAL 1 DAY)
        FROM 
            week_days
        WHERE 
            week_day < DATE_SUB(CURDATE(), INTERVAL (DAYOFWEEK(CURDATE()) - 8) DAY)
    ),
    sessions AS (
        SELECT 
            log.userid,
            log.timecreated,
            @session_id := IF(@prev_user = log.userid AND (log.timecreated - @prev_time) <= 1800, 
                              @session_id, 
                              @session_id + 1) AS session_id,
            IF(@prev_user = log.userid AND (log.timecreated - @prev_time) <= 1800, 
               log.timecreated - @prev_time, 
               0) AS time_spent,
            @prev_time := log.timecreated,
            @prev_user := log.userid
        FROM 
            mdl_logstore_standard_log AS log
        JOIN 
            mdl_user AS u ON log.userid = u.id,
            (SELECT @prev_time := NULL, @prev_user := NULL, @session_id := 0) AS vars
        WHERE 
            u.id = ?
            AND YEARWEEK(FROM_UNIXTIME(log.timecreated), 1) = YEARWEEK(CURDATE(), 1)
        ORDER BY 
            log.userid, log.timecreated
    )
    SELECT 
        DAYNAME(week_days.week_day) AS day_name,
        ROUND(COALESCE(SUM(activity.time_spent) / 3600, 0), 2) AS hours_spent
    FROM 
        week_days
    LEFT JOIN (
        SELECT 
            DATE(FROM_UNIXTIME(timecreated)) AS activity_date,
            time_spent
        FROM 
            sessions
    ) AS activity
    ON 
        activity.activity_date = week_days.week_day
    GROUP BY 
        week_days.week_day
    ORDER BY 
        (DAYOFWEEK(week_days.week_day) + 5) % 7;
    ";

    $currentWeekData = $DB->get_records_sql($currentWeekSql, [$userid]);

    // Map the data to an array for the chart (Sunday to Saturday)
    $hoursActivity = [];
    foreach (['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day) {
        $hoursActivity[] = isset($currentWeekData[strtolower($day)]) ? $currentWeekData[strtolower($day)]->hours_spent : 0;
    }

    // ======================== 📌 Total Hours and Percentage Change (Current vs Previous Week) ========================
    $weeklyHoursSql = "
    SELECT 
        total_current_week_hours,
        total_previous_week_hours,
        CASE 
            WHEN total_previous_week_hours > 0 THEN
                ROUND(((total_current_week_hours - total_previous_week_hours) / total_previous_week_hours) * 100, 2)
            ELSE 
                0
        END AS percentage_change
    FROM (
        SELECT 
            (SELECT 
                COALESCE(SUM(time_spent) / 3600, 0)
            FROM (
                SELECT 
                    userid, 
                    timecreated,
                    @prev_time AS prev_time,
                    IF(@prev_user = userid, timecreated - @prev_time, 0) AS time_spent,
                    @prev_time := timecreated,
                    @prev_user := userid
                FROM 
                    mdl_logstore_standard_log,
                    (SELECT @prev_time := NULL, @prev_user := NULL) AS vars
                WHERE 
                    userid = ?
                ORDER BY 
                    userid, timecreated
            ) AS activity
            WHERE 
                YEARWEEK(FROM_UNIXTIME(timecreated), 1) = YEARWEEK(CURDATE(), 1)
            ) AS total_current_week_hours,
            
            (SELECT 
                COALESCE(SUM(time_spent) / 3600, 0)
            FROM (
                SELECT 
                    userid, 
                    timecreated,
                    @prev_time2 AS prev_time,
                    IF(@prev_user2 = userid, timecreated - @prev_time2, 0) AS time_spent,
                    @prev_time2 := timecreated,
                    @prev_user2 := userid
                FROM 
                    mdl_logstore_standard_log,
                    (SELECT @prev_time2 := NULL, @prev_user2 := NULL) AS vars
                WHERE 
                    userid = ?
                ORDER BY 
                    userid, timecreated
            ) AS activity
            WHERE 
                YEARWEEK(FROM_UNIXTIME(timecreated), 1) = YEARWEEK(CURDATE() - INTERVAL 1 WEEK, 1)
            ) AS total_previous_week_hours
    ) AS weekly_hours;
    ";

    $weeklyHoursData = $DB->get_record_sql($weeklyHoursSql, [$userid, $userid]);

    $currentWeekTotal = $weeklyHoursData->total_current_week_hours ?? 0;
    $previousWeekTotal = $weeklyHoursData->total_previous_week_hours ?? 0;
    $percentageChange = $weeklyHoursData->percentage_change ?? 0;
    $isIncrease = $percentageChange >= 0;

    // ======================== 📌 Fetch User Progress & Points ========================
    $sql = "
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
        WHERE cmc.userid = (SELECT userid FROM UserID) AND cmc.completionstate = 1
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
        ((SELECT total_assigned_courses FROM TotalCourses) - (SELECT total_completed_courses FROM CompletedCourses)) AS total_courses_overdue,
        (SELECT total_points_earned FROM TotalPoints) AS total_points_earned,
        (SELECT max_total_points FROM MaxPoints) AS total_possible_points
    FROM dual
    ";

    $userData = $DB->get_record_sql($sql, [$USER->username]);

    $totalCourses = $userData->total_courses_assigned ?? 0;
    $completedCourses = $userData->total_courses_completed ?? 0;
    $totalOverdue = $userData->total_courses_overdue ?? 0;
    $totalPoints = $userData->total_points_earned ?? 0;
    $totalPossiblePoints = $userData->total_possible_points ?? 0;

    $formattedTotalPoints = rtrim(rtrim(number_format($totalPoints, 2, '.', ''), '0'), '.');
    $formattedTotalPossiblePoints = rtrim(rtrim(number_format($totalPossiblePoints, 2, '.', ''), '0'), '.');

    $learningPathPercentage = ($totalCourses > 0) ? round(($completedCourses / $totalCourses) * 100, 2) : 0;

    // Fetch Course Progress Data
    $sql = "
    WITH UserID AS (
        SELECT id AS userid FROM mdl_user WHERE username = ?
    ),
    CompletedCourses AS (
        SELECT
            c.id AS course_id,
            c.fullname AS course_name,
            SUM(g.finalgrade) AS earned_points,
            SUM(g.rawgrademax) AS total_points_assigned
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
        ROUND((earned_points / NULLIF(total_points_assigned, 0)) * 100, 0) AS percentage_points_earned
    FROM CompletedCourses
    ORDER BY earned_points DESC;
    ";

    $userCourses = $DB->get_records_sql($sql, [$USER->username]);
    $courses_data = [];
    foreach ($userCourses as $course) {
        $earned_points = intval($course->earned_points ?? 0);
        $total_points = intval($course->total_points_assigned ?? 1); // Avoid division by zero
        $percentage = intval(round(($earned_points / $total_points) * 100, 0));

        if ($percentage >= 70) {
            $bar_color = "#204070"; // Dark Blue
        } elseif ($percentage >= 40) {
            $bar_color = "#3C6894"; // Light Blue
        } else {
            $bar_color = "#808080"; // Gray
        }

        $courses_data[] = [
            'course_name' => $course->course_name,
            'earned_points' => $earned_points,
            'total_points' => $total_points,
            'percentage' => $percentage,
            'bar_color' => $bar_color,
            'points_display' => $earned_points . '/' . $total_points
        ];
    }

    // Assign data to Mustache context
    $templatecontext['username'] = $displayname;
    $templatecontext['userpicture'] = $userpicture;
    $templatecontext['completedCourses'] = $completedCourses;
    $templatecontext['totalCourses'] = $totalCourses;
    $templatecontext['totalOverdue'] = $totalOverdue;
    $templatecontext['totalPoints'] = $formattedTotalPoints;
    $templatecontext['totalPossiblePoints'] = $formattedTotalPossiblePoints;
    $templatecontext['learningPathPercentage'] = $learningPathPercentage;
    $templatecontext['curriculumPercentage'] = $learningPathPercentage;
    $templatecontext['asset1_image_url'] = $imageurl;
    $templatecontext['course_enrolled_url'] = $courseenrolledgif;
    $templatecontext['course_award_url'] = $awardgif;
    $templatecontext['hoursActivity'] = json_encode($hoursActivity);
    $templatecontext['percentageChange'] = $percentageChange;
    $templatecontext['isIncrease'] = $isIncrease;
    $templatecontext['currentWeekTotal'] = round($currentWeekTotal, 2);
    $templatecontext['previousWeekTotal'] = round($previousWeekTotal, 2);
    $templatecontext['courses'] = $courses_data;
}

echo $OUTPUT->header();

if (core_userfeedback::should_display_reminder()) {
    core_userfeedback::print_reminder_block();
}

if (has_capability('moodle/site:manageblocks', context_system::instance())) {
    echo $OUTPUT->addblockbutton('content');
}

// Render Dashboard Mustache Template
echo $OUTPUT->render_from_template('core/dashboard', $templatecontext);

echo $OUTPUT->custom_block_region('content');

echo $OUTPUT->footer();

// Trigger dashboard has been viewed event.
$eventparams = array('context' => $context);
$event = \core\event\dashboard_viewed::create($eventparams);
$event->trigger();