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

/**
 * Slides show.
 * @return url
 */
function theme_almondb_slideshow() {
    global $OUTPUT;

    $theme = theme_config::load('almondb');

    $templatecontext['sliderenabled'] = $theme->settings->sliderenabled;
    if (empty($templatecontext['sliderenabled'])) {
        return $templatecontext;
    }
    $templatecontext['slideropacity'] = $theme->settings->slideropacity;
    $templatecontext['slider'.$theme->settings->sliderdesign] = $theme->settings->sliderdesign;
    $templatecontext['slidershowheight'] = $theme->settings->slidershowheight;
    $slidercount = $theme->settings->slidercount;
    for ($i = 1, $j = 0; $i <= $slidercount; $i++, $j++) {
        $sliderimage = "sliderimage{$i}";
        $slidertitle = "slidertitle{$i}";
        $slidercap = "slidercap{$i}";
        $sliderbutton = "sliderbutton{$i}";
        $sliderurl = "sliderurl{$i}";

        $templatecontext['slides'][$j]['key'] = $j;
        $templatecontext['slides'][$j]['active'] = false;
        $image = $theme->setting_file_url($sliderimage, $sliderimage);
        if (empty($image)) {
            $image = $OUTPUT->image_url('almondb/slider/'.$i, 'theme');
        }
        $templatecontext['slides'][$j]['image'] = $image;
        $templatecontext['slides'][$j]['title'] = format_string($theme->settings->$slidertitle);
        $templatecontext['slides'][$j]['caption'] = format_string($theme->settings->$slidercap);
        $templatecontext['slides'][$j]['button'] = format_string($theme->settings->$sliderbutton);
        $templatecontext['slides'][$j]['buttonurl'] = format_string($theme->settings->$sliderurl);
        if ($i === 1) {
            $templatecontext['slides'][$j]['active'] = true;
        }
    }
    return $templatecontext;
}
