YUI.add("moodle-block_readspeaker_embhl-ReadSpeaker", function(){

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
 * @copyright  2016 ReadSpeaker
 * @author     Richard Risholm
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

    M.block_RS = M.block_RS || {};
    M.block_RS.ReadSpeaker = {
        init: function() {

            // Re-activate the detachment feature after clicking on the Listen button, if detachment is enabled.
            YUI().use('node', function(Y) {
                var handleEvent = function(event) {
                    var target = event.target;
                    if (target.hasClass('close-drag-btn') && (event.type === 'click' || (event.type === 'keydown' && event.keyCode === 13))) {
                        rsConf.ui.disableDetachment = true;
            
                        // Remove the rsdetached class.
                        var dragdropElements = Y.all('.rsdetached');
                        if (dragdropElements) {
                            dragdropElements.each(function(el) {
                                el.removeClass('rsdetached');
                                el.setAttribute('style', '');
                                el.setStyle('top', 'auto');
                                el.setStyle('left', 'auto');
                            });
                        }
            
                        // Hide the rsbtn_detach_clone div.
                        var detachCloneElement = Y.one('#rsbtn_detach_clone');
                        if (detachCloneElement) {
                            detachCloneElement.setStyle('display', 'none');
                        }
                    }
                };
            
                // Attach event listeners for click and keydown.
                Y.one(document).delegate('click', handleEvent, '.close-drag-btn');
                Y.one(document).delegate('keydown', handleEvent, '.close-drag-btn');
            });
            

            var readspeakerButton = Y.one('#readspeaker_button1');
            if (readspeakerButton) {
                readspeakerButton.on('click', function() {
                    if (rspkr.features.detachment && rsConf.ui.disableDetachment) {
                        var observer = new IntersectionObserver(function(entries) {
                            entries.forEach(function(entry) {
                                if (!entry.isIntersecting) {
                                    // When readspeaker_button1 is not visible in the viewport.
                                    rsConf.ui.disableDetachment = false;

                                    var readspeakerButton = Y.one('#readspeaker_button1');
                                    if (readspeakerButton) {
                                        readspeakerButton.addClass('rsdetached');
                                        readspeakerButton.setStyle('top', '300px');
                                        readspeakerButton.setStyle('left', '700px');
                                    }

                                    var detachCloneElement = Y.one('#rsbtn_detach_clone');
                                    if (detachCloneElement) {
                                        detachCloneElement.setStyle('display', '');
                                    }

                                    // Stop observing after the first trigger.
                                    observer.unobserve(entry.target);
                                }
                            });
                        }, { threshold: [0] }); // 0: even a single pixel out of view will trigger the callback.
                        if (readspeakerButton) {
                            observer.observe(readspeakerButton.getDOMNode());
                        }
                    }
                });
            }

            if (!window.rsConf) window.rsConf = {};

            // Check params
            var docreaderParam = (window.rsDocReaderConf && window.rsDocReaderConf.cid) ? '&revdload=DocReader.AutoAdd' : '';
            var customerid = (window.rsConf && window.rsConf.moodle && window.rsConf.moodle.customerid) ? window.rsConf.moodle.customerid : 'default';
            var region = (window.rsConf && window.rsConf.moodle && window.rsConf.moodle.region) ? window.rsConf.moodle.region : 'eu';

            var showInContent = (window.rsConf && window.rsConf.moodle && window.rsConf.moodle.showInContent) ? window.rsConf.moodle.showInContent : '';
            var editingMode = (window.rsConf && window.rsConf.moodle && window.rsConf.moodle.em === '1') ? true : false;
            var modeParam = (window.rsConf && window.rsConf.moodle && window.rsConf.moodle.mode !== '') ? '&mode=' + window.rsConf.moodle.mode : '';
            var scriptVersion = (window.rsConf && window.rsConf.moodle && window.rsConf.moodle.latestVersion === '1') ? 'latest' : 'current';


            var scriptSrc = 'https://cdn-%region%.readspeaker.com/script/%customerid%/webReaderForEducation/moodle/' + scriptVersion + '/webReader.js',
                scriptParams = '?pids=embhl' + docreaderParam + modeParam;
            scriptSrc = scriptSrc.replace('%customerid%', customerid);
            scriptSrc = scriptSrc.replace('%region%', region);

            window.rsConf.params = scriptSrc + scriptParams;

            var head = document.getElementsByTagName('HEAD').item(0);
            var scriptTag = document.createElement("script");
            scriptTag.setAttribute("type", "text/javascript");
            scriptTag.src = scriptSrc;

            var callback = function() {
                ReadSpeaker.init();
                if (showInContent) {
                    var rsButton = document.getElementById('readspeaker_button1'),
                    readArea = document.getElementById(showInContent);
                    // Remove the compact class
                    rsButton.classList.remove('rscompact');
                    // Move the Listen button
                    if (rsButton && readArea) {
                        readArea.prepend(rsButton.parentElement.removeChild(rsButton));
                    }
                    // Remove the empty block if we are NOT in editing mode. Then keep the block, so user can change settings.
                    if (!editingMode) {
                        var rsBlocks = document.getElementsByClassName('block_readspeaker_embhl block');
                        for (var i = 0; i < rsBlocks.length; i++) {
                            rsBlocks[0].style.display = "none";
                        }
                    }
                }
            };

            scriptTag.onreadystatechange = scriptTag.onload = function() {
                var state = scriptTag.readyState;
                if (!callback.done && (!state || /loaded|complete/.test(state))) {
                    callback.done = true;
                    callback();
                }
            };

            // use body if available. more safe in IE
            (document.body || head).appendChild(scriptTag);
        }
    };
}, "@VERSION@", {
    requires: ["node"]
});
