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
class block_readspeaker_embhl_edit_form extends block_edit_form {

    protected function specific_definition($mform) {

        // Section header title according to language file.
        $mform->addElement('header', 'configheader', get_string('block_settings_title', 'block_readspeaker_embhl'));

        // Set a custom language.
        $language_option = array(
            'af_za' => get_string('afrikaans', 'block_readspeaker_embhl'),
            'ar_ar' => get_string('arabic', 'block_readspeaker_embhl'),
            'eu_es' => get_string('basque', 'block_readspeaker_embhl'),
            'ca_es' => get_string('catalan', 'block_readspeaker_embhl'),
            'zh_cn' => get_string('chinese_mandarin', 'block_readspeaker_embhl'),
            'zh_tw' => get_string('chinese_taiwanese', 'block_readspeaker_embhl'),
            'hr_hr' => get_string('croatian', 'block_readspeaker_embhl'),
            'cs_cz' => get_string('czech', 'block_readspeaker_embhl'),
            'da_dk' => get_string('danish', 'block_readspeaker_embhl'),
            'nl_nl' => get_string('dutch', 'block_readspeaker_embhl'),
            'fy_nl' => get_string('dutch_frisian', 'block_readspeaker_embhl'),
            'nl_be' => get_string('dutch_flemish', 'block_readspeaker_embhl'),
            'en_us' => get_string('english_american', 'block_readspeaker_embhl'),
            'en_au' => get_string('english_australian', 'block_readspeaker_embhl'),
            'en_in' => get_string('english_indian', 'block_readspeaker_embhl'),
            'en_nz' => get_string('english_newzealand', 'block_readspeaker_embhl'),
            'en_sc' => get_string('english_scottish', 'block_readspeaker_embhl'),
            'en_za' => get_string('english_southafrican', 'block_readspeaker_embhl'),
            'en_uk' => get_string('english_brittish', 'block_readspeaker_embhl'),
            'fo_fo' => get_string('faroese', 'block_readspeaker_embhl'),
            'fa_ir' => get_string('farsi', 'block_readspeaker_embhl'),
            'fi_fi' => get_string('finnish', 'block_readspeaker_embhl'),
            'fr_fr' => get_string('french', 'block_readspeaker_embhl'),
            'fr_be' => get_string('french_belgian', 'block_readspeaker_embhl'),
            'fr_ca' => get_string('french_canadian', 'block_readspeaker_embhl'),
            'gl_es' => get_string('gelician', 'block_readspeaker_embhl'),
            'he_il' => get_string('hebrew', 'block_readspeaker_embhl'),
            'de_de' => get_string('german', 'block_readspeaker_embhl'),
            'el_gr' => get_string('greek', 'block_readspeaker_embhl'),
            'hi_in' => get_string('hindi', 'block_readspeaker_embhl'),
            'zh_hk' => get_string('hong_kong_cantonese', 'block_readspeaker_embhl'),
            'hu_hu' => get_string('hungarian', 'block_readspeaker_embhl'),
            'is_is' => get_string('icelandic', 'block_readspeaker_embhl'),
            'nr_za' => get_string('isindebele', 'block_readspeaker_embhl'),
            'xh_za' => get_string('isixhosa', 'block_readspeaker_embhl'),
            'zu_za' => get_string('isizulu', 'block_readspeaker_embhl'),
            'it_it' => get_string('italian', 'block_readspeaker_embhl'),
            'ja_jp' => get_string('japanese', 'block_readspeaker_embhl'),
            'ko_kr' => get_string('korean', 'block_readspeaker_embhl'),
            'lv_lv' => get_string('latvian', 'block_readspeaker_embhl'),
            'nso' => get_string('sepedi', 'block_readspeaker_embhl'),
            'st_za' => get_string('sesotho', 'block_readspeaker_embhl'),
            'tn_za' => get_string('setswana', 'block_readspeaker_embhl'),
            'ss_za' => get_string('siswati', 'block_readspeaker_embhl'),
            'es_ar' => get_string('spanish_argentinian', 'block_readspeaker_embhl'),
            'es_es' => get_string('spanish_castilian', 'block_readspeaker_embhl'),
            'es_us' => get_string('spanish_american', 'block_readspeaker_embhl'),
            'es_co' => get_string('spanish_columbian', 'block_readspeaker_embhl'),
            'es_mx' => get_string('spanish_mexican', 'block_readspeaker_embhl'),
            'no_nb' => get_string('norwegian_bokmal', 'block_readspeaker_embhl'),
            'no_nn' => get_string('norwegian_nynorsk', 'block_readspeaker_embhl'),
            'pl_pl' => get_string('polish', 'block_readspeaker_embhl'),
            'pt_pt' => get_string('portuguese', 'block_readspeaker_embhl'),
            'pt_br' => get_string('portuguese_brazilian', 'block_readspeaker_embhl'),
            'ro_ro' => get_string('romanian', 'block_readspeaker_embhl'),
            'ru_ru' => get_string('russian', 'block_readspeaker_embhl'),
            'sv_se' => get_string('swedish', 'block_readspeaker_embhl'),
            'sv_fi' => get_string('swedish_finnish', 'block_readspeaker_embhl'),
            'th_th' => get_string('thai', 'block_readspeaker_embhl'),
            've_za' => get_string('tshivenda', 'block_readspeaker_embhl'),
            'tr_tr' => get_string('turkish', 'block_readspeaker_embhl'),
            'uk_ua' => get_string('ukranian', 'block_readspeaker_embhl'),
            'cy_cy' => get_string('welsh', 'block_readspeaker_embhl'),
            'ts_za' => get_string('xitsonga', 'block_readspeaker_embhl')
        );
        $language_select = $mform->addElement('select', 'config_lang', get_string('lang', 'block_readspeaker_embhl'), $language_option);
        $language_select->setSelected(get_config('block_readspeaker_embhl', 'lang'));
        $mform->addHelpButton('config_lang', 'lang', 'block_readspeaker_embhl');

        // For custom parameters.
        $mform->addElement('text', 'config_customparams', get_string('customparams', 'block_readspeaker_embhl'));
        $mform->setDefault('config_customparams', get_config('block_readspeaker_embhl', 'customparams'));
        $mform->setType('config_customparams', PARAM_TEXT);
        $mform->addHelpButton('config_customparams', 'customparams', 'block_readspeaker_embhl');

        // For setting the &mode url parameter in the webReader script.
        $mode_options = array(
            'standard' => get_string('standard', 'block_readspeaker_embhl'),
            'restricted' => get_string('restricted', 'block_readspeaker_embhl')
        );
        $mode_select = $mform->addElement('select', 'config_mode', get_string('webreaderfeatures', 'block_readspeaker_embhl'), $mode_options);
        $mode_select->setSelected(get_config('block_readspeaker_embhl', 'mode'));
        $mform->addHelpButton('config_mode', 'webreaderfeatures', 'block_readspeaker_embhl');
    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if (isset($data['config_customparams'])) {
            // Check with regular expression that it only contains valid characters
            if (preg_match('/[^a-zA-Z0-9&=]/', $data['config_customparams'])) {
                $errors['config_customparams'] = 'Invalid character included in custom parameter.';
            }
            // Check PARAM_URL with temporary test URL
            $temp_url = 'https://app-eu.readspeaker.com/cgi-bin/rsent?customerid=TEST&lang=TEST&readid=TEST&url=TEST' . $data['config_customparams'];
            if (clean_param($temp_url, PARAM_URL) === '') {
                $errors['config_customparams'] = 'Invalid custom parameter provided, did not pass PARAM_URL check.';
            }
        }
        return $errors;
    }
}
