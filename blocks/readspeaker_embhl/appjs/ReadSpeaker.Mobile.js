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
 * Mobile support for Readspeakers webReader Moodle block
 *
 * @package    block_readspeaker_embhl
 * @copyright  2024 ReadSpeaker
 * @author     Nikolina Milioni
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


var btnPlace = '' ;
// DOM will be populated with the script_code from mobile.php.
// Add the placeholder for script_code assignment.
if (!window.rsConf) {
    try {
        var script = document.createElement('script');
        script.type = 'text/javascript';
        var dynamicScriptCode = null; 
        // %script_code%
        script.textContent = dynamicScriptCode;
        document.body.appendChild(script);
        
        (function() {
            // Check params
            var customerid = (window.rsConf && window.rsConf.moodle && window.rsConf.moodle.customerid) ? window.rsConf.moodle.customerid : 'default';
            var region = (window.rsConf && window.rsConf.moodle && window.rsConf.moodle.region) ? window.rsConf.moodle.region : 'eu';
            var showInContent = (window.rsConf && window.rsConf.moodle && window.rsConf.moodle.showInContent) ? window.rsConf.moodle.showInContent : '';
            var modeParam = (window.rsConf && window.rsConf.moodle && window.rsConf.moodle.mode !== '') ? '&mode=' + window.rsConf.moodle.mode : '';
            var scriptVersion = (window.rsConf && window.rsConf.moodle && window.rsConf.moodle.latestVersion === '1') ? 'latest' : 'current';
            var enableMobile = (window.rsConf && window.rsConf.moodle && window.rsConf.moodle.mobile) ? window.rsConf.moodle.mobile : '';
            btnPlace = showInContent;


            var scriptSrc = 'https://cdn-%region%.readspeaker.com/script/%customerid%/webReaderForEducation/moodle/' + scriptVersion + '/webReader.js',
                scriptParams = '?pids=embhl' + modeParam;
            scriptSrc = scriptSrc.replace('%customerid%', customerid);
            scriptSrc = scriptSrc.replace('%region%', region);

            window.rsConf.params = scriptSrc + scriptParams;

            var head = document.getElementsByTagName('HEAD').item(0);
            var scriptTag = document.createElement("script");
            scriptTag.setAttribute("type", "text/javascript");
            scriptTag.src = scriptSrc;


            // use body if available. more safe in IE
            (document.body || head).appendChild(scriptTag);

            scriptTag.onload = function() {
                // Script loaded successfully now.
                
                ReadSpeaker.p(function() {
                    // Check if admin has enabled webReader in mobile App.
                    if (enableMobile === "0" || rspkr.item('mobile.moodleEnabled') === false) {
                        findRSButton("#readspeaker_button1");
                    } else {
                        if (showInContent){
                            findRSButton("#readspeaker_button1", true);
                        }
                    }
                })
            };
        })();
    } catch (e) {}
} else {
    // For pull refresh and more than one Listen button in the page.
    try {
        ReadSpeaker.p(function() {
            if (window.rsConf.moodle.mobile === "0" || rspkr.item('mobile.moodleEnabled') === false) {
                findRSButton("#readspeaker_button1");
            } else {
                if (window.rsConf.moodle.showInContent) {
                    findRSButton("#readspeaker_button1", true);
                }
            }
        })
    } catch (e) {}
};

/**
     * Function that removes the Listen button or change its location if 'show in content'.
     *
     * @param {string} targetId
     * @param {boolean} inContent
     * @param {integer} interval
     * @return {Void}
     */
function findRSButton(targetId, inContent = false, interval = 100) {
    var buttonsRS = document.querySelectorAll(targetId);

    if (buttonsRS.length === 0) {
        // If buttonsRS is empty, set a timeout to call the function again until the Listen button is loaded.
        setTimeout(() => {
            findRSButton(targetId, interval);
        }, interval);
    } else {
        if (inContent && rspkr.item('mobile.moodleEnabled') === true) {
            var rsButton = document.getElementById('readspeaker_button1');
            var targetContainer = document.querySelector('core-block');

            // Move the Listen button
            // In case of a pull refresh, the item divider (required for padding) already exists.
            if (rsButton && targetContainer && rsButton.parentElement.tagName !== 'ION-ITEM-DIVIDER') { 
                var ionItemDivider = document.createElement('ion-item-divider');
                // Append rsButton to the ion-item-divider.
                ionItemDivider.appendChild(rsButton.parentElement.removeChild(rsButton));
                // Prepend the ion-item-divider (which now contains rsButton) to the target container.
                targetContainer.prepend(ionItemDivider);
            }
            var card = document.querySelector('ion-card.block_readspeaker_embhl');
            if (card && buttonsRS.length === 1) { // 1 means that we remove the card from dashboard only (1st occurence of rs_button).
                card.remove();
            }
            return
        }
    };

    // Process each Listen button if webReader for mobile is not enabled.
    buttonsRS.forEach(function() {
        var RSCard = document.querySelector('ion-card.block_readspeaker_embhl');
        if (RSCard) {
            RSCard.remove();
        }
    });
    return;
}


// Keep track when we move between pathnames.
(function(window) {
    // Store the previous pathname.
    var previousPathname = window.location.pathname;

    // Function to execute when pathname changes.
    function onPathnameChange() {
        
        var currentPathname = window.location.pathname;

        // We remove the tool panel if 'show in content'.
        if (btnPlace) {
            removePanel();
        } else {
            // Check if the transition is between 'dashboard', 'site' or course content in every direction, for show in block.
            var isDashboardToSite = previousPathname.includes('dashboard') && currentPathname.includes('site');
            var isSiteToDashboard = previousPathname.includes('site') && currentPathname.includes('dashboard');
            var isCoursesToContent = !previousPathname.includes('contents') && currentPathname.includes('contents');

            if (isDashboardToSite || isSiteToDashboard || isCoursesToContent) {
                removePanel();
            }
        }

        // regex to match the course/module id from the pathname.
        var content_regex = /course\/(\d+)\/contents/;
        var mod_regex = /mod_\w+\/\d+\/(\d+)/;
        // Extract the course id.
        var match_content = currentPathname.match(content_regex);
        if (match_content) {
            // The number will be in match[1] (because of the capture group).
            var courseId = match_content[1];
        }
        // Extract the module id.
        var match_mod = currentPathname.match(mod_regex);
        if (match_mod) {
            // The number will be in match[1] (because of the capture group).
            var modId = match_mod[1];
        }

        // Add the listen button in each course (main page) when 'show in content'.
        if (currentPathname.includes('contents') && btnPlace && courses.includes(courseId)) {
            addRSinContent();
        }
        // Add the listen button in each module of a course when 'show in content'.
        if (currentPathname.includes('mod') && btnPlace && modules.includes(modId)) {
            addRSinMod();
        }

        // Update the previous pathname to the current one for future checks.
        previousPathname = currentPathname;
    }

    // Save references to original functions.
    var originalPushState = window.history.pushState;
    var originalReplaceState = window.history.replaceState;

    // Override pushState to detect path changes.
    window.history.pushState = function(...args) {
        originalPushState.apply(this, args);
        onPathnameChange();
    };

    // Override replaceState to detect path changes.
    window.history.replaceState = function(...args) {
        originalReplaceState.apply(this, args);
        onPathnameChange();
    };

    // Listen for 'popstate' events, which are triggered by browser navigation (back/forward).
    window.addEventListener('popstate', onPathnameChange);
    
    onPathnameChange();

})(window);


// Close tool panel when we move within sections (same pathname).
document.addEventListener('click', function(event) {
    // Check if the clicked element or any of its parent elements is the 'ion-button[expand="block"]'.
    // Close panel when Section button is clicked.
    var expandButton = event.target.closest('ion-button[expand="block"]');

    // Close panel when we exit course.
    // Check for ion-back-button.
    var backButton = event.target.matches('ion-back-button');
    var arrowButton = event.target.closest('ion-row');

    if (backButton || arrowButton || expandButton) {
        removePanel(); 
    }
});

// Close ('show in content') or stop ('show in block') tool panel when we move between activities in a section.
document.addEventListener('click', function(event) {
// Check if the clicked element matches <ion-card> with the class 'activity-card'.
    if (event.target.closest('ion-card.activity-card') && btnPlace) {
        removePanel()
    } else if (event.target.closest('ion-card.activity-card') && !btnPlace) {
        stopPanel()
    }
});


/**
     * Function that adds button in course page when 'show in content'.
     *
     * @return {Void}
     */
function addRSinContent() {

    var interval = setInterval(function() {
        // var targetContainer = document.querySelector('section.core-course-module-list-wrapper.ng-star-inserted');
        var targetContainer = document.querySelector('core-course-format');

        if (targetContainer) {
            // Stop checking once the target container is found.
            clearInterval(interval);
            var rsButton = document.getElementById('readspeaker_button1');
            // Check if the button has already been prepended by creating a custom class.
            if (!targetContainer.querySelector('.cloned-readspeaker-button') &&  rsButton) {
                // Create a copy of the button ('true' means it clones the node and all of its child nodes).
                var clonedButton = rsButton.cloneNode(true);
                
                // Add a class to the cloned button to mark it as inserted.
                clonedButton.classList.add('cloned-readspeaker-button');

                // Create a new ion-item-divider element.
                var ionItemDivider = document.createElement('ion-item-divider');
                
                // Append the cloned button to the ion-item-divider.
                ionItemDivider.appendChild(clonedButton);

                // Insert the ion-item-divider (containing the cloned button) into the target container.
                targetContainer.prepend(ionItemDivider);
            }
        
        }
    }, 100);
}

/**
     * Function that adds button in activity page when 'show in content'.
     *
     * @return {Void}
     */
function addRSinMod() {

    var interval = setInterval(function() {
        var targetContainer = '' ;
        document.querySelectorAll('*').forEach(function(element) {
            if (element.tagName.toLowerCase().startsWith('addon-mod-')) {
                targetContainer = element;
            }
        });

        if (targetContainer) {
            clearInterval(interval);
            var rsButton = document.getElementById('readspeaker_button1');
            if (!targetContainer.querySelector('.cloned-readspeaker-button') && rsButton) {
                var clonedButton = rsButton.cloneNode(true);
                
                // Add a class to the cloned button to mark it as inserted.
                clonedButton.classList.add('cloned-readspeaker-button');
                
                // Create a new ion-item-divider element.
                var ionItemDivider = document.createElement('ion-item-divider');

                // Append the cloned button to the ion-item-divider.
                ionItemDivider.appendChild(clonedButton);

                // Insert the ion-item-divider (containing the cloned button) into the target container.
                targetContainer.prepend(ionItemDivider);
            }
        
        }
    }, 100);
}

/**
     * Function that removes the tool panel.
     *
     * @return {Void}
     */
function removePanel() {
    var controlPanel = document.getElementById('rsmpl_container');

    if (controlPanel) {
        // Find the div with the specific classes.
        var closerDiv = controlPanel.querySelector('div.rsmpl-closer.rsmpl-tool');
    
        if (closerDiv) {
            // Find the X button in the panel.
            var closeButton = closerDiv.querySelector('button[type="button"]');
    
            if (closeButton) {
                closeButton.click();
            }
        }
    } 
}

/**
     * Function that stops reading the content without closing the tool panel.
     *
     * @return {Void}
     */
function stopPanel() {
    var controlPanel = document.getElementById('rsmpl_container');
    if (controlPanel && !btnPlace) {
        var stopDiv = controlPanel.querySelector('div.rsmpl-stop.rsmpl-tool');
        if (stopDiv) {
            // Find the Stop button in the panel.
            var stopButton = stopDiv.querySelector('button[type="button"]');
    
            if (stopButton) {
                stopButton.click();
            }
        }
    }
}