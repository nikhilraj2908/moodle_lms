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
// Block 11 info.
$name = 'theme_almondb/block11info';
$heading = get_string('block11info', 'theme_almondb');
$information = get_string('block11infodesc', 'theme_almondb');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);
// Enable or disable block 11 settings.
$name = 'theme_almondb/block11enabled';
$title = get_string('block11enabled', 'theme_almondb');
$description = get_string('block11enableddesc', 'theme_almondb');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$page->add($setting);
// Block 11 design select.
$name = 'theme_almondb/block11design';
$title = get_string('block11design', 'theme_almondb');
$description = get_string('block11designdesc', 'theme_almondb');
$default = 1;
$options = [];
for ($i = 1; $i <= 2; $i++) {
     $options[$i] = $i;
}
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Count block 11 settings.
$name = 'theme_almondb/block11count';
$title = get_string('block11count', 'theme_almondb');
$description = get_string('block11countdesc', 'theme_almondb');
$default = 3;
$options = [];
for ($i = 2; $i <= 10; $i++) {
     $options[$i] = $i;
}
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// If we don't have an slide yet, default to the preset.
$count = get_config('theme_almondb', 'block11count');
// Block 11 header text.
$name = 'theme_almondb/block11header';
$title = get_string('block11header', 'theme_almondb');
$description = get_string('block11headerdesc', 'theme_almondb');
$default = get_string('block11headerdefault', 'theme_almondb');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$page->add($setting);
// Block 11 caption text.
$name = 'theme_almondb/block11caption';
$title = get_string('block11caption', 'theme_almondb');
$description = get_string('block11captiondesc', 'theme_almondb');
$default = get_string('block11captiondefault', 'theme_almondb');
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '2');;
$page->add($setting);
