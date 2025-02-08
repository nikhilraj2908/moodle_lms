<?php
require_once('../config.php'); // Load Moodle config
require_login(); // Ensure user is logged in

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/customfiles/user_dashboard.php');
$PAGE->set_title("User Dashboard");
$PAGE->set_heading("User Dashboard");

global $DB, $USER;

// Get user details
$userid = $USER->id;
$username = fullname($USER);
$userpicture = $OUTPUT->user_picture($USER, array('size' => 100));

// Fetch course progress (Activity Completion Based)
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

// Ensure points are not NULL
$totalPoints = $totalPoints ? round($totalPoints, 2) : 0;

// Convert data for Chart.js
$courseCompletionJson = json_encode(array_values($courseCompletionData));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Moodle</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            text-align: center;
            background-color: #f4f4f4;
        }
        .dashboard-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
            position: relative;
        }
        .progress-container {
            position: relative;
            width: 100px;
            height: 100px;
            margin: auto;
        }
        canvas {
            width: 100px;
            height: 100px;
        }
        .progress-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 18px;
            font-weight: bold;
        }
        .points-display {
            font-size: 20px;
            font-weight: bold;
            color: #ffcc00;
            text-align: center;
            margin-top: 20px;
        }
        .user-info {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-bottom: 20px;
        }
        .user-info img {
            border-radius: 50%;
            width: 60px;
        }
    </style>
</head>
<body>

    <div class="user-info">
        <?php echo $userpicture; ?>
        <div>
            <h2>Welcome <?php echo $username; ?></h2>
            <p>A learning curve is essential to growth!</p>
        </div>
    </div>

    <div class="dashboard-container">
        <!-- Learning Path Card -->
        <div class="card">
            <h3>Learning Path</h3>
            <p><b><?php echo $completedCourses; ?></b> Completed / <b><?php echo $totalCourses; ?></b> Assigned</p>
            <div class="progress-container">
                <canvas id="learningPathChart"></canvas>
                <div class="progress-text" id="learningPathText">0%</div>
            </div>
        </div>

        <!-- Curriculum Card -->
        <div class="card">
            <h3>Curriculum</h3>
            <p><b>0</b> Overdue / <b><?php echo $completedCourses; ?></b> Completed / <b><?php echo $totalCourses; ?></b> Assigned</p>
            <div class="progress-container">
                <canvas id="curriculumChart"></canvas>
                <div class="progress-text" id="curriculumText">0%</div>
            </div>
        </div>

        <!-- Points Earned Card -->
        <div class="card">
            <h3>Total Points Earned</h3>
            <p>Accumulated points from all completed courses</p>
            <div class="progress-container">
                <canvas id="pointsChart"></canvas>
                <div class="progress-text" id="pointsText"><?php echo $totalPoints; ?></div>
            </div>
            <p class="points-display"><i class="fas fa-star"></i> <b><?php echo $totalPoints; ?></b> Points</p>
        </div>
    </div>

    <script>
        // Dynamic Completion Data
        const courseCompletionData = <?php echo $courseCompletionJson; ?>;
        const learningPathPercentage = courseCompletionData.length > 0 ? courseCompletionData[0].completion_percentage : 0;
        const curriculumPercentage = <?php echo ($totalCourses > 0) ? round(($completedCourses / $totalCourses) * 100, 2) : 0; ?>;
        const pointsValue = <?php echo $totalPoints; ?>;

        // Function to create gauge charts and display percentage in center
        function createGaugeChart(canvasId, textId, value) {
            let ctx = document.getElementById(canvasId).getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Completed', 'Remaining'],
                    datasets: [{
                        data: [value, 100 - value],
                        backgroundColor: ['green', 'blue']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: { display: false }
                    }
                }
            });

            // Set text value in center
            document.getElementById(textId).innerText = value + "%";
        }

        // Create Charts
        createGaugeChart('learningPathChart', 'learningPathText', learningPathPercentage);
        createGaugeChart('curriculumChart', 'curriculumText', curriculumPercentage);

        // Points Chart
        let ctxPoints = document.getElementById('pointsChart').getContext('2d');
        new Chart(ctxPoints, {
            type: 'doughnut',
            data: {
                labels: ['Points Earned'],
                datasets: [{
                    data: [pointsValue, 100 - pointsValue],
                    backgroundColor: ['gold', 'gray']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { display: false }
                }
            }
        });
    </script>

</body>
</html>
