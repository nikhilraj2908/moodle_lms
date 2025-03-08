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

#[AllowDynamicProperties]

class block_readspeaker_embhl extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_readspeaker_embhl');

        // Set all ReadSpeaker variables.
        $this->plugin_config_language = get_config('block_readspeaker_embhl', 'lang');
        $this->plugin_config_customerid = get_config('block_readspeaker_embhl', 'cid');
        $this->plugin_config_readid = get_config('block_readspeaker_embhl', 'readid');

        $this->plugin_config_docreader = get_config('block_readspeaker_embhl', 'docreader');
        $this->plugin_config_region = get_config('block_readspeaker_embhl', 'region');

        $this->plugin_config_latestscript = get_config('block_readspeaker_embhl', 'latestscript');

        $this->plugin_custom_javascriptparams = get_config('block_readspeaker_embhl', 'customjavascript');
        $this->plugin_custom_params = get_config('block_readspeaker_embhl', 'customparams');

        $this->plugin_disable_in_em = get_config('block_readspeaker_embhl', 'disableinem');
        $this->plugin_custom_showincontent = get_config('block_readspeaker_embhl', 'showincontent');

        $this->plugin_mode = get_config('block_readspeaker_embhl', 'mode');

        $this->plugin_statistics = get_config('block_readspeaker_embhl', 'statistics');
        $this->plugin_custom_translation = get_config('block_readspeaker_embhl', 'translation');
    }

    public function specialization() {
        if (isset($this->config)) {
            if (!empty($this->config->lang)) {
                $this->plugin_config_language = $this->config->lang;
            }
            if (!empty($this->config->customparams)) {
                $this->plugin_custom_params = $this->config->customparams;
            }
            if (!empty($this->config->mode)) {
                $this->plugin_mode = $this->config->mode;
            }
        }
    }

    public function get_content() {

        global $CFG;

        if ($this->content !== NULL) {
            return $this->content;
        }

        // Set default text on the Listen button.
        $listen_text = get_string('listentext', 'block_readspeaker_embhl');
        $listen_text_title = get_string('listen_titletext', 'block_readspeaker_embhl');

        $uilang = $this->moodle_to_rslang(current_language());

        // An encoded version of the page URL.
        $encoded_url = urlencode($this->page->url);

        // Set path for docReader proxy component.
        $docreader_path = $CFG->wwwroot . "/blocks/readspeaker_embhl/docreader/proxy.php";
        $this->content       = new stdClass;
        $this->content->text = '';
        // Get the docReader ID
        $docreader = $this->plugin_config_docreader ? 'cid: "' . $this->plugin_config_customerid.'", ' : '';

        // Set the download file name the same as the current page title.
        $audiofile_name = "";
        $current_page_title = $this->page->title;
        if (!empty($current_page_title)) {
            // Prepare the page title:  replace diacritic letters with its regular analogues; convert all spaces to underscores;
            //                          leave only letters, digits, minuses and underscores; put everything in lowercase.
            $audiofile_name = strtolower(preg_replace('/[^a-zA-Z0-9_-]/', '', str_replace(" ", "_", trim($this->clear_letters($current_page_title)))));

            // Prepare the parameter to be inserted to the URL query directly.
            $audiofile_name = '&amp;audiofilename=' . $audiofile_name;
        }

        // Check whether the Listen button should be moved to the readid element.
        $show_in_content = $this->plugin_custom_showincontent ? $this->plugin_config_readid : '';
        // Check if user is in editing mode.
        $edit_mode = $this->page->user_is_editing();

        // Create the statistics format.
        $stats = urlencode($this->statistics_format());

        // Check the translation source.
        $translation = $this->plugin_custom_translation ? $this->plugin_config_language : '';

        // Determine the region of our block.
        $region = $this->instance->region;
        // Define the detachDragbarMarkup based on the region.
        $detachDragbarMarkup = '';
        if (($region === 'side-pre'||$region === 'side-post') && $this->plugin_custom_showincontent === '0') { // 0 for 'show in block'.
            $detachDragbarMarkup = '<div class="rsbtn_dragbar" tabindex="0" role="button"  aria-grabbed="false">' .
                '<span class="rsbtn_btnlabel">webReader - use arrow keys to move player</span>' .
                '<span class="rsicn rsicn-grippie" aria-hidden="true"></span>' .
                '<span class="close-drag-btn" tabindex="0" data-rs-tooltip="." role="button" data-rslang="title/arialabel:dockplayer" style="position: absolute; right: 2px; top: -2px; cursor: pointer; font-size: 1.8em;">&times;</span>' .
                '</div>';
        }

        // HTML code for inline settings to webReader.
        $script_code = implode(PHP_EOL, [
            '<script type="text/javascript">',
            '   window.rsConf = {',
            '       general: {',
            '           usePost: true,',
            '           translationSourceLang: "' . $translation . '"',
            '       },',
            '       ui: {',
            '           detachDragbarMarkup: \'' . $detachDragbarMarkup . '\',',
            '       },',
            '       moodle: {',
            '           customerid: "' . $this->plugin_config_customerid . '",',
            '           region: "' . $this->plugin_config_region . '",',
            '           showInContent: "' . $show_in_content . '",',
            '           latestVersion: "' . $this->plugin_config_latestscript . '",',
            '           em: "' . $edit_mode . '",',
            '           mode: "' . $this->plugin_mode . '"',
            '       }',
            '   };',
            '   window.rsDocReaderConf = {',
            '       ' . $docreader,
            '       proxypath: "' . $docreader_path . '",',
            '       lang: "' . $this->plugin_config_language . '"',
            '   };',
            '</script>'
        ]);

        // Determine href to use for Listen button.
        $href = 'https://app-' . $this->plugin_config_region .
        '.readspeaker.com/cgi-bin/rsent?customerid=' . $this->plugin_config_customerid .
        '&amp;lang=' . $this->plugin_config_language .
        '&amp;uilang=' . $uilang .
        ($this->plugin_statistics ? '&amp;statparam=' . $stats : '') .
        '&amp;readid=' . $this->plugin_config_readid .
        '&amp;url=' . $encoded_url .
        $audiofile_name .
        // In Moodle we want to include two sets of xslrules, one customer specific and one general theme rule.
        // We only do this for supported themes to avoid spamming logs with warnings.
        ($this->improved_reading_theme($CFG->theme) ? '&amp;xslrule=customer,moodle-' . $CFG->theme : '') .
        $this->plugin_custom_params;

        // HTML code for Listen button in block.
        $listen_button_code = implode(PHP_EOL, [
            '<div id="readspeaker_button1" class="rs_skip rsbtn rs_preserve rscompact">',
            '   <a class="rsbtn_play" title="' . $listen_text_title . '" href="' . $href . '">',
            '       <span class="rsbtn_left rsimg rspart">',
            '           <span class="rsbtn_text">',
            '               <span>'. $listen_text . '</span>',
            '           </span>',
            '       </span>',
            '       <span class="rsbtn_right rsimg rsplay rspart"></span>',
            '   </a>',
            '</div>'
        ]);

        // Populate the block content with the inline script settings and Listen button.
        if (!($this->plugin_disable_in_em === '1' && $edit_mode)) {
            // Request the JS component.
            $this->page->requires->yui_module('moodle-block_readspeaker_embhl-ReadSpeaker', 'M.block_RS.ReadSpeaker.init');
            $this->content->text .= $script_code.$listen_button_code;
        }

        return $this->content;
    }

    public function instance_allow_config() {
        return true;
    }

    public function has_config() {
        return true;
    }

    public function instance_allow_multiple() {
        return false;
    }

    public function applicable_formats() {
        return array('all' => true, 'tag' => false);
    }

    /**
     * Function replaces diacritic letters in the string with
     * its regular analogues and returns "clear" string.
     *
     * @param string $str
     * @return string
     */
    private function clear_letters($str)
    {
        $a = ['À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ'];
        $b = ['A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o'];
        return str_replace($a, $b, $str);
    }

    /**
     * Function for converting a ReadSpeaker language code to Moodle language,
     * provide Moodle language code (ISO 639-1), returns ReadSpeaker lang-code (xx_yy).
     *
     * @param string $lang
     * @return string
     */
    private function moodle_to_rslang($lang) {
        // Define a list of supported ISO 639-1 to ReadSpeaker lang-codes
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
     * Function for checking if theme has supported improved reading rules, curently only snap and boost.
     * Uses pre-set list of themes that have improved reading. Returns true correct and false otherwise.
     *
     * @param string $theme
     * @return boolean
     */
    private function improved_reading_theme($theme) {
        $theme_list = [
            'snap',
            'boost'
        ];
        return in_array($theme, $theme_list);
    }

    /**
     * Function to create the statistics format for webReader.
     *
     * @return string $final_format
     */
    private function statistics_format() {

        global $PAGE;

        // Retrieve all breadcrumb items.
        $breadcrumbs = $PAGE->navbar->get_items();

        // Initialize an array to store breadcrumb text.
        $breadcrumb_texts = [];
        $context = $PAGE->context;
        
        // Check if we are in a course context and the course ID is not 1 (site context).
        if (($context->contextlevel == CONTEXT_COURSE || $context->contextlevel == CONTEXT_MODULE) && isset($COURSE) && $COURSE->id != 1) {
            // Start from the third element (index 2) if in a course context.
            $start_index = 2;
        } else {
            // Otherwise, start from the beginning.
            $start_index = 0;
        }

        // Loop through each breadcrumb item, starting at the appropriate index.
        foreach ($breadcrumbs as $index => $breadcrumb) {
            // Skip the first two elements if in a course context.
            if ($index < $start_index) {
                continue;
            }

            // Get the text content of the breadcrumb.
            $text = $breadcrumb->get_content();

            // Add the text to the breadcrumb array.
            $breadcrumb_texts[] = $text;
        }

        // Join all breadcrumb texts with a separator.
        $breadcrumb_string = implode('/', $breadcrumb_texts);
        $decoded_text = html_entity_decode($breadcrumb_string, ENT_QUOTES | ENT_HTML5);

        $stats_name = preg_replace('/\s+/', '_', $decoded_text);

        $host = $_SERVER['HTTP_HOST'];
        $hostParts = explode('.', $host);

        // Check if there are more than two parts (indicating a subdomain exists).
        if (count($hostParts) > 2) {
            $subdomain = $hostParts[0];
        } else {
            $subdomain = null;
        }

        $final_format = $subdomain. '/' . $stats_name;

        return $final_format;
    }
}