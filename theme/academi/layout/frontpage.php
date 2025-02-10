<?php
defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/includes/layoutdata.php');
require_once(dirname(__FILE__) . '/includes/homeslider.php');

$PAGE->requires->css(new moodle_url('/theme/academi/style/slick.css'));
$PAGE->requires->js_call_amd('theme_academi/frontpage', 'init'); // ✅ Load frontend.js

$bodyattributes = $OUTPUT->body_attributes($extraclasses);

$jumbotronclass = (!empty(theme_academi_get_setting('jumbotronstatus'))) ? 'jumbotron-element' : '';
////////////////////////////////////////////////////////////////////////////////////
require_login();
global $DB, $USER, $OUTPUT;

if (isloggedin() && !isguestuser()) {
    $userid = $USER->id;
    $username = fullname($USER);
    $userpicture = $OUTPUT->user_picture($USER, ['size' => 100]);

    // Fetch total assigned courses
    $totalCourses = $DB->count_records('course_completions', ['userid' => $userid]);

    // Fetch completed courses
    $completedCourses = $DB->count_records_sql("
        SELECT COUNT(id) FROM {course_completions} 
        WHERE userid = ? AND timecompleted IS NOT NULL", [$userid]);

    // Fetch course completion percentage
    $completionPercentage = ($totalCourses > 0) 
        ? round(($completedCourses / $totalCourses) * 100, 2) 
        : 0;

    // Fetch total earned points
    $totalPoints = $DB->get_field_sql("
        SELECT SUM(finalgrade) FROM {grade_grades} 
        WHERE userid = ?", [$userid]);

    $totalPoints = $totalPoints ? round($totalPoints, 2) : 0;

    // ✅ Ensure values are available for JavaScript
    $PAGE->requires->js_init_code("
        window.M = window.M || {};
        window.M.cfg = {
            totalCourses: $totalCourses,
            completedCourses: $completedCourses,
            completionPercentage: $completionPercentage,
            totalPoints: $totalPoints
        };
    ");

    $templatecontext += [
        'isloggedin' => true,
        'username' => $username,
        'userpicture' => $userpicture,
        'completedCourses' => $completedCourses,
        'totalCourses' => $totalCourses,
        'completionPercentage' => $completionPercentage,
        'totalPoints' => $totalPoints
    ];
} else {
    $templatecontext['isloggedin'] = false;
}
///////////////////////////////////////////////////////////////////////////////
$templatecontext += $sliderconfig;
$templatecontext += [
    'bodyattributes' => $bodyattributes,
    'jumbotronclass' => $jumbotronclass,
];

echo $OUTPUT->render_from_template('theme_academi/frontpage', $templatecontext);
