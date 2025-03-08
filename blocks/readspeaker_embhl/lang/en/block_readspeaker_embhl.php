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

// General Strings.
$string['pluginname'] = 'ReadSpeaker webReader';
$string['readspeaker_embhl'] = 'ReadSpeaker webReader';
$string['readspeaker_embhl:addinstance'] = 'Add a new webReader block';
$string['readspeaker_embhl:myaddinstance'] = 'Add a new webReader block to the My Moodle page';
$string['readspeaker_embhl:edit'] = 'Edit setting for the webReader block';

// Admin Configuration Strings.
$string['header_config'] = 'Configuration Options';
$string['header_config_help'] = 'Below you will find the configuration options available for the ReadSpeaker webReader plugin.';

// Block title.
$string['block_title'] = 'Listen to this page using ReadSpeaker';
$string['block_settings_title'] = 'Configure ReadSpeaker webReader';

// CustomerID.
$string['customerid'] = 'Customer ID (required)';
$string['customerid_help'] = 'Your ReadSpeaker Customer ID (example "1234").';

// Language.
$string['lang'] = 'Reading language (required)';
$string['lang_help'] = 'Select the reading language for the Listen button (the selected language must be enabled in your ReadSpeaker account).';

// List of reading languages.
$string['afrikaans'] = 'Afrikaans';
$string['arabic'] = 'Arabic';
$string['basque'] = 'Basque';
$string['catalan'] = 'Catalan';
$string['chinese_mandarin'] = 'Chinese (Mandarin)';
$string['chinese_taiwanese'] = 'Chinese Taiwanese Mandarin';
$string['croatian'] = 'Croatian';
$string['czech'] = 'Czech';
$string['danish'] = 'Danish';
$string['dutch'] = 'Dutch';
$string['dutch_frisian'] = 'Dutch (Frisian)';
$string['dutch_flemish'] = 'Dutch (Flemish)';
$string['english_american'] = 'English (American)';
$string['english_australian'] = 'English (Australian)';
$string['english_indian'] = 'English (Indian)';
$string['english_newzealand'] = 'English (New Zealand)';
$string['english_scottish'] = 'English (Scottish)';
$string['english_southafrican'] = 'English (South African)';
$string['english_brittish'] = 'English (UK)';
$string['faroese'] = 'Faroese';
$string['farsi'] = 'Farsi';
$string['finnish'] = 'Finnish';
$string['french'] = 'French';
$string['french_belgian'] = 'French (Belgian)';
$string['french_canadian'] = 'French (Canadian)';
$string['gelician'] = 'Galician';
$string['hebrew'] = 'Hebrew';
$string['german'] = 'German';
$string['greek'] = 'Greek';
$string['hindi'] = 'Hindi';
$string['hong_kong_cantonese'] = 'Hong Kong Cantonese';
$string['hungarian'] = 'Hungarian';
$string['icelandic'] = 'Icelandic';
$string['isindebele'] = 'IsiNdebele';
$string['isixhosa'] = 'IsiXhosa';
$string['isizulu'] = 'IsiZulu';
$string['italian'] = 'Italian';
$string['japanese'] = 'Japanese';
$string['korean'] = 'Korean';
$string['latvian'] = 'latvian';
$string['sepedi'] = 'Sepedi';
$string['sesotho'] = 'Sesotho';
$string['setswana'] = 'Setswana';
$string['siswati'] = 'Siswati';
$string['spanish_argentinian'] = 'Spanish (Argentinian)';
$string['spanish_castilian'] = 'Spanish (Castilian)';
$string['spanish_american'] = 'Spanish (American)';
$string['spanish_columbian'] = 'Spanish (Columbian)';
$string['spanish_mexican'] = 'Spanish (Mexican)';
$string['norwegian_bokmal'] = 'Norwegian (Bokm&aring;l)';
$string['norwegian_nynorsk'] = 'Norwegian (Nynorska)';
$string['polish'] = 'Polish';
$string['portuguese'] = 'Portuguese';
$string['portuguese_brazilian'] = 'Portuguese (Brazilian)';
$string['romanian'] = 'Romanian';
$string['russian'] = 'Russian';
$string['swedish'] = 'Swedish';
$string['swedish_finnish'] = 'Swedish (Finnish)';
$string['thai'] = 'Thai';
$string['tshivenda'] = 'Tshivenda';
$string['turkish'] = 'Turkish';
$string['ukranian'] = 'Ukranian';
$string['welsh'] = 'Welsh';
$string['xitsonga'] = 'Xitsonga';


// Language.
$string['region'] = 'Region';
$string['region_help'] = 'Select the region for your ReadSpeaker installation.';

// Readid.
$string['readid'] = 'Reading area ID (required)';
$string['readid_help'] = 'The ID of the block level element which is to be read (example "region-main").';

// Region names.
$string['africa'] = 'Africa';
$string['asia'] = 'Asia';
$string['east_asia'] = 'East Asia';
$string['europe'] = 'Europe';
$string['middle_east'] = 'Middle East';
$string['north_america'] = 'North America';
$string['south_america'] = 'South America';
$string['oceania'] = 'Oceania';

// DocReader.
$string['docreader'] = 'Enable docReader';
$string['docreader_help'] = 'Check to enable docReader (docReader must be enabled in your ReadSpeaker account).';

// Player placement.
$string['showincontent'] = 'Listen button placement';
$string['showincontent_help'] = 'The Listen button is by default placed in a block in the sidebar. You can move the player to the top of the content instead.';
$string['showincontent_showinblock'] = 'Show in block';
$string['showincontent_showincontent'] = 'Show in content';

// Choosing to disable webReader in editing mode.
$string['disableinem'] = 'Advanced option: Disable in editing mode.';
$string['disableinem_help'] = 'Check this option to disable the webReader functionality when in editing mode, the block will still display on pages but the Listen button and scripts will not be loaded when in editing mode.';

// Using latest version of webReader for Education scripts.
$string['latestscript'] = 'Advanced option: Latest script version';
$string['latestscript_help'] = 'Check to use the latest version of the webReader for Education scripts for testing purposes (WARNING: latest scripts will always automatically update with new webReader for Education releases).';

// Custom parameters.
$string['customparams'] = 'Advanced option: Custom parameters';
$string['customparams_help'] = 'Specify custom parameters to the ReadSpeaker Listen button (adds in addition to default).';

// Restricted mode configuration.
$string['webreaderfeatures'] = 'webReader features';
$string['webreaderfeatures_help'] = 'This sets the mode for webReader, which in turn controls which webReader listen button features are available.';
$string['standard'] = 'Standard';
$string['restricted'] = 'Restricted';

// For cache.
$string['cachedef_readspeaker_tokens'] = 'DocReader token cache';

// Listen button text.
$string['listentext'] = "Listen";

// Listen button descriptive title text.
$string['listen_titletext'] = "Listen to this page using ReadSpeaker";

// Privacy text.
$string['privacy:metadata'] = 'The ReadSpeaker block does not store any personal information and only displays the ReadSpeaker Listen button.';

// Enable statistics.
$string['statistics'] = 'Statistics: Enable page information';
$string['stats_help'] = 'Check to display the page path instead of the page URL in statistics.';

// Translation
$string['translation'] = 'Translation language';
$string['translation_help'] = 'Select the Page language or the Reading language to be used as the source language (from) for translation.';
$string['translation_page'] = 'Page language';
$string['translation_voice'] = 'Reading language';

// Mobile plugin
$string['mobileapp'] = "Enable mobile plugin";
$string['mobileapp_help'] = "Check to enable the plugin in the Moodle app (also requires the feature to be turned on by ReadSpeaker).";

// Mobile toolbar.
$string['pixels'] = 'Mobile toolbar position';
$string['pixels_help'] = 'Specify how many pixels from the bottom of the screen the mobile toolbar should be placed (example "130").';