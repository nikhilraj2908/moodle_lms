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
 * Theme almondb block01.
 *
 * @package   theme_almondb
 * @copyright 2022 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
    // Block 01 info.
    $name = 'theme_almondb/block01info';
    $heading = get_string('block01info', 'theme_almondb');
    $information = get_string('block01infodesc', 'theme_almondb');
    $setting = new admin_setting_heading($name, $heading, $information);
    $page->add($setting);

    // Enable or disable block 01 settings.
    $name = 'theme_almondb/block01enabled';
    $title = get_string('block01enabled', 'theme_almondb');
    $description = get_string('block01enableddesc', 'theme_almondb');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 1);
    $page->add($setting);
    // Block 01 background color.
    $name = 'theme_almondb/block01color';
    $title = get_string('block01color', 'theme_almondb');
    $description = get_string('block01colordesc', 'theme_almondb');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 01 caption.
    $name = 'theme_almondb/block01caption';
    $title = get_string('block01caption', 'theme_almondb');
    $description = get_string('block01captiondesc', 'theme_almondb');
    $default = get_string('block01captiondefault', 'theme_almondb');
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $page->add($setting);
    // Block 01 button.
    $name = 'theme_almondb/block01button';
    $title = get_string('block01button', 'theme_almondb');
    $description = get_string('block01buttondesc', 'theme_almondb');
    $default = get_string('button', 'theme_almondb');
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $page->add($setting);
    // Block 01 button link.
    $name = 'theme_almondb/block01buttonlink';
    $title = get_string('block01buttonlink', 'theme_almondb');
    $description = get_string('block01buttonlinkdesc', 'theme_almondb');
    $default = 'http://www.example.com/';
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
    $page->add($setting);
