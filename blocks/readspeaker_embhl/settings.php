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

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_heading(
        'header_config',
        get_string('header_config', 'block_readspeaker_embhl'),
        get_string('header_config_help', 'block_readspeaker_embhl')
    ));

    $settings->add(new admin_setting_configtext(
        'block_readspeaker_embhl/cid',
        get_string('customerid', 'block_readspeaker_embhl'),
        get_string('customerid_help', 'block_readspeaker_embhl'),
        0,
        PARAM_INT
    ));

    $settings->add(new admin_setting_configtext(
        'block_readspeaker_embhl/readid',
        get_string('readid', 'block_readspeaker_embhl'),
        get_string('readid_help', 'block_readspeaker_embhl'),
        'region-main',
        PARAM_RAW
    ));

    $settings->add(new admin_setting_configselect(
        'block_readspeaker_embhl/lang',
        get_string('lang', 'block_readspeaker_embhl'),
        get_string('lang_help', 'block_readspeaker_embhl'),
        'en_us',
        array(
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
        )
    ));

    $settings->add(new admin_setting_configselect(
        'block_readspeaker_embhl/translation',
        get_string('translation', 'block_readspeaker_embhl'),
        get_string('translation_help', 'block_readspeaker_embhl'),
        '0',
        array(
                '0' => get_string('translation_page', 'block_readspeaker_embhl'),
                '1' => get_string('translation_voice', 'block_readspeaker_embhl')
            )
    ));

    $settings->add(new admin_setting_configselect(
        'block_readspeaker_embhl/region',
        get_string('region', 'block_readspeaker_embhl'),
        get_string('region_help', 'block_readspeaker_embhl'),
        'eu',
        array(
            'af' => get_string('africa', 'block_readspeaker_embhl'),
            'as' => get_string('asia', 'block_readspeaker_embhl'),
            'eas' => get_string('east_asia', 'block_readspeaker_embhl'),
            'eu' => get_string('europe', 'block_readspeaker_embhl'),
            'me' => get_string('middle_east', 'block_readspeaker_embhl'),
            'na' => get_string('north_america', 'block_readspeaker_embhl'),
            'sa' => get_string('south_america', 'block_readspeaker_embhl'),
            'oc' => get_string('oceania', 'block_readspeaker_embhl')
        )
    ));

    $settings->add(new admin_setting_configselect(
        'block_readspeaker_embhl/showincontent',
        get_string('showincontent', 'block_readspeaker_embhl'),
        get_string('showincontent_help', 'block_readspeaker_embhl'),
        '1',
        array(
                '0' => get_string('showincontent_showinblock', 'block_readspeaker_embhl'),
                '1' => get_string('showincontent_showincontent', 'block_readspeaker_embhl')
            )
    ));

    $settings->add(new admin_setting_configcheckbox(
        'block_readspeaker_embhl/docreader',
        get_string('docreader', 'block_readspeaker_embhl'),
        get_string('docreader_help', 'block_readspeaker_embhl'), false)
    );

    $settings->add(new admin_setting_configcheckbox(
        'block_readspeaker_embhl/disableinem',
        get_string('disableinem', 'block_readspeaker_embhl'),
        get_string('disableinem_help', 'block_readspeaker_embhl'), false)
    );

    $settings->add(new admin_setting_configcheckbox(
        'block_readspeaker_embhl/latestscript',
        get_string('latestscript', 'block_readspeaker_embhl'),
        get_string('latestscript_help', 'block_readspeaker_embhl'), false)
    );

    $settings->add(new admin_setting_configtext(
        'block_readspeaker_embhl/customparams',
        get_string('customparams', 'block_readspeaker_embhl'),
        get_string('customparams_help', 'block_readspeaker_embhl'),
        '',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configcheckbox(
        'block_readspeaker_embhl/statistics',
        get_string('statistics', 'block_readspeaker_embhl'),
        get_string('stats_help', 'block_readspeaker_embhl'), false)
    );

    $settings->add(new admin_setting_configcheckbox(
        'block_readspeaker_embhl/mobileapp',
        get_string('mobileapp', 'block_readspeaker_embhl'),
        get_string('mobileapp_help', 'block_readspeaker_embhl'), false),
    );

    $settings->add(new admin_setting_configtext(
        'block_readspeaker_embhl/pixels',
        get_string('pixels', 'block_readspeaker_embhl'),
        get_string('pixels_help', 'block_readspeaker_embhl'),
        '130',
        PARAM_RAW
    ));
}
