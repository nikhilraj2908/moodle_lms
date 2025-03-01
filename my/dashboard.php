<?php
require_once(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/my/lib.php');
require 'vendor/autoload.php';

require_login();
global $DB, $USER, $OUTPUT, $CFG;

// Get user details
$userid = $USER->id;
$username = fullname($USER);
$userpicture = $OUTPUT->user_picture($USER, ['size' => 100]);

$imageurl = $OUTPUT->pix_url('Asset1', 'theme_academi');
$templatecontext['asset1_image_url'] = $imageurl;
// Use the provided CTE query to fetch user progress & points

$imageurl = $OUTPUT->pix_url('Asset1', 'theme_academi');
    
$sql = "
WITH UserID AS (
    SELECT id AS userid FROM {user} WHERE username = ?
),
TotalCourses AS (
    SELECT COUNT(c.id) AS total_assigned_courses
    FROM {course} c
    JOIN {enrol} e ON c.id = e.courseid
    JOIN {user_enrolments} ue ON e.id = ue.enrolid
    WHERE ue.userid = (SELECT userid FROM UserID)
),
CompletedCourses AS (
    SELECT COUNT(DISTINCT cm.course) AS total_completed_courses
    FROM {course_modules_completion} cmc
    JOIN {course_modules} cm ON cmc.coursemoduleid = cm.id
    WHERE cmc.userid = (SELECT userid FROM UserID) AND cmc.completionstate = 1
),
TotalPoints AS (
    SELECT COALESCE(SUM(g.finalgrade), 0) AS total_points_earned
    FROM {grade_grades} g
    WHERE g.userid = (SELECT userid FROM UserID)
),
MaxPoints AS (
    SELECT COALESCE(SUM(g.rawgrademax), 0) AS max_total_points
    FROM {grade_grades} g
    WHERE g.userid = (SELECT userid FROM UserID)
)
SELECT 
    (SELECT total_assigned_courses FROM TotalCourses) AS total_courses_assigned,
    (SELECT total_completed_courses FROM CompletedCourses) AS total_courses_completed,
    ((SELECT total_assigned_courses FROM TotalCourses) - (SELECT total_completed_courses FROM CompletedCourses)) AS total_courses_overdue,
    (SELECT total_points_earned FROM TotalPoints) AS total_points_earned,
    (SELECT max_total_points FROM MaxPoints) AS total_possible_points
FROM (SELECT 1) AS dummy
";

// Pass the logged-in user's username as the parameter
$userData = $DB->get_record_sql($sql, [$USER->username]);

// Assign fetched data with appropriate fallbacks
$totalCourses      = $userData->total_courses_assigned   ?? 0;
$completedCourses  = $userData->total_courses_completed    ?? 0;
$totalOverdue      = $userData->total_courses_overdue      ?? 0;
$totalPoints       = round($userData->total_points_earned ?? 0, 2);
$maxTotalPoints    = round($userData->total_possible_points ?? 0, 2);
$progressPercentage = ($totalCourses > 0) ? round(($completedCourses / $totalCourses) * 100, 2) : 0;
$formattedUsername = ucfirst(strtolower($username));

// Prepare the template context with only the progress-related details
$templatecontext = [
    'isloggedin'            => isloggedin() && !isguestuser(),
    'username'              => $formattedUsername,
    'userpicture'           => $userpicture,
    'completedCourses'      => $completedCourses,
    'totalCourses'          => $totalCourses,
    'totalOverdueCourses'   => $totalOverdue,
    'totalPoints'           => $totalPoints,
    'maxTotalPoints'        => $maxTotalPoints,
    'learningPathPercentage'=> $progressPercentage,
    'asset1_image_url'=>$imageurl
];


// Load and render the Mustache template (e.g., "dashboard.mustache")
$mustache = new Mustache_Engine([
    'loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__) . '/templates'),
]);

echo $OUTPUT->header();
echo $mustache->render('dashboard', $templatecontext);
echo $OUTPUT->footer();

// Trigger dashboard viewed event
$event = \core\event\dashboard_viewed::create(['context' => context_user::instance($userid)]);
$event->trigger();
?>

