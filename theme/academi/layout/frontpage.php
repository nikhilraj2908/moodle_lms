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

    // Fetch course progress
    $courseCompletionData = $DB->get_records_sql("
        SELECT c.id AS course_id, 
               c.fullname AS course_name, 
               ROUND((COUNT(cmc.id) / COUNT(cm.id)) * 100, 2) AS completion_percentage
        FROM {course_modules} cm
        LEFT JOIN {course_modules_completion} cmc 
            ON cm.id = cmc.coursemoduleid AND cmc.userid = ?
        JOIN {course} c ON cm.course = c.id
        WHERE c.id IN (SELECT course FROM {course_completions} WHERE userid = ?)
        GROUP BY c.id
    ", [$userid, $userid]);

    // Fetch total assigned courses
    $totalCourses = $DB->count_records('course_completions', ['userid' => $userid]);

    // Fetch completed courses
    $completedCourses = $DB->count_records_sql("
        SELECT COUNT(id) FROM {course_completions} 
        WHERE userid = ? AND timecompleted IS NOT NULL", [$userid]);

    // Fetch total earned points
    $totalPoints = $DB->get_field_sql("
        SELECT SUM(finalgrade) FROM {grade_grades} 
        WHERE userid = ?", [$userid]);

    $totalPoints = $totalPoints ? round($totalPoints, 2) : 0;

    $templatecontext += [
        'isloggedin' => true,
        'username' => $username,
        'userpicture' => $userpicture,
        'completedCourses' => $completedCourses,
        'totalCourses' => $totalCourses,
        'totalPoints' => $totalPoints
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
