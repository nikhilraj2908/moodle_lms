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

define(['jquery', 'core/modal_factory', 'core/str', 'core/templates'], function ($, ModalFactory, Str, templates) {
    var translation_button = {
        'returnurl': '',
        'init': function (returnurl) {
            translation_button.returnurl = returnurl;
            // Register both right and left click handlers on the translation button - we need both as it will sometimes
            // conflict with the left click action on elements in front.
            $('body').on('click', '.filter_translations_btn_translate', translation_button.opentranslation);
            $('body').on('contextmenu', '.filter_translations_btn_translate', translation_button.opentranslation);
        },
        'findandinjectbuttons': function() {
            var encodedone = "\u{200B}"; // Zero-Width Space
            var encodedzero = "\u{200C}"; // Zero-Width Non-Joiner
            var encodedseperator = "\u{200D}"; // Zero-Width Joiner

            // Two encodedseperator next to each other indicates a placeholder to swap for a translation button.
            var elems = translation_button.findElementsDirectlyContainingText(document, encodedseperator + encodedseperator);
            let itemsprocessed = 0;
            let missingitems = [];
            elems.forEach(function(elem) {
                var matches = new RegExp(encodedseperator + encodedseperator + '([' + encodedone + encodedzero + ']*)');

                var regexzero = new RegExp(encodedzero, 'g');
                var regexone = new RegExp(encodedone, 'g');

                // Decode the inpagetranslationid so we can grab the translation info from translation_button.objects.
                var binary = matches.exec($(elem).html())[1].replace(regexone, '1').replace(regexzero, '0');
                var key = parseInt(binary, 2);
                var translationinfo = translation_button.objects[key];

                // Render the translation button.
                templates.render('filter_translations/translatebutton', translationinfo).done(function (html) {
                    $(elem).append(html);

                    // Some content may be rendered multiple times, such as in title attributes, for screenreaders
                    // and hidden for display in responsive views.
                    // Do not count duplicates.
                    if (translationinfo.notranslation == true) {
                        if (!missingitems.includes(translationinfo.generatedhash)) {
                            missingitems.push(translationinfo.generatedhash);
                        }
                    }

                    itemsprocessed++;
                    if (itemsprocessed === elems.length) {
                        // Last inline button has been added. Now we can start counting.
                        // There will always be one <title> element, but get its count anyways.
                        const titlemissing = document.querySelectorAll('title .icon-translate-cross').length;
                        const missingitemscount = missingitems.length - titlemissing;

                        // Count missing and stale translations and inject the numbers into the menu.
                        // This has to be done in JS as we need to render the menu in the header but don't know the number
                        // until the whole page has renderred.
                        let stalecount = 0;
                        for (let k in translation_button.objects) {
                            if (translation_button.objects[k].staletranslation) {
                                stalecount += 1;
                            }
                        }

                        const totalcount = stalecount + missingitemscount;

                        $('.translation-icon-wrapper i').after(
                            '<div class="count-container " data-region="count-container">'
                            + translation_button.cap_count(totalcount)
                            + '</div>'
                        );

                        $('.translation-icon-wrapper a.missingonthispage').html(
                            $('.translation-icon-wrapper a.missingonthispage').html()
                            + ' (' +  translation_button.cap_count(missingitemscount) + ')'
                        );

                        $('.translation-icon-wrapper a.staleonthispage').html(
                            $('.translation-icon-wrapper a.staleonthispage').html()
                            + ' (' +  translation_button.cap_count(stalecount) + ')'
                        );
                    }
                });
            });
        },
        'opentranslation': function (event) {
            event.stopPropagation();
            event.preventDefault();

            var context = translation_button.objects[$(this).data('inpagetranslationid')];
            context.returnurl = translation_button.returnurl;

            // Show the translation modal.
            Str.get_strings([{
                key: 'translationdetails',
                component: 'filter_translations'
            }]).then(function (langStrings) {
                return templates.render('filter_translations/translationdetailsmodalbody', context).done(function (html) {
                    ModalFactory.create({
                        title: langStrings[0],
                        body: html,
                        type: ModalFactory.types.ALERT
                    }).then(function (modal) {
                        modal.show();
                    });
                });
            }).fail(Notification.exception);
        },
        'register': function (key, translationinfo) {
            translation_button.objects[key] = translationinfo;
        },
        // Utility function to find elements that contain a piece of text directly, rather than in a descendant.
        'findElementsDirectlyContainingText': function (ancestor, text) {
            var elements = [];
            walk(ancestor);
            return elements;

            function walk(element) {
                var n = element.childNodes.length;
                for (var i = 0; i < n; i++) {
                    var child = element.childNodes[i];
                    if (child.nodeType === 3 && child.data.indexOf(text) !== -1) {
                        elements.push(element);
                        break;
                    }
                }
                for (var i = 0; i < n; i++) {
                    var child = element.childNodes[i];
                    if (child.nodeType === 1) {
                        walk(child);
                    }
                }
            }
        },
        'cap_count': function (count) {
            if (count < 100) {
                return count;
            } else {
                return '99+';
            }
        },
        objects: {}
    };

    return translation_button;
});
