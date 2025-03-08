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
 * Mobile output functions.
 *
 * @package block_readspeaker_embhl
 * @copyright 2024 ReadSpeaker
 * @author     Nikolina Milioni
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_readspeaker_embhl\output;

defined('MOODLE_INTERNAL') || die();

class mobile {

    /**
     * Function for selecting a specific option from Moodle configuration,
     * provide string option, returns the value of the configuration.
     *
     * @param string $option
     * @return string
     */
    public static function commonConf($option) {
        // Common variables for mobile class methods.
        $plugin_config_customerid = get_config('block_readspeaker_embhl', 'cid');
        $plugin_config_region = get_config('block_readspeaker_embhl', 'region');

        if ($option === 'cid') {
            return $plugin_config_customerid;
        } elseif ($option === 'reg') {
            return $plugin_config_region;
        }
    }

    /**
     * Function for converting a ReadSpeaker language code to Moodle language,
     * provide Moodle language code (ISO 639-1), returns ReadSpeaker lang-code (xx_yy).
     *
     * @param string $lang
     * @return string
     */
    public static function moodle_to_rslang($lang) {
        // Define a list of supported ISO 639-1 to ReadSpeaker lang-codes.
        $langlist = [
            'af' => 'af_za',
            'ar' => 'ar_ar',
            'en' => 'en_us',
            'ca' => 'ca_es',
            'ca' => 'vl_es',
            'cz' => 'cs_cz',
            'cy' => 'cy_cy',
            'de' => 'de_de',
            'da' => 'da_dk',
            'el' => 'el_gr',
            'es' => 'es_es',
            'eu' => 'eu_es',
            'fi' => 'fi_fi',
            'fr' => 'fr_fr',
            'fo' => 'fo_fo',
            'fy' => 'fy_nl',
            'gl' => 'gl_es',
            'he' => 'he_il',
            'hi' => 'hi_in',
            'hr' => 'hr_hr',
            'it' => 'it_it',
            'is' => 'is_is',
            'ja' => 'ja_jp',
            'ko' => 'ko_kr',
            'nl' => 'nl_nl',
            'nb' => 'no_nb',
            'nd' => 'nr_za',
            'nr' => 'nr_za',
            'nn' => 'no_nn',
            'pl' => 'pl_pl',
            'pt' => 'pt_pt',
            'ro' => 'ro_ro',
            'ru' => 'ru_ru',
            'ss' => 'ss_za',
            'sv' => 'sv_se',
            'tn' => 'tn_za',
            'tr' => 'tr_tr',
            'ts' => 'ts_za',
            'uk' => 'uk_ua',
            've' => 've_za',
            'zh' => 'zh_cn',
            'zh_cn' => 'zh_cn',
            'yue' => 'zh_hk',
            'zh_tw' => 'zh_tw',
            'nan' => 'zh_tw',
            'xh' => 'xh_za',
            'zu' => 'zu_za'
        ];

        // Check if language map exists and return Moodle language code.
        if (isset($langlist[$lang])) {
            return $langlist[$lang];
        }
        // If there is no language found for the entire code, only look at the two first characters in case it is a shortcode.
        if (isset($langlist[substr($lang, 0, 2)])) {
            return $langlist[substr($lang, 0, 2)];
        }

        // If not found, return the default English value.
        return $langlist['en'];
    }

    /**
     * Function finds which courses have the block enabled.
     *
     * @return array An array containing the course ids.
     */
    public static function block_in_course() {
        global $DB;

        // Fetch only visible courses.
        $courses = $DB->get_records('course', array('visible' => 1), 'fullname ASC');
        $courseIds = array(); 

        if ($courses) {
            foreach ($courses as $course) {
                // Get the context ID for the course.
                $contextId = \context_course::instance($course->id)->id;

                // Retrieve all readspeaker block instances for this course context.
                $blockInstances = $DB->get_records('block_instances', array(
                    'parentcontextid' => $contextId,
                    'blockname' => 'readspeaker_embhl'
                ));

                // Check if any block instances exist for this block in the current course.
                if ($blockInstances) {
                    foreach ($blockInstances as $blockInstance) {
                        if (
                            isset($blockInstance->pagetypepattern) && 
                            (strpos($blockInstance->pagetypepattern, 'course-view-') === 0 || 
                             strpos($blockInstance->pagetypepattern, 'course-') === 0 | 
                             strpos($blockInstance->pagetypepattern, '*') === 0)
                        ) {
                            if (!in_array($course->id, $courseIds)) {
                                // Add the course ID to the array if it's not already there.
                                $courseIds[] = $course->id;
                            }
                            // No need to check further block instances for this course once a match is found.
                            break;
                        }
                    }
                }
            }
        }
        return json_encode($courseIds);
    }

    /**
     * Function finds which modules have the block enabled.
     *
     * @return array An array containing the module ids.
     */
    public static function block_in_module() {
        global $DB;

        // Fetch all block instances for the plugin.
        $blockInstances = $DB->get_records('block_instances', array('blockname' => 'readspeaker_embhl'));
        $modIds = array(); 

        if ($blockInstances) {
            foreach ($blockInstances as $blockInstance) {
                // Retrieve the context for the block instance.
                $context = \context::instance_by_id($blockInstance->parentcontextid, IGNORE_MISSING);
        
                // 1: Check if the block is set at the course level and applies to "All pages".
                if ($context && $context->contextlevel === CONTEXT_COURSE) {
                    $courseId = $context->instanceid;
        
                    if ($blockInstance->pagetypepattern === '*') {
                        // Retrieve all modules in this course.
                        $modules = $DB->get_records('course_modules', array('course' => $courseId));
        
                        foreach ($modules as $module) {
                            // Get the context for the module.
                            $moduleContext = \context_module::instance($module->id);
        
                            // Check if the block is manually removed in this module context.
                            $blockManuallyRemoved = $DB->get_record('block_positions', array(
                                'blockinstanceid' => $blockInstance->id,
                                'contextid' => $moduleContext->id,
                                'visible' => 0
                            ));
        
                            // If the block is not manually removed, add the module ID to the list (simple array of IDs).
                            if (!$blockManuallyRemoved) {
                                $modIds[] = $module->id;  // Add only the module ID.
                            }
                        }
                    }
                }
        
                // 2: Check if the block is directly added to a module (activity/resource).
                if (
                    isset($blockInstance->pagetypepattern) &&
                    (strpos($blockInstance->pagetypepattern, 'mod-') === 0 || strpos($blockInstance->pagetypepattern, '*') === 0)
                ) {
                    if ($context && $context->contextlevel === CONTEXT_MODULE) {
                        $moduleId = $context->instanceid;
        
                        // Retrieve the course ID from the module.
                        $moduleRecord = $DB->get_record('course_modules', array('id' => $moduleId), 'course');
                        $courseId = $moduleRecord ? $moduleRecord->course : null;
        
                        // Check if the block is manually removed in this module context.
                        $blockManuallyRemoved = $DB->get_record('block_positions', array(
                            'blockinstanceid' => $blockInstance->id,
                            'contextid' => $context->id,
                            'visible' => 0
                        ));
        
                        // Add the module ID to the list if valid and not removed.
                        if ($courseId && !$blockManuallyRemoved && !in_array($moduleId, $modIds)) {
                            $modIds[] = $moduleId;  // Add only the module ID.
                        }
                    }
                }
            }
        }
        

        // Return the combined list of course IDs and module IDs as JSON.
        return json_encode($modIds);
    }
    

    /**
     * Function for fetching the Javascript content for the mobile app and
     * adding the window object to the JS file.
     *
     * @return array An associative array containing the JavaScript file content as a string.
     */
    public static function rs_init() {
        global $CFG;

        // Check if plugin in mobile is enabled.
        $plugin_config_mobile = get_config('block_readspeaker_embhl', 'mobileapp');

        // Prepare params for rsConf object.
        $plugin_config_readid = get_config('block_readspeaker_embhl', 'readid');
        $plugin_custom_showincontent = get_config('block_readspeaker_embhl', 'showincontent');
        $show_in_content = $plugin_custom_showincontent ? $plugin_config_readid : '';
        $plugin_config_latestscript = get_config('block_readspeaker_embhl', 'latestscript');
        $plugin_mode = get_config('block_readspeaker_embhl', 'mode');
        $plugin_config_pixels = get_config('block_readspeaker_embhl', 'pixels');
        $toolbar_location = $plugin_config_pixels ? $plugin_config_pixels : '130';

        // Convert PHP array to JSON
        $courseJson = self::block_in_course();
        $modJson = self::block_in_module();

        // Get the content of the JavaScript file.
        $jscontent = file_get_contents($CFG->dirroot . '/blocks/readspeaker_embhl/appjs/ReadSpeaker.Mobile.js');

        // Construct the JS window object for the script tag.
        $script_code = implode(PHP_EOL, [
            'window.rsConf = {',
            '    general: {',
            '        usePost: true, ',
            '        labels :{ignoreSelector: "[aria-label], [alt]"}',
            '    },',
            '    ui: {',
            '        tools: {',
            '            download: false,',
            '        },',
            '        displayDownload: false,',
            '        mobileVertPos: "bottom=' . $toolbar_location . '" ',
            '    },',
            '    moodle: {',
            '        customerid: "' . self::commonConf('cid') . '",',
            '        region: "' . self::commonConf('reg') . '",',
            '        showInContent: "' . $show_in_content . '",',
            '        latestVersion: "' . $plugin_config_latestscript . '",',
            '        mode: "' . $plugin_mode . '",',
            '        mobile: "' . $plugin_config_mobile . '"',
            '    }',
            '};'
        ]);

        // Replace the placeholders in the JavaScript file content with the PHP variable.
        $javascriptContent = str_replace('// %script_code%', "dynamicScriptCode = " . json_encode($script_code) . ";\n" .
        "var modules = " . $modJson . ";\n" .
        "var courses = " . $courseJson . ";", $jscontent);
        
        return [
            'javascript' => $javascriptContent,
        ];
    }

    /**
     * Return the Listen button.
     *
     * @return array An associative array with the web service response: template.
     */
    public static function mobile_block_view(array $args) : array {
        global $OUTPUT, $CFG, $PAGE;

        // Prepare Listen button data.
        $uilang = self::moodle_to_rslang(current_language());
        $plugin_config_language = get_config('block_readspeaker_embhl', 'lang');
        $plugin_custom_params = get_config('block_readspeaker_embhl', 'customparams');
        $encoded_url = urlencode($PAGE->url);
        $listen_titletext = get_string('listen_titletext', 'block_readspeaker_embhl');
        $listen_text = get_string('listentext', 'block_readspeaker_embhl');

        $href = 'https://app-' . self::commonConf('reg') . '.readspeaker.com/cgi-bin/rsent';
        $href .= '?customerid=' . self::commonConf('cid');
        $href .= '&lang=' . $plugin_config_language;
        $href .= '&uilang=' . $uilang;
        $href .= '&readclass=content-ltr';
        $href .= '&url=' . $encoded_url;

        // Optionally add xslrule parameter based on theme.
        if ($CFG->theme === 'snap' || $CFG->theme === 'boost') {
            $href .= '&xslrule=customer,moodle-' . $CFG->theme;
        };

        $href .= $plugin_custom_params;

        // Populate the mustache tempalte with data.
        $data = [
                'listen_text_title' => $listen_titletext,
                'listen_text' => $listen_text,
                'href' => $href,
        ];

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('block_readspeaker_embhl/mobile_block_view', $data),
                ],
            ],
            'javascript' => file_get_contents($CFG->dirroot . '/blocks/readspeaker_embhl/appjs/ReadSpeaker.Mobile.js'),
        ];
    }
}