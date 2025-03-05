<?php
defined('MOODLE_INTERNAL') || die();

// Load necessary files and Moodle's global context
require_once(dirname(__FILE__) . '/includes/layoutdata.php');
require_once(dirname(__FILE__) . '/includes/homeslider.php');

$PAGE->requires->css(new moodle_url('/theme/academi/style/slick.css'));
$PAGE->requires->js_call_amd('theme_academi/frontpage', 'init');

$bodyattributes = $OUTPUT->body_attributes($extraclasses);

// Jumbotron class.
$jumbotronclass = (!empty(theme_academi_get_setting('jumbotronstatus'))) ? 'jumbotron-element' : '';

// Default Course Image
$default_image_url = $CFG->wwwroot . '/theme/academi/pix/defaultcourse.jpg';

// User Dashboard Data
require_login();
global $DB, $USER, $OUTPUT;

if (isloggedin() && !isguestuser()) {

    $userid = $USER->id;
    // We can use the username from the current user
    // If you specifically want 'nikhilraj', replace $USER->username with 'nikhilraj'
    $username = $USER->username; 
    $displayname = fullname($USER);
    $userpicture = $OUTPUT->user_picture($USER, ['size' => 100]);

    // -- NEW QUERY WITH CTE -----------------------------------------------
    // Using the logged-in user's username as parameter:
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

    // Map the returned columns to local PHP variables.
    // (Use null coalescing to handle possible NULL values.)
     // Map the returned columns to local PHP variables.
     $totalCourses = $userData->total_courses_assigned ?? 0;
     $completedCourses = $userData->total_courses_completed ?? 0;
     $totalOverdue = $userData->total_courses_overdue ?? 0;
     $totalPoints = $userData->total_points_earned ?? 0;
     $totalPossiblePoints = $userData->total_possible_points ?? 0;
 
     // Format the points to remove trailing zeros and decimal if whole number
     $formattedTotalPoints = rtrim(rtrim(number_format($totalPoints, 2, '.', ''), '0'), '.');
     $formattedTotalPossiblePoints = rtrim(rtrim(number_format($totalPossiblePoints, 2, '.', ''), '0'), '.');
 
     $learningPathPercentage = ($totalCourses > 0)
         ? round(($completedCourses / $totalCourses) * 100, 2)
         : 0;
 
     // Prepare data for the Mustache template context
     $formattedUsername = ucfirst(strtolower($displayname));
     $templatecontext += [
         'isloggedin' => true,
         'username' => $formattedUsername,
         'userpicture' => $userpicture,
         'completedCourses' => $completedCourses,
         'totalCourses' => $totalCourses,
         'totalPoints' => $formattedTotalPoints, // Use formatted value
         'learningPathPercentage' => $learningPathPercentage,
         'curriculumPercentage' => $learningPathPercentage,
         'totalPossiblePoints' => $formattedTotalPossiblePoints, // Use formatted value
         'totalOverdue' => $totalOverdue,
     ];
     
    $courses = $DB->get_records_sql("
        SELECT 
            c.id AS course_id,
            c.fullname AS course_name,
            c.shortname AS course_shortname,
            c.summary AS course_summary,
            c.startdate AS course_startdate,
            c.enddate AS course_enddate,
            c.visible AS course_visible,
            cc.name AS category_name,
            f.filename AS course_image_filename,
            f.filepath AS course_image_filepath,
            f.mimetype AS course_image_mimetype,
            CONCAT('/pluginfile.php/', ctx.id, '/course/overviewfiles/', f.filename) AS course_image_url
        FROM mdl_course c
        JOIN mdl_enrol e ON e.courseid = c.id
        JOIN mdl_user_enrolments ue ON ue.enrolid = e.id
        LEFT JOIN mdl_course_categories cc ON c.category = cc.id
        LEFT JOIN mdl_context ctx ON c.id = ctx.instanceid AND ctx.contextlevel = 50
        LEFT JOIN mdl_files f ON ctx.id = f.contextid 
                   AND f.component = 'course' 
                   AND f.filearea = 'overviewfiles' 
                   AND f.filename <> '.'
        WHERE ue.userid = ?
        AND c.visible = 1
        ORDER BY c.fullname ASC
    ", [$userid]);

    // Prepare courses data to be passed to the template
    $courses_data = [];
    foreach ($courses as $course) {
        // Check if course image exists; otherwise, use the default image
        $course_image_url = (!empty($course->course_image_filename))
            ? $CFG->wwwroot . $course->course_image_url
            : $default_image_url;

        $startdate = ($course->course_startdate) 
            ? date('Y-m-d', $course->course_startdate) 
            : 'N/A';
        $enddate = ($course->course_enddate) 
            ? date('Y-m-d', $course->course_enddate) 
            : 'N/A';

        $courses_data[] = [
            'course_id' => $course->course_id,
            'course_name' => $course->course_name,
            'course_shortname' => $course->course_shortname,
            'course_summary' => strip_tags($course->course_summary),
            'course_image_url' => $course_image_url,
            'course_url' => new moodle_url('/course/view.php', ['id' => $course->course_id]),
            'course_startdate' => $startdate,
            'course_enddate' => $enddate,
            'category_name' => $course->category_name,
        ];
    }
    $templatecontext['courses'] = $courses_data;
    
    $templatecontext['alert_gif'] = $OUTPUT->image_url('alert', 'theme_academi')->out(false);


    $is_admin = is_siteadmin($USER->id);
    $showpopup = false;
    $popupmessage = "";
    
    if ($is_admin) {
        // ✅ Fetch last uploaded course
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
    
    // ✅ Pass the popup variables to Mustache
    $templatecontext['bodyattributes'] = $bodyattributes;
    $templatecontext['jumbotronclass'] = $jumbotronclass;
    $templatecontext['showpopup'] = $showpopup;
    $templatecontext['popupmessage'] = $popupmessage;
} else {
    $templatecontext['isloggedin'] = false;
}

$templatecontext['sitefeatures'] = (new \theme_academi\academi_blocks())->sitefeatures();
$templatecontext += $sliderconfig;
$templatecontext += [
    'bodyattributes' => $bodyattributes,
    'jumbotronclass' => $jumbotronclass,
];

// Render the template with the context data
echo $OUTPUT->render_from_template('theme_academi/frontpage', $templatecontext);
