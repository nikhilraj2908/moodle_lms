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
 * Theme Almondb settings lib.
 *
 * @package   theme_almondb
 * @copyright 2022 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Frontpage section.
 * @return url
 */
function theme_almondb_frontpage_section() {
    $theme = theme_config::load('almondb');
    // Front page navbar frontpagenavlightdark?
    $templatecontext['frontpagenavchoice'.$theme->settings->frontpagenavchoice] = $theme->settings->frontpagenavchoice;
    $templatecontext['navbarcontainer'] = $theme->settings->navbarcontainer;
    $templatecontext['header3phone'] = $theme->settings->header3phone;
    // Front page navbar logo select.
    switch ($theme->settings->headerlogo) {
        case 'Logo':
            $templatecontext['headerlogo'] = true;
            break;
        case 'Compact logo':
            $templatecontext['headerlogocompact'] = true;
            break;
    }
    $templatecontext['frontpagenavlightdark'] = $theme->settings->frontpagenavlightdark;
    if (!empty($theme->settings->frontpagenavlink)) {
        $templatecontext['frontpagenavlink'] = theme_almondb_header_links($theme->settings->frontpagenavlink, false);
        $templatecontext['frontpagenavlink-m'] = theme_almondb_header_links($theme->settings->frontpagenavlink, true);
    } else {
        $templatecontext['frontpagenavlink'] = "";
    }
    return $templatecontext;
}
/**
 * Frontpage color.
 * @return url
 */
function theme_almondb_frontpagecolor() {
    $theme = theme_config::load('almondb');
    $templatecontext['colorsetup'] = $theme->settings->frontpagecolor;
    if (empty($theme->settings->sitecolor)) {
        $templatecontext['sitecolor'] = $theme->settings->frontpagecolor;
    } else {
        $templatecontext['sitecolor'] = $theme->settings->sitecolor;
    }
    return $templatecontext;
}
/**
 * Frontpage course image.
 *
 * @param int $id course id.
 * @return url
 */
function almondb_get_course_image($id) {
    global $CFG;
    $url = '';
    require_once( $CFG->libdir . '/filelib.php' );
    $context = context_course::instance( $id );
    $fs = get_file_storage();
    $files = $fs->get_area_files( $context->id, 'course', 'overviewfiles', 0 );
    foreach ($files as $f) {
        if ($f->is_valid_image()) {
            $url = moodle_url::make_pluginfile_url( $f->get_contextid(),
                $f->get_component(), $f->get_filearea(), null, $f->get_filepath(), $f->get_filename(), false );
        }
    }
    return $url;
}
/**
 * Frontpage blog image.
 * @param int $id blog post id.
 * @return url
 */
function almondb_get_blog_post_image($id) {
    global $CFG;
    $url = '';
    require_once( $CFG->libdir . '/filelib.php' );
    $syscontext = context_system::instance();
    $fs = get_file_storage();
    $files = $fs->get_area_files($syscontext->id, 'blog', 'attachment', $id);
    foreach ($files as $f) {
        if ($f->is_valid_image()) {
            $url = moodle_url::make_pluginfile_url( $f->get_contextid(),
                $f->get_component(), $f->get_filearea(), $id, $f->get_filepath(), $f->get_filename(), false );
        }
    }
    return $url;
}
/**
 * Frontpage user image.
 * @param int $id user id.
 * @return url
 */
function almondb_get_user_image($id) {
    global $CFG;
    $url = '';
    require_once( $CFG->libdir . '/filelib.php' );
    $context = context_user::instance( $id );
    $fs = get_file_storage();
    $files = $fs->get_area_files( $context->id, 'user', 'icon', 'almondb', false);
    foreach ($files as $f) {
        if ($f->is_valid_image()) {
            $url = moodle_url::make_pluginfile_url( $f->get_contextid(), $f->get_component(),
                $f->get_filearea(), null, $f->get_filepath(), $f->get_filename(), false );
        }
    }
    return $url;
}
/**
 * Frontpage category image.
 * @param int $id category id.
 * @return url
 */
function theme_almondb_frontpageblockcategory($id) {
    GLOBAL  $DB;
    $category = $DB->get_record('course_categories', ['id' => $id]);
    if (!empty($category)) {
        $categoryname = $category->name;
    }
    return $categoryname;
}
/**
 * Frontpage links.
 * @param string $links footer link.
 * @return content.
 */
function theme_almondb_links($links) {
    $weblink = $links;
    $content = "";
    $websettings = explode("\n", $weblink);
    foreach ($websettings as $key => $settingval) {
        $expset = explode("|", $settingval);
        if (isset($expset[0]) && isset($expset[1]) ) {
            list($ltxt, $lurl) = $expset;
        } else {
            $ltxt = $expset[0];
            $lurl = "#";
        }
        $ltxt = trim($ltxt);
        $lurl = trim($lurl);

        if (empty($ltxt)) {
            continue;
        }
        $content .= '<li><a class="text-decoration-none" href="'.$lurl.'" target="_blank">'.$ltxt.'</a></li>';
    }
    return $content;
}
/**
 * Frontpage header link.
 * @param string $links header desktop nav links.
 * @param string $mobil header mobil links.
 * @return content.
 */
function theme_almondb_header_links($links, $mobil ) {
    $weblink = $links;
    $content = "";
    $lurl = "";
    $ltxt = "";
    $i = 0;
    $websettings = explode("\n", $weblink);
    if ($mobil) {
        $content .= "<div class= 'hynavbar-nav'>";
    } else {
        $content .= "<div class= 'navbar-nav'>";
    }
    foreach ($websettings as $key => $settingval) {
        if (!empty(trim($settingval))) {
            $expset = explode("|", $settingval);
            $j = uniqid();
            if (isset($expset[0]) && !empty($expset[0]) ) {
                if (substr($expset[0], 0, 1) <> "-") {
                    if ($i != 0) {
                        $content .= "</div></div>";
                    }
                    $ltxt = trim($expset[0]);
                    $blank = "_self";
                    if (isset($expset[2])) {
                        $blank = trim($expset[2]);
                    }
                    if (isset($expset[1])) {
                        $lurl = trim($expset[1]);
                        $content .= "<div class='nav-item'>";
                        $content .= "<a class='nav-item nav-link' href='".$lurl."' target='".$blank."'>".$ltxt."</a><div>";
                    } else {
                        $content .= "<div class='position-relative nav-item' id='dropdown-custom-".$j."'>";
                        $content .= "<a class='dropdown-toggle nav-link' id='drop-down-".$j."' data-toggle='dropdown'";
                        $content .= " aria-haspopup='true' aria-expanded='false' href='#' title='".$ltxt."'";
                        $content .= " aria-controls='drop-down-menu-".$j."'>".$ltxt."</a>";
                        $content .= "<div class='dropdown-menu' role='menu'
                                id='drop-down-menu-".$j."' aria-labelledby='drop-down-".$j."'>";
                    }
                } else {
                    $blank = "_self";
                    if (isset($expset[2])) {
                        $blank = trim($expset[2]);
                    }
                    if (isset($expset[1])) {
                        list($ltxt, $lurl) = $expset;
                        $ltxt = trim(substr($ltxt, 1, strlen($ltxt)));
                        $lurl = trim($lurl);
                        $content .= "<a class='dropdown-item' role='menuitem'
                                href='".$lurl."' target='".$blank."'  title='".$ltxt. "'>".$ltxt."</a>";
                    }
                }
            } else {
                $ltxt = $expset[0];
            }
            $i++;
        }
    }
    $content .= "</div></div>";
    $content .= "</div>";
    return $content;
}
/**
 * Frontpage random color.
 * @return randcolor
 */
function theme_almondb_random_color() {
    /*
    * Any of the following methods can be used to find random color.
    * $randcolor = "#".str_pad(dechex(rand(0x000000, 0xFFFFFF)), 6, 0, STR_PAD_LEFT);.
    * $randcolor = "#".substr(md5(rand()), 0, 6);.
    * $randcolor = '#'.substr(str_shuffle('ABCDEF0123456789'), 0, 6);.
    */
    $randcolor = "rgba(".rand(0, 255).", ".rand(50, 255).", ".rand(25, 255).", 0.3)";
    return $randcolor;
}
