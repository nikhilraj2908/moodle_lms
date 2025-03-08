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
 * ReadSpeakers webReader for Moodle block.
 *
 * @package    block_readspeaker_embhl
 * @copyright  2016 ReadSpeaker <info@readspeaker.com>
 * @author     Richard Risholm
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version = 2024121601;  // YYYYMMDDHH (year, month, day, 24-hr time)
$plugin->requires = 2015111600; // YYYYMMDDHH
$plugin->component = 'block_readspeaker_embhl'; // Full name of the plugin (used for diagnostics)