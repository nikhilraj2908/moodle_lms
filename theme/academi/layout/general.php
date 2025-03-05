<?php
global $DB;

// ✅ Step 1: Debugging - Check if `general.php` is loading
echo "<p style='color: red; font-weight: bold;'>general.php is loading!</p>";

// ✅ Step 2: Fetch the last uploaded course based on 'timecreated'
$last_course = $DB->get_record_sql("SELECT id, fullname, timecreated FROM {course} ORDER BY timecreated DESC LIMIT 1");

// ✅ Step 3: Debug - Check if a course is fetched
echo "<pre>";
print_r($last_course);
echo "</pre>";

// ✅ Step 4: Initialize message
$message = ''; 

if ($last_course) {
    $last_upload_time = $last_course->timecreated;
    $current_time = time();
    
    // Convert timestamps to readable format
    $last_upload_date = date("Y-m-d H:i:s", $last_upload_time);
    $current_date = date("Y-m-d H:i:s", $current_time);

    // ✅ Step 5: Debug - Show last uploaded course time
    echo "<p><strong>Last course uploaded on:</strong> " . $last_upload_date . "</p>";
    echo "<p><strong>Current time:</strong> " . $current_date . "</p>";

    // Calculate time difference (in seconds)
    $time_difference = $current_time - $last_upload_time;
    $one_week = 7 * 24 * 60 * 60; // 1 week in seconds

    if ($time_difference >= $one_week) {
        $message = "It's been more than a week since a course was last uploaded! Please upload a new course.";
    }
}

// ✅ Step 6: Debug - Show message output before JavaScript
echo "<p style='color: blue; font-weight: bold;'>Message: " . $message . "</p>";
?>

<!-- ✅ Step 7: Fix JavaScript Alert -->
<?php if (!empty($message)) : ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var message = <?php echo json_encode($message); ?>;
            if (message) {
                alert(message);
            }
        });
    </script>
<?php endif; ?>
