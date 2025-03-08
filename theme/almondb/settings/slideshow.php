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
 * Theme almondb slides.
 *
 * @package   theme_almondb
 * @copyright 2022 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
$page = new admin_settingpage('theme_almondb_slide', get_string('slideshow', 'theme_almondb'));
$page->add(new admin_setting_heading('theme_almondb_slideshow', get_string('slideshowheading', 'theme_almondb'),
format_text(get_string('slideshowheadingdesc', 'theme_almondb'), FORMAT_MARKDOWN)));
// Slideshow design select.
$name = 'theme_almondb/sliderdesign';
$title = get_string('sliderdesign', 'theme_almondb');
$description = get_string('sliderdesigndesc', 'theme_almondb');
$default = 1;
$options = [];
for ($i = 1; $i < 5; $i++) {
    $options[$i] = $i;
}
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Enable or disable Slideshow settings.
$name = 'theme_almondb/sliderenabled';
$title = get_string('sliderenabled', 'theme_almondb');
$description = get_string('sliderenableddesc', 'theme_almondb');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$page->add($setting);

// Count Slideshow settings.
$name = 'theme_almondb/slidercount';
$title = get_string('slidercount', 'theme_almondb');
$description = get_string('slidercountdesc', 'theme_almondb');
$default = 4;
$options = [];
for ($i = 0; $i < 7; $i++) {
    $options[$i] = $i;
}
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// If we don't have an slide yet, default to the preset.
$slidercount = get_config('theme_almondb', 'slidercount');

if (!$slidercount) {
    $slidercount = 1;
}
// Header size setting.
$name = 'theme_almondb/slidershowheight';
$title = get_string('slidershowheight', 'theme_almondb');
$description = get_string('slidershowheight_desc', 'theme_almondb');
$default = '550';
$options = [
    '250' => '250',
    '275' => '275',
    '300' => '300',
    '325' => '325',
    '350' => '350',
    '375' => '375',
    '400' => '400',
    '425' => '425',
    '450' => '450',
    '475' => '475',
    '500' => '500',
    '525' => '525',
    '550' => '550',
    '575' => '575',
    '600' => '600',
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Frontpage color opacity to slider.
$name = 'theme_almondb/slideropacity';
$title = get_string('slideropacity', 'theme_almondb');
$description = get_string('slideropacitydesc', 'theme_almondb');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$page->add($setting);

for ($count = 1; $count <= $slidercount; $count++) {
    $name = 'theme_almondb/slide' . $count . 'info';
    $heading = get_string('slideno', 'theme_almondb', ['slide' => $count]);
    $information = get_string('slidenodesc', 'theme_almondb', ['slide' => $count]);
    $setting = new admin_setting_heading($name, $heading, $information);
    $page->add($setting);
    // Slider image.
    $fileid = 'sliderimage'.$count;
    $name = 'theme_almondb/sliderimage'.$count;
    $title = get_string('sliderimage', 'theme_almondb');
    $description = get_string('sliderimagedesc', 'theme_almondb');
    $opts = ['accepted_types' => ['.png', '.jpg', '.gif', '.webp', '.tiff', '.svg'], 'maxfiles' => 1];
    $setting = new admin_setting_configstoredfile($name, $title, $description, $fileid,  0, $opts);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Slider title.
    $name = 'theme_almondb/slidertitle' . $count;
    $title = get_string('slidertitle', 'theme_almondb');
    $description = get_string('slidertitledesc', 'theme_almondb');
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_TEXT);
    $page->add($setting);
    // Slider caption.
    $name = 'theme_almondb/slidercap' . $count;
    $title = get_string('slidercaption', 'theme_almondb');
    $description = get_string('slidercaptiondesc', 'theme_almondb');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $page->add($setting);
    // Slider button.
    $name = 'theme_almondb/sliderbutton' . $count;
    $title = get_string('sliderbutton', 'theme_almondb');
    $description = get_string('sliderbuttondesc', 'theme_almondb');
    $default = get_string('button', 'theme_almondb');
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $page->add($setting);
    // Slide button link.
    $name = 'theme_almondb/sliderurl'. $count;
    $title = get_string('sliderbuttonurl', 'theme_almondb');
    $description = get_string('sliderbuttonurldesc', 'theme_almondb');
    $default = 'http://www.example.com/';
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
    $page->add($setting);
}
$page->add(new admin_setting_heading('theme_almondb_slideend', get_string('slideshowend', 'theme_almondb'),
format_text(get_string('slideshowenddesc', 'theme_almondb'), FORMAT_MARKDOWN)));
$settings->add($page);
