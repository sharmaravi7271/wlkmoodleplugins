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
 * Bulk Activity Creation
 * @package    block_enrollforms
 * @copyright  2021 welkins systme
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * The bulk activity creation block class
 */
class block_enrollforms extends block_base {
    public function init() {
        $this->title = get_string('pluginname', __CLASS__);
        $this->version = 2020110909;
    }

    public function applicable_formats() {
        return array(
            'site' => true,
            'course' => true,
            'course-category' => true,
            'mod' => true,
            'my' => true,
            'tag' => true,
            'admin' => true,
        );
    }

    public function instance_can_be_docked() {
        return false; // AJAX won't work with Dock.
    }

    public function has_config() {
        return false;
    }

    /**
     *  Get the block content
     *
     * @return object|string
     * @global object $USER
     * @global object $CFG
     */

    function deleteuserformenrolltable()
    {
        global $DB;
        $users = $DB->get_records('enroll_users', array('type' => 'student'));
        foreach ($users as $user) {
            $course = $DB->get_record('course', array('idnumber' => $user->teacherenrolledcourse));
            if (!empty($course)) {
                $context = context_course::instance($course->id);
                $cuser = $DB->get_record('user', array('email' => $user->email));
                if (isset($cuser->id)) {
                    if (!is_enrolled($context, $cuser)) {
                        $DB->delete_records('enroll_users', array('id' => $user->id));
                    }
                } else {
                    $DB->delete_records('enroll_users', array('id' => $user->id));
                }

            }
        }
    }

    public function get_content() {
        global $CFG, $USER,$DB,$PAGE,$COURSE;

if ($USER->id > 0) {
    $this->deleteuserformenrolltable();
    $showit = 0;
    if (is_siteadmin()) {
        $html = '<a href="' . $CFG->wwwroot . '/blocks/enrollforms/view.php">' . get_string('pluginname', 'block_enrollforms') . '</a>';
        $showit = 1;
    } else {
        if ($DB->count_records('enroll_users', array('email' => $USER->email, 'type'=>'teacher'))) {

            $html = '<a href="' . $CFG->wwwroot . '/blocks/enrollforms/addstudent.php">' . get_string('addstudent', 'block_enrollforms') . '</a><br>';
            $html .= '<a href="' . $CFG->wwwroot . '/blocks/enrollforms/studentinfo.php">' . get_string('studentinfo', 'block_enrollforms') . '</a>';
            $showit = 1;
        }
    }


    $footer = '<div style="display:block;">'
        . '<div class="header-commands">' . $this->get_header() . '</div>'
        . '</div>';
    if ($showit==1) {
        return $this->content = (object)array('text' => $html, 'footer' => $footer);
    }
    }
}

    /**
     * Get the block header
     *
     * @return string
     * @global core_renderer $OUTPUT
     */
    private function get_header() {
        global $OUTPUT;
        // Link to bulkdelete
        $alt = get_string('enrollforms', __CLASS__);
        $src = $OUTPUT->image_url('enrollforms', __CLASS__);
        $url = new moodle_url('/blocks/enrollforms/view.php');
    }





    /**
     * Check Moodle 3.2 or later.
     *
     * @return boolean.
     */
    private function is_special_version() {
        return moodle_major_version() >= 3.2;
    }

}
