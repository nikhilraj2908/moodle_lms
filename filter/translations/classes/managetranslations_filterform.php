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
 * @package filter_translations
 * @author Andrew Hancox <andrewdchancox@googlemail.com>
 * @author Open Source Learning <enquiries@opensourcelearning.co.uk>
 * @link https://opensourcelearning.co.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2021, Andrew Hancox
 */

namespace filter_translations;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/formslib.php');

/**
 * Form for filtering the managetranslations_table.
 */
class managetranslations_filterform extends \moodleform {
    /**
     * Form definition method.
     */
    public function definition() {
        global $CFG, $PAGE;

        $mform = $this->_form;

        $mform->addElement('header', 'filteroptions', get_string('filteroptions', 'filter_translations'));

        $languages = [0 => get_string('any')] + get_string_manager()->get_list_of_translations();

        if (!has_capability('filter/translations:editsitedefaulttranslations', $PAGE->context) && isset($languages[$CFG->lang])) {
            unset($languages[$CFG->lang]);
        }

        $mform->addElement('select', 'targetlanguage', get_string('targetlanguage', 'filter_translations'), $languages);

        $mform->addElement('text', 'rawtext', get_string('rawtext', 'filter_translations'));
        $mform->setType('rawtext', PARAM_TEXT);

        $mform->addElement('text', 'substitutetext', get_string('substitutetext', 'filter_translations'));
        $mform->setType('substitutetext', PARAM_TEXT);

        $mform->addElement('text', 'hash', get_string('hash', 'filter_translations'));
        $mform->setType('hash', PARAM_TEXT);

        $mform->addElement('text', 'usermodified', get_string('userid', 'filter_translations'));
        $mform->setType('usermodified', PARAM_INT);

        $mform->addElement('submit', 'submit', get_string('update'));
    }
}
