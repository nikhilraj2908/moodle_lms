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
 * Renders course section navigation
 *
 * @package     format_cards
 * @copyright   2024 University of Essex
 * @author      John Maydew <jdmayd@essex.ac.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_cards\output\courseformat\content;

use core_courseformat\output\local\content\sectionnavigation as sectionnavigation_base;
use renderer_base;
use stdClass;

/**
 * Renders course section navigation
 *
 * @package     format_cards
 * @copyright   2024 University of Essex
 * @author      John Maydew <jdmayd@essex.ac.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sectionnavigation extends sectionnavigation_base {

    /**
     * Get the template path
     *
     * @param renderer_base $renderer
     * @return string
     */
    public function get_template_name(renderer_base $renderer): string {
        return 'format_cards/local/content/sectionnavigation';
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return stdClass data context for a mustache template
     */
    public function export_for_template(renderer_base $output): stdClass {

        $data = parent::export_for_template($output);

        // Inject courseurl into the template data if we don't already have it.
        if (!object_property_exists($data, 'courseurl')) {
            $format = $this->format;
            $course = $format->get_course();

            $displayoption = $this->format->get_format_option('sectionnavigationhome');
            $data->showcoursehome = $displayoption == FORMAT_CARDS_SECTIONNAVIGATIONHOME_SHOW;

            $data->courseurl = course_get_url($course, null, [ 'navigation' => true ])->out();
        }

        if ($data->courseurl == $data->previousurl) {
            $data->showcoursehome = true;
            $data->hasprevious = false;
        }

        return $data;
    }
}
