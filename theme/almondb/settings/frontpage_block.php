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
 * Theme almondb frontpage block.
 *
 * @package   theme_almondb
 * @copyright 2022 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
$page = new admin_settingpage('theme_almondb_block', get_string('theme_almondb_frontpageblock', 'theme_almondb'));
require('blocks/block01.php');
require('blocks/block02.php');
require('blocks/block03.php');
require('blocks/block04.php');
require('blocks/block05.php');
require('blocks/block06.php');
require('blocks/block07.php');
require('blocks/block08.php');
require('blocks/block09.php');
require('blocks/block10.php');
require('blocks/block11.php');
require('blocks/block18.php');
require('blocks/block19.php');
require('blocks/block20.php');
// Block end.
$page->add(new admin_setting_heading('theme_almondb_blockend', get_string('theme_almondb_frontpageblockend', 'theme_almondb'),
format_text(get_string('theme_almondb_frontpageblockenddesc', 'theme_almondb'), FORMAT_MARKDOWN)));
$settings->add($page);
