<?php
defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/includes/layoutdata.php');
require_once(dirname(__FILE__) . '/includes/homeslider.php');

$PAGE->requires->css(new moodle_url('/theme/academi/style/slick.css'));
$PAGE->requires->js_call_amd('theme_academi/frontpage', 'init');

$bodyattributes = $OUTPUT->body_attributes($extraclasses);

// Jumbotron class.
$jumbotronclass = (!empty(theme_academi_get_setting('jumbotronstatus'))) ? 'jumbotron-element' : '';

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
        'username' =>  $formattedUsername ,
        'userpicture' => $userpicture,
        'completedCourses' => $completedCourses,
        'totalCourses' => $totalCourses,
        'totalPoints' => $totalPoints,
        'learningPathPercentage' => ($totalCourses > 0) ? round(($completedCourses / $totalCourses) * 100, 2) : 0,
        'curriculumPercentage' => ($totalCourses > 0) ? round(($completedCourses / $totalCourses) * 100, 2) : 0
    ];
    

} else {
    $templatecontext['isloggedin'] = false;
}

$templatecontext += $sliderconfig;
$templatecontext += [
    'bodyattributes' => $bodyattributes,
    'jumbotronclass' => $jumbotronclass,
];

echo $OUTPUT->render_from_template('theme_academi/frontpage', $templatecontext);
