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
 * enrollforms
 * @package    block_enrollforms
 * @copyright  2021 enrollforms
 * @author
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(__FILE__)) . '../../config.php');

defined('MOODLE_INTERNAL') || die();
global $PAGE, $CFG, $DB, $OUTPUT,$USER;
$PAGE->set_context(context_system::instance());
require_login();

//$enrolltype = $DB->count_records('enroll_users',array('email'=>$USER->email, 'type'=>'teacher'));
//if ($enrolltype==0) {
//    redirect('../../', 'You are not authorised to add students.', null, \core\output\notification::NOTIFY_ERROR);
//    exit;
//}

require("teacherinfo_table.php");

$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string("teacherinfo", "block_enrollforms"));
$PAGE->set_heading(get_string("teacherinfo", "block_enrollforms"));
$PAGE->navbar->ignore_active();
$PAGE->set_url($CFG->wwwroot . "/blocks/enrollforms/teacherinfo.php");
$PAGE->requires->jquery();
$PAGE->navbar->add((get_string('teacherinfo','block_enrollforms')), new moodle_url('/blocks/enrollforms/teacherinfo.php'));

//$download = optional_param('download', '', PARAM_ALPHA);

//$PAGE->requires->js( '',true);

require_once('locallib.php');

$table = new teacherinfo_table('uniqueid');



if (!isset($_GET["download"])) {
    echo $OUTPUT->header();
}

//$filename = "Student Information - " . $USER->firstname . " " . $USER->lastname . " - " . date("m-d-Y");

//$table->is_downloading($download, $filename, 'sheet1');

$table->no_sorting('operations');
$table->no_sorting('noofcourse');

$table->set_sql('*', "{enroll_users}", "type='teacher'");
$table->define_baseurl("$CFG->wwwroot/blocks/enrollforms/teacherinfo.php");
$table->out(10, true);

//$enroll = $DB->get_record('enroll_users',array('email'=>$USER->email));
//$noofenroll = $DB->count_records('enroll_users',array('createdby'=>$USER->id));
//
//echo  '<h3 style="font-weight: 400;">'.get_st ring('totalenrolledstudents','block_enrollforms',array('count'=>$noofenroll)).'</h3>';
//echo  '<h3 style="font-weight: 400;">'.get_string('availabletoseats','block_enrollforms',array('noallotment' => ($enroll->total_allotment -($noofenroll)))).'</h3>';

if (!isset($_GET["download"])) {
    echo $OUTPUT->footer();
}
?>