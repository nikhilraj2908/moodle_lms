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
 * Overrides default control menu to modify the permalink
 *
 * @package   format_cards
 * @copyright 2024 University of Essex
 * @author    John Maydew <jdmayd@essex.ac.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace format_cards\output\courseformat\content\section;

use format_topics\output\courseformat\content\section\controlmenu as base_controlmenu;

/**
 * Section control menu class
 *
 * @package     format_cards
 * @copyright   2024 University of Essex
 * @author      John Maydew <jdmayd@essex.ac.uk>
 */
class controlmenu extends base_controlmenu {

    /**
     * The default control items return a link to the section as a URL fragment.
     * The cards format displays each section on a separate page, so if the permalink
     * control is present, replace it with a version that uses a direct link.
     *
     * @return array
     */
    public function section_control_items(): array {

        // Grab all the default controls.
        $controls = parent::section_control_items();

        // Nothing to do if there's no permalink.
        if (!array_key_exists('permalink', $controls)) {
            return $controls;
        }

        // Format base class already has a function to get the correct section URL.
        $sectionlink = $this->format->get_view_url($this->section->section);

        $controls['permalink'] = [
            'url' => $sectionlink,
            'icon' => 'i/link',
            'name' => get_string('sectionlink', 'course'),
            'pixattr' => ['class' => ''],
            'attr' => [
                'class' => 'icon',
                'data-action' => 'permalink',
            ],
        ];

        return $controls;
    }

}
