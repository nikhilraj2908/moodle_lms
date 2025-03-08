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
// Block 10 info.
$name = 'theme_almondb/block10info';
$heading = get_string('block10info', 'theme_almondb');
$information = get_string('block10infodesc', 'theme_almondb');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);
// Enable or disable block 10 settings.
$name = 'theme_almondb/block10enabled';
$title = get_string('block10enabled', 'theme_almondb');
$description = get_string('block10enableddesc', 'theme_almondb');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$page->add($setting);
// Block 10 design select.
$name = 'theme_almondb/block10design';
$title = get_string('block10design', 'theme_almondb');
$description = get_string('block10designdesc', 'theme_almondb');
$default = 1;
$options = [];
for ($i = 1; $i < 3; $i++) {
     $options[$i] = $i;
}
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Count block 2 settings.
$name = 'theme_almondb/block10count';
$title = get_string('block10count', 'theme_almondb');
$description = get_string('block10countdesc', 'theme_almondb');
$default = 3;
$options = [];
for ($i = 2; $i < 9; $i++) {
     $options[$i] = $i;
}
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// If we don't have an slide yet, default to the preset.
$count = get_config('theme_almondb', 'block10count');
// Block 10 header text.
$name = 'theme_almondb/block10header';
$title = get_string('block10header', 'theme_almondb');
$description = get_string('block10headerdesc', 'theme_almondb');
$description = $description.get_string('underline', 'theme_almondb');
$default = get_string('block10headerdefault', 'theme_almondb');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$page->add($setting);
// Block 10 general settings END.
// ------------------------------------------------------------------------------------.
for ($i = 1; $i < $count + 1; $i++) {
     // Block 10 img.
     $name = 'theme_almondb/sliderimageblock10img'.$i;
     $title = get_string('sliderimageblock10img', 'theme_almondb', ['block' => $i]);
     $description = get_string('block10imgdesc', 'theme_almondb');
     $setting = new admin_setting_configstoredfile($name, $title, $description, 'sliderimageblock10img'.$i);
     $setting->set_updatedcallback('theme_reset_all_caches');
     $page->add($setting);
     // Block 10 name.
     $name = 'theme_almondb/block10name'.$i;
     $title = get_string('block10name', 'theme_almondb', ['block' => $i]);
     $description = get_string('block10namedesc', 'theme_almondb');
     $default = "";
     $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
     $page->add($setting);
     // Block 10 job.
     $name = 'theme_almondb/block10job'.$i;
     $title = get_string('block10job', 'theme_almondb', ['block' => $i]);
     $description = get_string('block10jobdesc', 'theme_almondb');
     $default = "";
     $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
     $page->add($setting);
     // Block 10 caption.
     $name = 'theme_almondb/block10caption'.$i;
     $title = get_string('block10caption', 'theme_almondb', ['block' => $i]);
     $description = get_string('block10captiondesc', 'theme_almondb');
     $default = "";
     $setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '3');
     $page->add($setting);
     // Block 10 button link.
     $name = 'theme_almondb/block10link'.$i;
     $title = get_string('block10link', 'theme_almondb', ['block' => $i]);
     $description = get_string('block10linkdesc', 'theme_almondb');
     $description = $description.get_string('underline', 'theme_almondb');
     $default = get_string('buttonlink', 'theme_almondb');
     $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
     $page->add($setting);
}
