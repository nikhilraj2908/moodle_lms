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
 * docReader proxy for document retrieval.
 * 
 * This is a proxy component constructed to cache the user session and generate a temporary token,
 * the token is provided to docReader in a call which docReader will then return as a cookie when
 * attempting to fetch the document. The proxy will check the cache for the cookie and make a request
 * to the document location using the stored information and will return the result back to docReader.
 *
 * @package    block_readspeaker_embhl
 * @copyright  2016 ReadSpeaker <info@readspeaker.com>
 * @author     Richard Risholm
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Require the Moodle configuration.
require_once('../../../config.php');
global $CFG;
require_once("$CFG->libdir/filelib.php");

// Get what stage we're going through.
$current_stage = optional_param('stage', 'init', PARAM_ALPHA);
if ($current_stage == 'fetch') {
    // Get the document to fetch.
    $document_url = required_param('url', PARAM_URL);

    // Check that the request is made towards the Moodle server.
    if (parse_url($document_url, PHP_URL_HOST) != parse_url($CFG->wwwroot, PHP_URL_HOST)) {
        echo "Request towards \"" . $document_url . "\" is not allowed.";
        http_response_code(403);
        die();
    }

    // Check if the url is empty.
    if ($document_url == '') {
        echo "Error: Invalid URL provided.";
        http_response_code(404);
        die();
    }

    // Get the token.
    $use_token = true;
    if (isset($_COOKIE['DocReaderToken'])) {
        $token = s($_COOKIE['DocReaderToken']);
    } else {
        // The URL is being accessed directly, redirect to the document instead
        header('Location: ' . $document_url);
        http_response_code(302);
        die();
    }

    if ($use_token) {
        // Make connection to cache and save the cookie and token.
        $cache = cache::make('block_readspeaker_embhl', 'readspeaker_tokens');
        // Get the information from the cache.
        $cookies = $cache->get($token);

        if ($cookies) {
            // Delete the token from the cache.
            $cache->delete($token);
        }
    }

    // Check if user is authenticated.
    if (!$cookies) {
        echo "Error: Unable to lookup session information.";
        http_response_code(403);
        die();
    }

    // Close the session to allow concurrent requests.
    @session_start();
    @session_write_close();

    // This is where the request comes from the DocReader server.
    $curl = new curl();

    // Set some options.

    $options = array(
        "CURLOPT_RETURNTRANSFER" => true,
        "CURLOPT_URL" => $document_url,
        "CURLOPT_COOKIE" => $cookies,
        "CURLOPT_FOLLOWLOCATION" => true,
        // Do not cache results.
        "CURLOPT_FRESH_CONNECT" => true,
        // Set timeout values.
        "CURLOPT_CONNECTTIMEOUT" => 5,
        "CURLOPT_TIMEOUT" => 5,
        // Set option to enable temporary cookiejar.
        "CURLOPT_COOKIEFILE" => "-"
    );

    // Get the response from the server.
    $result = $curl->get($document_url, array(), $options);
    // Check for errors.
    $error = $curl->get_errno();

    // Check if the request returned a result successfully.
    $http_code = $curl->info["http_code"];
    $problem = ($http_code != 200) ? true : ($error != 0);
    if ($problem) {
        // Print the result as it will likely be an error message.
        echo $result;
        http_response_code($http_code);
        die();
    }

    // Remove all current headers to send.
    header_remove();

    // Get the response headers for the request.
    $header_array = $curl->get_raw_response();
    // Process response headers and send to client.
    foreach($header_array as $header) {
        $colon_position = strpos($header, ':');
        if ($colon_position !== FALSE) {
            $header_name = substr($header, 0, $colon_position);

            // Ignore content headers, let the webserver decide how to deal with the content.
            if (trim(strtolower($header_name)) == 'content-encoding') continue;
            if (trim(strtolower($header_name)) == 'content-length') continue;
            if (trim(strtolower($header_name)) == 'transfer-encoding') continue;
            // Since we are sending back the file, sending the location header is kinda unnecessary.
            if (trim(strtolower($header_name)) == 'location') continue;
        }
        // Set header, overwrite duplicates.
        header($header, TRUE);
    }

    // Echo the resulting file.
    echo $result;
} else {
    // This is where the request is coming from the user.
    // The user should be logged in when they make a request towards the proxy.
    require_login();

    // Check that curl exists.
    if (new curl() === false) {
        echo "Error: Missing component in library (curl).";
        http_response_code(503);
        die();
    }

    // Get all cookies
    $check_cookie = array(
        'MOODLEID_'.$CFG->sessioncookie,
        'MoodleSession'.$CFG->sessioncookie,
        'MoodleSessionTest'.$CFG->sessioncookie
    );
    $cookies = array();
    foreach ($check_cookie as $check) {
        if (isset($_COOKIE[$check])) {
            $cookies[] = $check . '=' . $_COOKIE[$check];
        }
    }

    $sessioncookie = s(implode(';', $cookies));

    // Generate a random token.
    $token = random_string(20);

    // Save cookie and token in cache.
    $cache = cache::make('block_readspeaker_embhl', 'readspeaker_tokens');
    if (!$cache->set($token, $sessioncookie)) {
        echo "Error: Failed to connect to cache, has it been set up?";
        http_response_code(503);
        die();
    }

    // Create a cookie to send from the token.
    $cookie = 'DocReaderToken='.$token;

    // Close the session to allow concurrent requests.
    session_write_close();

    // Redirect the user to DocReader with the token.
    $redirect = str_replace('&amp;', '&', 'https://docreader.readspeaker.com/docreader/?'.$_SERVER['QUERY_STRING'].'&sessioncookie='.urlencode($cookie));
    header('Location: ' . $redirect);
    http_response_code(302);
    die();
}