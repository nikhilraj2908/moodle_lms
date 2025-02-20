<?php
require_once('../../config.php');
require_login();

global $DB, $USER;

$PAGE->set_url(new moodle_url('/local/ai_blog/index.php'));
$PAGE->set_title('AI Blog Generator');
$PAGE->set_heading('Generate Blog with AI');
$PAGE->set_pagelayout('standard');

echo $OUTPUT->header();
?>

<form action="generate.php" method="POST">
    <label for="title">Enter Blog Title:</label>
    <input type="text" id="title" name="title" required>
    <button type="submit">Generate Blog</button>
</form>

<hr>
<h2>My AI-Generated Blogs</h2>
<?php
$blogs = $DB->get_records('ai_blog', ['userid' => $USER->id]);

foreach ($blogs as $blog) {
    echo "<h3>{$blog->title}</h3>";
    echo "<p>{$blog->content}</p>";
    echo "<hr>";
}
?>

<?php
echo $OUTPUT->footer();
?>
