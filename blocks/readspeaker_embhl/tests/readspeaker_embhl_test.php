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
class block_readspeaker_embhl_testcase extends advanced_testcase {
    public function test_get_content() {
        global $CFG;

        $this->resetAfterTest(true);
        set_config('lang', 'it_it', 'block_readspeaker_embhl');

        $title_text = array("it_it" => "Ascolta questa pagina con ReadSpeaker");
        $title = $title_text["it_it"];

        $listen_text = array("it_it" => "Ascolta");
        $listen_description = $listen_text["it_it"];

        $slink = "cdn-";
        $region = get_config('block_readspeaker_embhl', 'region');

        $page_url = "https://".(isset($_SERVER["SERVER_NAME"]) ? $_SERVER["SERVER_NAME"] : '').(isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : '');
        $encoded_url = urlencode($page_url);

        $docreader_path = $CFG->wwwroot . "/blocks/readspeaker_embhl/docreader/proxy.php";
        $content = new stdClass;
        $content->text = '';

        $this->page->requires->yui_module('moodle-block_readspeaker_embhl-ReadSpeaker', 'M.block_RS.ReadSpeaker.init');

        $docreader_id = get_config('block_readspeaker_embhl', 'docreaderenabled') ? 'cid: "' . get_config('block_readspeaker_embhl', 'docreaderenabled') . '"' : '';

        $content->text .= '<script type="text/javascript">window.rsConf = {general: {usePost: true}}; window.rsDocReaderConf = {'.$docreader_id.', proxypath: "'.$docreader_path.'", lang: "'.get_config('block_readspeaker_embhl', 'lang').'"}</script>
            <div id="readspeaker_button1" class="rs_skip rsbtn rs_preserve">
            <a accesskey="L" class="rsbtn_play" title="'.$title.'" href="https://app-' . $region . '.readspeaker.com/cgi-bin/rsent?customerid=' . get_config('block_readspeaker_embhl', 'cid') . '&amp;lang='.get_config('block_readspeaker_embhl', 'lang') . '&amp;readid=' . get_config('block_readspeaker_embhl', 'readid').'&amp;url='.$encoded_url.get_config('block_readspeaker_embhl', 'customparams').'">
            <span class="rsbtn_left rsimg rspart"><span class="rsbtn_text"><span>'.$listen_description.'</span></span></span>
            <span class="rsbtn_right rsimg rsplay rspart"></span>
            </a>
            </div>';

        $plugin_config_language = get_config('block_readspeaker_embhl', 'lang');
        $plugin_config_customerid = get_config('block_readspeaker_embhl', 'cid');
        $plugin_config_readid = get_config('block_readspeaker_embhl', 'readid');

        $plugin_config_docreader = get_config('block_readspeaker_embhl', 'docreaderenabled');
        $plugin_config_region = get_config('block_readspeaker_embhl', 'region');

        $plugin_custom_javascriptparams = get_config('block_readspeaker_embhl', 'customjavascript');
        $plugin_custom_params = get_config('block_readspeaker_embhl', 'customparams');

        $content->text .= '<script type="text/javascript">window.rsConf = {general: {usePost: true}, moodle: {customerid: "' . $plugin_config_customerid .'", region: "'. $region .'"}, ui: {tools: {voicesettings: true}}}; window.rsDocReaderConf = {' . $docreader_id . 'proxypath: "' . $docreader_path . '", lang: "' . $plugin_config_language . '"}</script>'.
            '<div id="readspeaker_button1" class="rs_skip rsbtn rs_preserve rscompact">
            <a accesskey="L" class="rsbtn_play" title="' . $title . '" href="https://app-' . $region . '.readspeaker.com/cgi-bin/rsent?customerid=' . $plugin_config_customerid . '&amp;lang='.$plugin_config_language.'&amp;readid='.$plugin_config_readid.'&amp;url='.$encoded_url.$plugin_custom_params.'">
            <span class="rsbtn_left rsimg rspart"><span class="rsbtn_text"><span>'.$listen_description.'</span></span></span>
            <span class="rsbtn_right rsimg rsplay rspart"></span>
            </a>
            </div>';

        $test_mock = $this->createMock('block_readspeaker_embhl');
        $test_mock->expects($this->any())->method('get_content')->will($this->returnValue($content));
    }
}