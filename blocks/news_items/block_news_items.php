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
 * This file contains the news item block class, based upon block_base.
 *
 * @package    block_news_items
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Class block_news_items
 *
 * @package    block_news_items
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_news_items extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'block_news_items');
    }

    function get_content() {
        global $CFG, $USER;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        if (empty($this->instance)) {
            return $this->content;
        }

        if ($this->page->course->newsitems) {
            require_once($CFG->dirroot.'/mod/forum/lib.php');

$text="";
            // Add custom CSS styles
            $text .= '
            <style>
            .block_news_items,
.block_calendar_month {
    max-height: 400px !important;
    overflow-y: auto;
}

.block_news_items .card-body,
.block_calendar_month .card-body {
    max-height: 360px;
    overflow-y: auto;
}

.block_news_items::-webkit-scrollbar,
.block_calendar_month::-webkit-scrollbar {
    width: 6px;
}
.block_news_items::-webkit-scrollbar-thumb,
.block_calendar_month::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 4px;
}
            .block_news_items .bg-announcements {
                background: url("https://cdn.pixabay.com/photo/2020/04/10/19/17/notification-5024558_960_720.png") no-repeat center;
                background-size: cover;
                padding: 20px;
                border-radius: 10px;
            }

            .announcement-card {
                background: #fff;
                border-left: 5px solid #204070;
                border-radius: 10px;
                margin-bottom: 15px;
                box-shadow: 0 4px 8px rgba(0,0,0,0.08);
                padding: 15px 20px;
                position: relative;
                display: flex;
                gap: 15px;
                align-items: center;
            }

            .announcement-icon {
                width: 40px;
                height: 40px;
                background: url("https://cdn-icons-png.flaticon.com/512/1827/1827392.png") no-repeat center;
                background-size: contain;
                position: relative;
            }

            .announcement-icon::after {
                content: "";
                position: absolute;
                top: -5px;
                right: -5px;
                background: red;
                width: 12px;
                height: 12px;
                border-radius: 50%;
                border: 2px solid #fff;
            }

            .announcement-content {
                flex: 1;
            }

            .announcement-title {
                font-weight: bold;
                font-size: 1rem;
                color: #333;
                text-decoration: none;
            }

            .announcement-title:hover {
                text-decoration: underline;
            }

            .announcement-meta {
                font-size: 0.85em;
                color: #777;
                margin-top: 4px;
            }


            /* Wrapper for the two blocks */
#block-region-content {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    justify-content: space-around;
    margin:20px
}

/* Left block (calendar) */
.block_calendar_month {
    flex: 1 1 48%;
    max-width: 600px;
    
}

/* Right block (announcements) */
.block_news_items {
    flex: 1 1 48%;
    max-width: 600px;
}
#region-main .maincalendar .calendarwrapper td>div {
     height: 2em;
    overflow: hidden;
}
   
            </style>
            ';

            if (!$forum = forum_get_course_forum($this->page->course->id, 'news')) {
                return '';
            }

            $modinfo = get_fast_modinfo($this->page->course);
            if (empty($modinfo->instances['forum'][$forum->id])) {
                return '';
            }
            $cm = $modinfo->instances['forum'][$forum->id];

            if (!$cm->uservisible) {
                return '';
            }

            $context = context_module::instance($cm->id);

            if (!has_capability('mod/forum:viewdiscussion', $context)) {
                return '';
            }

            $groupmode    = groups_get_activity_groupmode($cm);
            $currentgroup = groups_get_activity_group($cm, true);

            if (forum_user_can_post_discussion($forum, $currentgroup, $groupmode, $cm, $context)) {
                $text .= '<div class="newlink"><a href="'.$CFG->wwwroot.'/mod/forum/post.php?forum='.$forum->id.'">'.
                          get_string('addanewtopic', 'forum').'</a>...</div>';
            }

            $sort = forum_get_default_sort_order(true, 'p.modified', 'd', false);
            if (! $discussions = forum_get_discussions($cm, $sort, false,
                                                        -1, $this->page->course->newsitems,
                                                        false, -1, 0, FORUM_POSTS_ALL_USER_GROUPS) ) {
                $text .= '('.get_string('nonews', 'forum').')';
                $this->content->text = $text;
                return $this->content;
            }

            $strposttimeformat = get_string('strftimedatetime', 'core_langconfig');

            $text .= '<div class="bg-announcements">';
            $text .= "\n<ul class='unlist'>\n";
            foreach ($discussions as $discussion) {
                $discussion->subject = format_string($discussion->name, true, $forum->course);
                $posttime = $discussion->modified;
                if (!empty($CFG->forum_enabletimedposts) && ($discussion->timestart > $posttime)) {
                    $posttime = $discussion->timestart;
                }

                $userfullname = $discussion->userdeleted 
                               ? get_string('deleteduser', 'mod_forum')
                               : fullname($discussion, has_capability('moodle/site:viewfullnames', $context));

                $text .= '<li class="post ">
                    <div class="announcement-card ">
                        <div class="announcement-icon"></div>
                        <div class="announcement-content">
                            <a href="'.$CFG->wwwroot.'/mod/forum/discuss.php?d='.$discussion->discussion.'" class="announcement-title">'.$discussion->subject.'</a>
                            <div class="announcement-meta">'.userdate($posttime, $strposttimeformat).' &mdash; '.$userfullname.'</div>
                        </div>
                    </div>
                </li>';
            }
            $text .= "</ul>\n</div>";

            $this->content->text = $text;
            $this->content->footer = '<a href="'.$CFG->wwwroot.'/mod/forum/view.php?f='.$forum->id.'">'.
                                      get_string('oldertopics', 'forum').'</a> ...';

            if (isset($CFG->enablerssfeeds) && isset($CFG->forum_enablerssfeeds) &&
                $CFG->enablerssfeeds && $CFG->forum_enablerssfeeds && $forum->rsstype && $forum->rssarticles) {
                require_once($CFG->dirroot.'/lib/rsslib.php');
                $tooltiptext = ($forum->rsstype == 1) 
                             ? get_string('rsssubscriberssdiscussions','forum')
                             : get_string('rsssubscriberssposts','forum');
                $userid = isloggedin() ? $USER->id : $CFG->siteguest;
                $this->content->footer .= '<br />'.rss_get_link($context->id, $userid, 'mod_forum', $forum->id, $tooltiptext);
            }
        }

        return $this->content;
    }
}