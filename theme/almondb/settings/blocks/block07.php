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
 * Parent theme: boost
 *
 * @package   theme_almondb
 * @copyright 2022 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
// Block 07 info.
$name = 'theme_almondb/block07info';
$heading = get_string('block07info', 'theme_almondb');
$information = get_string('block07infodesc', 'theme_almondb');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);
// Enable or disable block 07 settings.
$name = 'theme_almondb/block07enabled';
$title = get_string('block07enabled', 'theme_almondb');
$description = get_string('block07enableddesc', 'theme_almondb');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$page->add($setting);
// Block 07 design select.
$name = 'theme_almondb/block07design';
$title = get_string('block07design', 'theme_almondb');
$description = get_string('block07designdesc', 'theme_almondb');
$default = 1;
$options = [];
for ($i = 1; $i <= 5; $i++) {
     $options[$i] = $i;
}
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 07 count text.
$name = 'theme_almondb/block07count';
$title = get_string('block07count', 'theme_almondb');
$description = get_string('block07countdesc', 'theme_almondb');
$default = get_string('block07countdefault', 'theme_almondb');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT, '2');
$page->add($setting);
// Block 07 teacher role.
$options = [];
$role = $DB->get_records('role');
foreach ($role as $roles) {
     $options[$roles->id] = $roles->shortname;
}
$name = 'theme_almondb/block07teacherrole';
$title = get_string('block07teacherrole', 'theme_almondb');
$description = get_string('block07teacherroledesc', 'theme_almondb');
$default = 'editingteacher';
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 07 student role.
$name = 'theme_almondb/block07studentrole';
$title = get_string('block07studentrole', 'theme_almondb');
$description = get_string('block07studentroledesc', 'theme_almondb');
$default = 'student';
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Teacher enabled/disabled .
$name = 'theme_almondb/block07teacherenabled';
$title = get_string('block07teacherenabled', 'theme_almondb');
$description = get_string('block07teacherenableddesc', 'theme_almondb');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$page->add($setting);
// Show price .
$name = 'theme_almondb/block07priceshow';
$title = get_string('block07priceshow', 'theme_almondb');
$description = get_string('block07priceshowdesc', 'theme_almondb');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$page->add($setting);
// Block 07 tile select.
$name = 'theme_almondb/block07title';
$title = get_string('block07title', 'theme_almondb');
$description = get_string('block07titledesc', 'theme_almondb');
$default = "shortname";
$options = [
'shortname' => 'shortname',
'fullname' => 'fullname',
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 07 header text.
$name = 'theme_almondb/block07header';
$title = get_string('block07header', 'theme_almondb');
$description = get_string('block07headerdesc', 'theme_almondb');
$default = get_string('block07headerdefault', 'theme_almondb');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$page->add($setting);
// Block 07 button.
$name = 'theme_almondb/block07button';
$title = get_string('block07button', 'theme_almondb');
$description = get_string('block07buttondesc', 'theme_almondb');
$default = get_string('button', 'theme_almondb');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$page->add($setting);
// Block 07 button link.
$name = 'theme_almondb/block07buttonlink';
$title = get_string('block07buttonlink', 'theme_almondb');
$description = get_string('block07buttonlinkdesc', 'theme_almondb');
$default = 'course/index.php';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
$page->add($setting);
