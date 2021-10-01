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
 * @copyright  2021 welkins system
 * @author
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(__FILE__)) . '../../config.php');

defined('MOODLE_INTERNAL') || die();
global $PAGE, $CFG, $DB, $OUTPUT;
$PAGE->set_context(context_system::instance());
require_login();
if(!is_siteadmin()){
  die();
}

require_once(dirname(dirname(__FILE__)) . '../../lib/grouplib.php');

require_once($CFG->dirroot.'/blocks/enrollforms/locallib.php');
require_once($CFG->dirroot.'/blocks/enrollforms/formslib.php');
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string("pluginname", "block_enrollforms"));
$PAGE->set_heading(get_string("pluginname", "block_enrollforms"));
//$PAGE->navbar->ignore_active();
$PAGE->navbar->add((get_string('pluginname','block_enrollforms')), new moodle_url('/blocks/enrollforms/view.php'));
$PAGE->set_url($CFG->wwwroot . "/blocks/enrollforms/view.php");
$PAGE->requires->jquery();
echo $OUTPUT->header();

// TEMPORARY FOR DISPLAYIN CONFIRMATION URL FOR TEACHERS
//echo $CFG->wwwroot.'/blocks/enrollforms/account.php?user='.md5(3);

$mform = new addteacher_form();
if ($mform->is_cancelled()) {
  $currenturl = "$CFG->wwwroot/blocks/enrollforms/view.php";
 redirect($currenturl,'',false);
}elseif($mform->is_submitted()){

  $update = $DB->get_record('enroll_users',array('email'=>$_POST['email']));


  if($update){

    $update->total_allotment = $_POST['totalallottedstudents'];
    $update->teacherenrolledcourse = $_POST['teacherenrolledcourses'];
    $update->teachergroups = $_POST['teachergroups'];
    $update->firstname = $_POST['firstname'];
    $update->lastname = $_POST['lastname'];
    $DB->update_record('enroll_users',$update);
    sendmailtoteacher($update);
  }else {
    $insert = new stdClass();
    $insert->firstname = $_POST['firstname'];
    $insert->lastname = $_POST['lastname'];
    $insert->email = $_POST['email'];
    $insert->type = 'teacher';
    $insert->total_allotment = $_POST['totalallottedstudents'];
    $insert->teacherenrolledcourse = $_POST['teacherenrolledcourses'];
    $insert->teachergroups = $_POST['teachergroups'];
    $insert->status = 1;

    $insert->id = $DB->insert_record('enroll_users', $insert);
    sendmailtoteacher($insert);
  }
  $destination = "$CFG->wwwroot/blocks/enrollforms/view.php";


  redirect($destination, get_string('teacheraddedsuccessfully','block_enrollforms'), null, \core\output\notification::NOTIFY_SUCCESS);
}else{


 echo $mform->display();
}
echo $OUTPUT->footer();

 ?>
