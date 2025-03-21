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

$edit = optional_param('edit', null, PARAM_BOOL);
$reset = optional_param('reset', null, PARAM_BOOL);

require_login();

$hassiteconfig = has_capability('moodle/site:config', context_system::instance());
if ($hassiteconfig && moodle_needs_upgrading()) {
    redirect(new moodle_url('/admin/index.php'));
}

$strmymoodle = get_string('myhome');

if (empty($CFG->enabledashboard)) {
    $defaultpage = get_default_home_page();
    if ($defaultpage == HOMEPAGE_MYCOURSES) {
        redirect(new moodle_url('/my/courses.php'));
    } else {
        throw new moodle_exception('error:dashboardisdisabled', 'my');
    }
}

if (isguestuser()) {
    if (empty($CFG->allowguestmymoodle)) {
        redirect(new moodle_url('/', array('redirect' => 0)));
    }
    $userid = null;
    $USER->editing = $edit = 0;
    $context = context_system::instance();
    $PAGE->set_blocks_editing_capability('moodle/my:configsyspages');
    $pagetitle = "$strmymoodle (" . get_string('guest') . ")";
} else {
    $userid = $USER->id;
    $context = context_user::instance($USER->id);
    $PAGE->set_blocks_editing_capability('moodle/my:manageblocks');
    $pagetitle = $strmymoodle;
}

if (!$currentpage = my_get_page($userid, MY_PAGE_PRIVATE)) {
    throw new \moodle_exception('mymoodlesetup');
}

$params = array();
$PAGE->set_context($context);
$PAGE->set_url('/my/index.php', $params);
$PAGE->set_pagelayout('mydashboard');
$PAGE->add_body_class('limitedwidth');
$PAGE->set_pagetype('my-index');
$PAGE->blocks->add_region('content');
$PAGE->set_subpage($currentpage->id);

if (!isguestuser() && get_home_page() != HOMEPAGE_MY) {
    if (optional_param('setdefaulthome', false, PARAM_BOOL)) {
        set_user_preference('user_home_page_preference', HOMEPAGE_MY);
    } else if (!empty($CFG->defaulthomepage) && $CFG->defaulthomepage == HOMEPAGE_USER) {
        $frontpagenode = $PAGE->settingsnav->add(get_string('frontpagesettings'), null, navigation_node::TYPE_SETTING, null);
        $frontpagenode->force_open();
        $frontpagenode->add(get_string('makethismyhome'), new moodle_url('/my/', array('setdefaulthome' => true)), navigation_node::TYPE_SETTING);
    }
}

if (empty($CFG->forcedefaultmymoodle) && $PAGE->user_allowed_editing()) {
    if ($reset !== null) {
        if (!is_null($userid)) {
            require_sesskey();
            if (!$currentpage = my_reset_page($userid, MY_PAGE_PRIVATE)) {
                throw new \moodle_exception('reseterror', 'my');
            }
            redirect(new moodle_url('/my'));
        }
    } else if ($edit !== null) {
        $USER->editing = $edit;
    } else {
        if ($currentpage->userid) {
            $edit = !empty($USER->editing) ? 1 : 0;
        } else {
            if (!$currentpage = my_copy_page($USER->id, MY_PAGE_PRIVATE)) {
                throw new \moodle_exception('mymoodlesetup');
            }
            $context = context_user::instance($USER->id);
            $PAGE->set_context($context);
            $PAGE->set_subpage($currentpage->id);
            $USER->editing = $edit = 0;
        }
    }

    $params = array('edit' => !$edit);
    $resetbutton = $OUTPUT->single_button(new moodle_url("$CFG->wwwroot/my/index.php", array('edit' => 1, 'reset' => 1)), get_string('resetpage', 'my'));
    $editstring = !$currentpage->userid || empty($edit) ? get_string('updatemymoodleon') : get_string('updatemymoodleoff');
    $button = !$PAGE->theme->haseditswitch ? $OUTPUT->single_button(new moodle_url("$CFG->wwwroot/my/index.php", $params), $editstring) : '';
    $PAGE->set_button($resetbutton . $button);
} else {
    $USER->editing = $edit = 0;
}

// Prepare Dashboard Data
$templatecontext = [
    'username' => fullname($USER),
    'completedCourses' => 0,
    'totalCourses' => 0,
    'totalOverdue' => 0,
    'totalPoints' => 0,
    'totalPossiblePoints' => 0,
    'learningPathPercentage' => 0,
    'overduePercentage' => 0,
    'hoursActivity' => json_encode([]),
    'percentageChange' => 0,
    'isIncrease' => true,
    'currentWeekTotal' => 0,
    'previousWeekTotal' => 0,
    'courses' => []
];

if (isloggedin() && !isguestuser()) {
    global $DB, $USER, $OUTPUT, $CFG;

    $userid = $USER->id;
    $displayname = fullname($USER);
    $userpicture = $OUTPUT->user_picture($USER, ['size' => 70]);
    $imageurl = $OUTPUT->image_url('Asset1', 'theme_academi');
    $courseenrolledgif = $OUTPUT->image_url('graduate', 'theme_academi')->out();
    $awardgif = $OUTPUT->image_url('award', 'theme_academi')->out();

    // Reset user variables to ensure they are initialized for this query
    $DB->execute("SET @prev_time = NULL, @prev_user = NULL, @session_id = 0");

    // Current Week Activity
 // Current Week Activity
$currentWeekSql = "
WITH RECURSIVE week_days AS (
    SELECT DATE_SUB(CURDATE(), INTERVAL (DAYOFWEEK(CURDATE()) - 2) DAY) AS week_day
    UNION ALL
    SELECT DATE_ADD(week_day, INTERVAL 1 DAY)
    FROM week_days
    WHERE week_day < DATE_SUB(CURDATE(), INTERVAL (DAYOFWEEK(CURDATE()) - 8) DAY)
),
sessions AS (
    SELECT 
        log.userid,
        log.timecreated,
        @session_id := IF(@prev_user = log.userid AND (log.timecreated > @prev_time) AND (log.timecreated - @prev_time) <= 1800, 
                          @session_id, 
                          @session_id + 1) AS session_id,
        IF(@prev_user = log.userid AND (log.timecreated > @prev_time) AND (log.timecreated - @prev_time) <= 1800, 
           log.timecreated - @prev_time, 
           0) AS time_spent,
        @prev_time := log.timecreated,
        @prev_user := log.userid
    FROM mdl_logstore_standard_log AS log,
         (SELECT @prev_time := NULL, @prev_user := NULL, @session_id := 0) AS vars
    WHERE log.userid = ?
    AND YEARWEEK(FROM_UNIXTIME(log.timecreated), 1) = YEARWEEK(CURDATE(), 1)
    ORDER BY log.userid, log.timecreated ASC
),
daily_hours AS (
    SELECT 
        DAYNAME(week_days.week_day) AS day_name,
        ROUND(COALESCE(SUM(activity.time_spent) / 3600, 0), 2) AS hours_spent
    FROM week_days
    LEFT JOIN (
        SELECT DATE(FROM_UNIXTIME(timecreated)) AS activity_date, time_spent
        FROM sessions
    ) AS activity
    ON activity.activity_date = week_days.week_day
    GROUP BY week_days.week_day
)
SELECT 
    day_name, 
    hours_spent, 
    (SELECT ROUND(SUM(hours_spent), 2) FROM daily_hours) AS total_week_hours
FROM daily_hours
ORDER BY FIELD(day_name, 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
";
try {
$currentWeekData = $DB->get_records_sql($currentWeekSql, [$userid]);
} catch (dml_exception $e) {
error_log("Error executing current week query: " . $e->getMessage());
$currentWeekData = [];
}

// Process the weekly activity using the day names as returned (e.g., "Sunday", "Monday", etc.)
$hoursActivity = [];
$currentWeekTotal = 0;
foreach (['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day) {
$hours = isset($currentWeekData[$day]) ? (float)$currentWeekData[$day]->hours_spent : 0;
$hoursActivity[] = $hours;
if (isset($currentWeekData[$day])) {
    $currentWeekTotal = (float)$currentWeekData[$day]->total_week_hours;
}
}


    // Reset user variables for the next query
    $DB->execute("SET @prev_time = NULL, @prev_user = NULL, @session_id = 0");

    // Previous Week Activity
    $previousWeekSql = "
        WITH RECURSIVE week_days AS (
            SELECT DATE_SUB(DATE_SUB(CURDATE(), INTERVAL (DAYOFWEEK(CURDATE()) - 2) DAY), INTERVAL 1 WEEK) AS week_day
            UNION ALL
            SELECT DATE_ADD(week_day, INTERVAL 1 DAY)
            FROM week_days
            WHERE week_day < DATE_SUB(DATE_SUB(CURDATE(), INTERVAL (DAYOFWEEK(CURDATE()) - 8) DAY), INTERVAL 1 WEEK)
        ),
        sessions AS (
            SELECT 
                log.userid,
                log.timecreated,
                @session_id := IF(@prev_user = log.userid AND (log.timecreated > @prev_time) AND (log.timecreated - @prev_time) <= 1800, 
                                  @session_id, 
                                  @session_id + 1) AS session_id,
                IF(@prev_user = log.userid AND (log.timecreated > @prev_time) AND (log.timecreated - @prev_time) <= 1800, 
                   log.timecreated - @prev_time, 
                   0) AS time_spent,
                @prev_time := log.timecreated,
                @prev_user := log.userid
            FROM mdl_logstore_standard_log AS log,
                 (SELECT @prev_time := NULL, @prev_user := NULL, @session_id := 0) AS vars
            WHERE log.userid = ?
            AND YEARWEEK(FROM_UNIXTIME(log.timecreated), 1) = YEARWEEK(DATE_SUB(CURDATE(), INTERVAL 1 WEEK), 1)
            ORDER BY log.userid, log.timecreated ASC
        ),
        daily_hours AS (
            SELECT 
                DAYNAME(week_days.week_day) AS day_name,
                ROUND(COALESCE(SUM(activity.time_spent) / 3600, 0), 2) AS hours_spent
            FROM week_days
            LEFT JOIN (
                SELECT DATE(FROM_UNIXTIME(timecreated)) AS activity_date, time_spent
                FROM sessions
            ) AS activity
            ON activity.activity_date = week_days.week_day
            GROUP BY week_days.week_day
        )
        SELECT 
            (SELECT ROUND(SUM(hours_spent), 2) FROM daily_hours) AS total_week_hours;
    ";
    try {
        $previousWeekData = $DB->get_record_sql($previousWeekSql, [$userid]);
    } catch (dml_exception $e) {
        error_log("Error executing previous week query: " . $e->getMessage());
        $previousWeekData = (object)['total_week_hours' => 0];
    }

    $previousWeekTotal = $previousWeekData->total_week_hours ?? 0;

    // Debug: Log the previous week total
    error_log("Previous Week Total: " . $previousWeekTotal);

    $percentageChange = ($previousWeekTotal > 0) ? round((($currentWeekTotal - $previousWeekTotal) / $previousWeekTotal) * 100, 2) : 0;
    $isIncrease = $percentageChange >= 0;

    // User Progress Data
    $sql = "
        WITH UserID AS (
            SELECT id AS userid FROM mdl_user WHERE id = ?
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
    $userData = $DB->get_record_sql($sql, [$USER->id]);

    $totalCourses = $userData->total_courses_assigned ?? 0;
    $completedCourses = $userData->total_courses_completed ?? 0;
    $totalOverdue = $userData->total_courses_overdue ?? 0;
    $totalPoints = $userData->total_points_earned ?? 0;
    $totalPossiblePoints = $userData->total_possible_points ?? 0;

    $formattedTotalPoints = rtrim(rtrim(number_format($totalPoints, 2, '.', ''), '0'), '.');
    $formattedTotalPossiblePoints = rtrim(rtrim(number_format($totalPossiblePoints, 2, '.', ''), '0'), '.');

    $learningPathPercentage = ($totalCourses > 0) ? round(($completedCourses / $totalCourses) * 100, 2) : 0;
    $overduePercentage = ($totalCourses > 0) ? round(($totalOverdue / $totalCourses) * 100, 2) : 0;

    // Course Progress Data
    $sql = "
        WITH UserID AS (
            SELECT id AS userid FROM mdl_user WHERE id = ?
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
    $userCourses = $DB->get_records_sql($sql, [$USER->id]);
    $courses_data = [];
    foreach ($userCourses as $course) {
        $earned_points = (int)($course->earned_points ?? 0);
        $total_points = (int)($course->total_points_assigned ?? 1);
        $percentage = (int)round(($earned_points / $total_points) * 100, 0);
        $bar_color = $percentage >= 70 ? "#204070" : ($percentage >= 40 ? "#3C6894" : "#808080");

        $courses_data[] = [
            'course_name' => $course->course_name,
            'earned_points' => $earned_points,
            'total_points' => $total_points,
            'percentage' => $percentage,
            'bar_color' => $bar_color,
            'points_display' => "$earned_points/$total_points"
        ];
    }

    // Assign to template context
    $templatecontext = [
        'username' => $displayname,
        'userpicture' => $userpicture,
        'completedCourses' => $completedCourses,
        'totalCourses' => $totalCourses,
        'totalOverdue' => $totalOverdue,
        'totalPoints' => $formattedTotalPoints,
        'totalPossiblePoints' => $formattedTotalPossiblePoints,
        'learningPathPercentage' => $learningPathPercentage,
        'overduePercentage' => $overduePercentage,
        'asset1_image_url' => $imageurl,
        'course_enrolled_url' => $courseenrolledgif,
        'course_award_url' => $awardgif,
        'hoursActivity' => json_encode($hoursActivity),
        'percentageChange' => abs($percentageChange),
        'isIncrease' => $isIncrease,
        'currentWeekTotal' => round($currentWeekTotal, 2),
        'previousWeekTotal' => round($previousWeekTotal, 2),
        'courses' => $courses_data
    ];
}

echo $OUTPUT->header();

if (core_userfeedback::should_display_reminder()) {
    core_userfeedback::print_reminder_block();
}

if (has_capability('moodle/site:manageblocks', context_system::instance())) {
    echo $OUTPUT->addblockbutton('content');
}

echo $OUTPUT->render_from_template('core/dashboard', $templatecontext);

echo $OUTPUT->custom_block_region('content');

echo $OUTPUT->footer();

$eventparams = array('context' => $context);
$event = \core\event\dashboard_viewed::create($eventparams);
$event->trigger();