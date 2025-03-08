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
// Block 03 info.
$name = 'theme_almondb/block03info';
$heading = get_string('block03info', 'theme_almondb');
$information = get_string('block03infodesc', 'theme_almondb');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);
// Enable or disable block 03 settings.
$name = 'theme_almondb/block03enabled';
$title = get_string('block03enabled', 'theme_almondb');
$description = get_string('block03enableddesc', 'theme_almondb');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$page->add($setting);

// Block 3 design select.
$name = 'theme_almondb/block03design';
$title = get_string('block03design', 'theme_almondb');
$description = get_string('block03designdesc', 'theme_almondb');
$default = 1;
$options = [];
for ($i = 1; $i < 4; $i++) {
     $options[$i] = $i;
}
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_almondb/block03header';
$title = get_string('block03header', 'theme_almondb');
$description = get_string('block03headerdesc', 'theme_almondb');
$description = $description.get_string('underline', 'theme_almondb');
$default = get_string('block03headerdefault', 'theme_almondb');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$page->add($setting);

// Block 03 general settings END.
// ------------------------------------------------------------------------------------.
for ($i = 1; $i <= 6; $i++) {
     // Block 03 icon .
     $name = 'theme_almondb/block03icon'.$i;
     $title = get_string('block03icon', 'theme_almondb', ['block' => $i]);
     $description = get_string('block03icondesc', 'theme_almondb');
     $default = get_string('block03icondefault'.$i, 'theme_almondb');
     $options = [];
     $options[] = theme_almondb_get_core_icon_list();
     $setting = new admin_setting_configselect($name, $title, $description, $default, $options);
     $setting->set_updatedcallback('theme_reset_all_caches');
     $page->add($setting);
     // Block 03 title.
     $name = 'theme_almondb/block03title'.$i;
     $title = get_string('block03title', 'theme_almondb', ['block' => $i]);
     $description = get_string('block03titledesc', 'theme_almondb');
     $default = 'Top Investment Advisors';
     $setting = new admin_setting_configtext($name, $title, $description, $default);
     $page->add($setting);
     // Block 03 caption.
     $name = 'theme_almondb/block03caption'.$i;
     $title = get_string('block03caption', 'theme_almondb', ['block' => $i]);
     $description = get_string('block03captiondesc', 'theme_almondb');
     $default = get_string('block03captiondefault', 'theme_almondb');
     $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
     $page->add($setting);
     // Block 03 link.
     $name = 'theme_almondb/block03link'.$i;
     $title = get_string('block03link', 'theme_almondb', ['block' => $i]);
     $description = get_string('block03linkdesc', 'theme_almondb');
     $description = $description.get_string('underline', 'theme_almondb');
     $default = get_string('buttonlink', 'theme_almondb');
     $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
     $page->add($setting);
}
