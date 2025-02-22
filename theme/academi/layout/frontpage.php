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

// Default Course Image (Ensure this file exists inside /theme/academi/pix/)
$default_image_url = $CFG->wwwroot . '/theme/academi/pix/defaultcourse.jpg';

// User Dashboard Data
require_login();
global $DB, $USER, $OUTPUT;

if (isloggedin() && !isguestuser()) {
    $userid = $USER->id;
    $username = fullname($USER);
    $userpicture = $OUTPUT->user_picture($USER, ['size' => 100]);

    // Fetch user progress, assigned courses, completed courses, and points
    $userData = $DB->get_record_sql("
        SELECT 
            COUNT(DISTINCT ue.id) AS total_assigned_courses,
            COUNT(DISTINCT cc.id) AS completed_courses,
            (COUNT(DISTINCT cc.id) / COUNT(DISTINCT ue.id)) * 100 AS progress_percentage,
            SUM(g.finalgrade) AS total_points
        FROM mdl_user u
        LEFT JOIN mdl_user_enrolments ue ON u.id = ue.userid
        LEFT JOIN mdl_enrol e ON ue.enrolid = e.id
        LEFT JOIN mdl_course c ON e.courseid = c.id
        LEFT JOIN mdl_course_completions cc ON u.id = cc.userid AND cc.timecompleted IS NOT NULL
        LEFT JOIN mdl_grade_grades g ON u.id = g.userid
        WHERE u.id = ?
    ", [$userid]);

    // Assign fetched data to variables
    $totalCourses = $userData->total_assigned_courses ?? 0;
    $completedCourses = $userData->completed_courses ?? 0;
    $progressPercentage = round($userData->progress_percentage ?? 0, 2);
    $totalPoints = round($userData->total_points ?? 0, 2);
    $formattedUsername = ucfirst(strtolower($username));

    $templatecontext += [
        'isloggedin' => true,
        'username' => $formattedUsername,
        'userpicture' => $userpicture,
        'completedCourses' => $completedCourses,
        'totalCourses' => $totalCourses,
        'totalPoints' => $totalPoints,
        'learningPathPercentage' => ($totalCourses > 0) ? round(($completedCourses / $totalCourses) * 100, 2) : 0,
        'curriculumPercentage' => ($totalCourses > 0) ? round(($completedCourses / $totalCourses) * 100, 2) : 0
    ];

    // Fetch the courses the user is enrolled in with all details (including image, start date, end date, category)
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

        $startdate = ($course->course_startdate) ? date('Y-m-d', $course->course_startdate) : 'N/A';
        $enddate = ($course->course_enddate) ? date('Y-m-d', $course->course_enddate) : 'N/A';

        $courses_data[] = [
            'course_id' => $course->course_id,
            'course_name' => $course->course_name,
            'course_shortname' => $course->course_shortname,
            'course_summary' => strip_tags($course->course_summary), // Removing HTML tags from summary
            'course_image_url' => $course_image_url,
            'course_url' => new moodle_url('/course/view.php', ['id' => $course->course_id]),
            'course_startdate' => $startdate,
            'course_enddate' => $enddate,
            'category_name' => $course->category_name,
        ];
    }

    // Add courses data to the template context
    $templatecontext['courses'] = $courses_data;

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
?>
