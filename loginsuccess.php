<?php
require_once(dirname(dirname(__FILE__)) . '../../config.php');
defined('MOODLE_INTERNAL') || die();
global $PAGE, $CFG, $DB, $OUTPUT,$USER;
$PAGE->set_context(context_system::instance());
require_login();
require_once($CFG->dirroot.'/blocks/enrollforms/formslib.php');
$PAGE->set_pagelayout('admin');
$PAGE->set_url($CFG->wwwroot . "/blocks/enrollforms/loginsuccess.php");
$PAGE->set_title(get_string("thankyouforcreatingaccount",'block_enrollforms'));
$PAGE->set_heading(get_string("thankyouforcreatingaccount", "block_enrollforms"));
$PAGE->navbar->add((get_string('addstudent','block_enrollforms')), new moodle_url('/blocks/enrollforms/loginsuccess.php'));
echo $OUTPUT->header();
$html ='<div class="card" style="text-align: center;">
  <div class="card-body">
    <h3>'.get_string('thankyouforcreatingaccount','block_enrollforms').'</h3>
    <p class="card-text">'.get_string('choosebtn','block_enrollforms').'</p>
    <a href="addstudent.php" class="btn btn-warning" style="color:#fff;">'.get_string('addastudent','block_enrollforms').'</a>
    <a href="'.$CFG->wwwroot.'/my" class="btn btn-success" style="color:#fff;">'.get_string('gotoyouredbasecampcourses','block_enrollforms').'</a>
  </div>
</div>';
echo $html;

echo $OUTPUT->footer();