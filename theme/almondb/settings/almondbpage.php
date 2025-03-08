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
 * Theme almondb page.
 *
 * @package   theme_almondb
 * @copyright 2022 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
$page = new admin_settingpage('theme_almondb_almondbpage', get_string('almondbpage', 'theme_almondb'));
$page->add(new admin_setting_heading('theme_almondb_almondbpage', get_string('almondbpageheading', 'theme_almondb'),
format_text(get_string('almondbpageheadingdesc', 'theme_almondb'), FORMAT_MARKDOWN)));
// Enable or disable page settings.
$name = 'theme_almondb/almondbpageenabled';
$title = get_string('almondbpageenabled', 'theme_almondb');
$description = get_string('almondbpageenableddesc', 'theme_almondb');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Count page settings.
$name = 'theme_almondb/almondbpagecount';
$title = get_string('almondbpagecount', 'theme_almondb');
$description = get_string('almondbpagecountdesc', 'theme_almondb');
$default = 1;
$options = [];
for ($i = 1; $i <= 10; $i++) {
    $options[$i] = $i;
}
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// If we don't have an slide yet, default to the preset.
$almondbpagecount = get_config('theme_almondb', 'almondbpagecount');
if (!$almondbpagecount) {
    $almondbpagecount = 2;
}
for ($count = 1; $count <= $almondbpagecount; $count++) {
    $name = 'theme_almondb/almondbpage' . $count . 'info';
    $heading = get_string('almondbpageno', 'theme_almondb', ['almondbpage' => $count]);
    $information = get_string('almondbpagenodesc', 'theme_almondb', ['almondbpage' => $count]);
    $setting = new admin_setting_heading($name, $heading, $information);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Page title.
    $name = 'theme_almondb/almondbpagetitle' . $count;
    $title = get_string('almondbpagetitle', 'theme_almondb');
    $description = get_string('almondbpagetitledesc', 'theme_almondb');
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_TEXT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Page caption.
    $name = 'theme_almondb/almondbpagecap' . $count;
    $title = get_string('almondbpagecaption', 'theme_almondb');
    $description = get_string('almondbpagecaptiondesc', 'theme_almondb');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Page css link.
    $name = 'theme_almondb/almondbpagecsslink' . $count;
    $title = get_string('almondbpagecsslink', 'theme_almondb');
    $description = get_string('almondbpagecsslinkdesc', 'theme_almondb');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '1');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Page img folder link.
    $name = 'theme_almondb/almondbpageimglink' . $count;
    $title = get_string('almondbpageimglink', 'theme_almondb');
    $description = get_string('almondbpageimglinkdesc', 'theme_almondb');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '1');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Page css.
    $name = 'theme_almondb/almondbpagecss' . $count;
    $title = get_string('almondbpagecss', 'theme_almondb');
    $description = get_string('almondbpagecssdesc', 'theme_almondb');
    $default = '';
    $setting = new admin_setting_scsscode($name, $title, $description, $default, PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Add navbar to info page.
    $name = 'theme_almondb/almondbpagenavbar'. $count;
    $title = get_string('almondbpagenavbar', 'theme_almondb');
    $description = get_string('almondbpagenavbardesc', 'theme_almondb');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Add header to info page.
    $name = 'theme_almondb/almondbpageheader'. $count;
    $title = get_string('almondbpageheader', 'theme_almondb');
    $description = get_string('almondbpageheaderdesc', 'theme_almondb');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Add footer to info page.
    $name = 'theme_almondb/almondbpagefooter'. $count;
    $title = get_string('almondbpagefooter', 'theme_almondb');
    $description = get_string('almondbpagefooterdesc', 'theme_almondb');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
}
// Simple page.
$name = 'theme_almondb/almondbpageheadingsimple';
$heading = get_string('almondbpageheadingsimple', 'theme_almondb');
$information = get_string('almondbpageheadingsimpledesc', 'theme_almondb');
$setting = new admin_setting_heading($name, $heading, $information);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Enable or disable page settings.
$name = 'theme_almondb/almondbpageenabledsimple';
$title = get_string('almondbpageenabledsimple', 'theme_almondb');
$description = get_string('almondbpageenabledsimpledesc', 'theme_almondb');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Count page settings.
$name = 'theme_almondb/almondbpagecountsimple';
$title = get_string('almondbpagecountsimple', 'theme_almondb');
$description = get_string('almondbpagecountsimpledesc', 'theme_almondb');
$default = 1;
$options = [];
for ($i = 1; $i <= 10; $i++) {
    $options[$i] = $i;
}
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// If we don't have an page yet, default to the preset.
$almondbpagecount = get_config('theme_almondb', 'almondbpagecountsimple');
if (!$almondbpagecount) {
    $almondbpagecount = 2;
}
for ($count = 1; $count <= $almondbpagecount; $count++) {
    $name = 'theme_almondb/almondbpagesimple' . $count . 'info';
    $heading = get_string('almondbpagenosimple', 'theme_almondb', ['almondbpage' => $count]);
    $information = get_string('almondbpagenosimpledesc', 'theme_almondb', ['almondbpage' => $count]);
    $setting = new admin_setting_heading($name, $heading, $information);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Page title.
    $name = 'theme_almondb/almondbpagetitlesimple' . $count;
    $title = get_string('almondbpagetitlesimple', 'theme_almondb');
    $description = get_string('almondbpagetitlesimpledesc', 'theme_almondb');
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_TEXT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Page image.
    $fileid = 'sliderimagealmondbpagesimple'.$count;
    $name = 'theme_almondb/sliderimagealmondbpagesimple'.$count;
    $title = get_string('almondbpageimagesimple', 'theme_almondb');
    $description = get_string('almondbpageimagesimpledesc', 'theme_almondb');
    $opts = ['accepted_types' => ['.png', '.jpg', '.gif', '.webp', '.tiff', '.svg'], 'maxfiles' => 1];
    $setting = new admin_setting_configstoredfile($name, $title, $description, $fileid,  0, $opts);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Count page settings.
    $name = 'theme_almondb/almondbpageimgpositionsimple'.$count;
    $title = get_string('almondbpageimgpositionsimple', 'theme_almondb');
    $description = get_string('almondbpageimgpositionsimpledesc', 'theme_almondb');
    $default = 1;
    $options = [
        "1" => "Background",
        "2" => "Top",
        "21" => "Full Top",
        "3" => "Left",
        "4" => "Right",
    ];
    $setting = new admin_setting_configselect($name, $title, $description, $default, $options);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Page caption.
    $name = 'theme_almondb/almondbpagecapsimple'.$count;
    $title = get_string('almondbpagecaptionsimple', 'theme_almondb');
    $description = get_string('almondbpagecaptionsimpledesc', 'theme_almondb');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Add header to info page.
    $name = 'theme_almondb/almondbpageheadersimple'. $count;
    $title = get_string('almondbpageheadersimple', 'theme_almondb');
    $description = get_string('almondbpageheadersimpledesc', 'theme_almondb');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Add footer to info page.
    $name = 'theme_almondb/almondbpagefootersimple'. $count;
    $title = get_string('almondbpagefootersimple', 'theme_almondb');
    $description = get_string('almondbpagefootersimpledesc', 'theme_almondb');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
}
$page->add(new admin_setting_heading('theme_almondb_almondbpageend', get_string('almondbpageend', 'theme_almondb'),
format_text(get_string('almondbpageenddesc', 'theme_almondb'), FORMAT_MARKDOWN)));
$settings->add($page);
