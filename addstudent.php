<?php
require_once(dirname(dirname(__FILE__)) . '../../config.php');
defined('MOODLE_INTERNAL') || die();
global $PAGE, $CFG, $DB, $OUTPUT,$USER;
$PAGE->set_context(context_system::instance());
require_login();
require_once($CFG->dirroot.'/blocks/enrollforms/formslib.php');
require_once($CFG->dirroot.'/blocks/enrollforms/locallib.php');
require_once($CFG->dirroot . '/user/lib.php');

$page = optional_param('page','1',PARAM_INT);

if($page==2) {
    $cc = $_POST["courses"];
    $ccvalue = 0;
    foreach ($cc as $key => $value) {
        $ccvalue = $ccvalue + $value;
    }
    if ($ccvalue == 0) {
        redirect('addstudent.php', 'Please select one or more course(s).', null, \core\output\notification::NOTIFY_ERROR);
        exit;
    }
}

$enrolltype = $DB->count_records('enroll_users',array('email'=>$USER->email, 'type'=>'teacher'));
if ($enrolltype==0) {
    redirect('../../', 'You are not authorised to add students.', null, \core\output\notification::NOTIFY_ERROR);
    exit;
}

$PAGE->set_pagelayout('admin');
$PAGE->set_url($CFG->wwwroot . "/blocks/enrollforms/addstudent.php");
$PAGE->navbar->add((get_string('addstudent','block_enrollforms')), new moodle_url('/blocks/enrollforms/addstudent.php'));
$PAGE->set_title(get_string("thankyouforcreatingaccount",'block_enrollforms'));
$PAGE->set_heading(get_string('addstudent','block_enrollforms'));
$PAGE->requires->jquery();
echo $OUTPUT->header();
//get_string("adduserpateheader", "block_enrollforms",array('tallotment'=>2,'noallotment'=>3))

$enroll = $DB->get_record('enroll_users',array('email'=>$USER->email));
$noofenroll = $DB->count_records('enroll_users',array('createdby'=>$USER->id));

$nextpage = $page;
$nextpage++;


$formdata = new addstudent_form('addstudent.php?page='.$nextpage,array('enroll'=>$enroll,'page'=>$page,'data'=>$_POST));

if($page==2 || $page==3){
    $formdata = new addstudent2_form('addstudent.php?page='.$nextpage,array('enroll'=>$enroll,'page'=>$page,'data'=>$_POST));
}
if($page==3){
// echo "<pre>";
// print_r($_POST);
// echo "</pre>";
// exit;
        if ($formdata->is_cancelled()) {
            $currenturl = "$CFG->wwwroot/blocks/enrollforms/addstudent.php";
            redirect($currenturl);
        }elseif($formdata->is_submitted()){
            $pagedata = unserialize($_POST['pagedata']);


            $groups = trim($pagedata["groups"]);

            $courses = $pagedata["courses"];

            $cohort_arr = [];

            $course_ids = "";
            $course_idspwd = "";

            foreach($courses as $key_courses=>$value){

            if($value == 1){
            $course =  $DB->get_record('course',array('id'=>$key_courses));
            $coursecontext = context_course::instance($course->id);

            $course_name = $course->fullname;

            

            $course_ids .= $course->idnumber . "\r\n";

            $course_idspwd .= $course->id;

            $iscohort =  $DB->get_record('cohort',array('name'=>trim($course_name),'contextid'=>$coursecontext->id));

           if(!$iscohort){
            $cohort = new stdClass();
            $cohort->name = trim($course_name);
            $cohort->contextid = $coursecontext->id;
               $iscohort->id =  cohort_add_cohort($cohort);
           }

           $cohort_arr[$course->id] = $iscohort->id;

           $data = new stdClass();
            $data->courseid = $key_courses;

            $data->name = $groups;
            if($groups)
            $isgroup =  $DB->get_record('groups',array('name'=>$groups,'courseid'=>$key_courses));
            if(!$isgroup){
                $isgroup->id  =  groups_create_group($data, $editform=false, $editoroptions=null);
            }

            $data_assigned = new stdClass();
            $data_assigned->name = 'Assigned';
            $data_assigned->courseid = $key_courses;
            $isgroup_assigned =  $DB->get_record('groups',array('name'=>'Assigned','courseid'=>$key_courses));
            if(!$isgroup_assigned){
                $isgroup_assigned->id  =  groups_create_group($data_assigned, $editform=false, $editoroptions=null);
            }

}
}


                if($formdata->get_file_content('file')) {
                global $DB;
                $text = $formdata->get_file_content('file');
                $text = preg_replace('!\r\n?!', "\n", $text);
                $rawlines = explode("\n", $text);
                require_once($CFG->libdir . '/csvlib.class.php');
                $importid = csv_import_reader::get_new_iid('groupimport');
                $csvimport = new csv_import_reader($importid, 'groupimport');
                $readcount = $csvimport->load_csv_content($text,'UTF-8','comma');
                $csvimport->init();
                unset($text);
                $count = 0;
                while ($line = $csvimport->next()) {
                    $count++;
                    $firstname = $line[0];
                    $lastnames = $line[1];
                    $insert = new stdClass();
                    $insert->firstname = $firstname;
                    $insert->lastname = $lastnames;
                    $insert->username = fictitious_username($firstname, $lastnames);
                    $insert->password = fictitious_passwordbylastname($lastnames,$course_idspwd);
                    $insert->email = fictitious_email($insert->username);
                    $insert->type = 'student';
                    $insert->total_allotment = $pagedata['noofuser'];
                    $insert->teacherenrolledcourse = trim($course_ids);
                    $insert->teachergroups = trim($pagedata['groups']);
                    $insert->status = 1;
                    $insert->createdby = $USER->id;
                    
                    if ($DB->insert_record('enroll_users', $insert, true)) {
                        $insert->auth = 'manual';
                        $insert->mnethostid = 1;
                        $insert->confirmed = 1;
                        user_create_user($insert);
                        $existinguser = $DB->get_record('user',array('email'=>$insert->email));

                        foreach($courses as $key_courses=>$value){
                                $course =  $DB->get_record('course',array('id'=>trim($key_courses)));
                                user_enroll_to_course($course->id,$existinguser->id,5,'manual');
                            cohort_add_member($cohort_arr[$key_courses],$existinguser->id);
                            $isgroup =  $DB->get_record('groups',array('name'=>$groups,'courseid'=>$key_courses));
                            groups_add_member($isgroup->id, $existinguser->id);
                        }
                    
                } 

                    if($pagedata['noofuser']==$count){
                        break;
                    }
                }
                $csvimport->close();

            } else {


                $firstnames = $_POST['firstname'];
                $lastnames = $_POST['lastname'];
                foreach ($firstnames as $key => $firstname) {

                    $username = fictitious_username($firstname, $lastnames[$key]);
                    if($username!='') {
                        $insert = new stdClass();
                        $insert->firstname = $firstname;
                        $insert->lastname = $lastnames[$key];
                        $insert->username =fictitious_username($firstname, $lastnames[$key]);
                        $insert->password = fictitious_passwordbylastname($lastnames[$key],$course_idspwd);
                        $insert->email = fictitious_email($insert->username);
                        $insert->type = 'student';
                        $insert->total_allotment = $pagedata['noofuser'];
                        $insert->teacherenrolledcourse = trim($course_ids);
                                                //$insert->teacherenrolledcourse = "01";
                        $insert->teachergroups = trim($pagedata['groups']);

                        $insert->status = 1;
                        $insert->createdby = $USER->id;



                        if ($DB->insert_record('enroll_users', $insert, true)) {

                            $insert->auth = 'manual';
                            $insert->mnethostid = 1;
                            $insert->confirmed = 1;
                            user_create_user($insert);
                            $existinguser = $DB->get_record('user',array('email'=>$insert->email));

                            foreach($courses as $key_courses=>$value){
                                if ($value!=0) {
                                    $course = $DB->get_record('course', array('id' => trim($key_courses)));

                                    //echo $key_courses . " " . $existinguser->id . "<br/>";

                                    user_enroll_to_course($key_courses, $existinguser->id, 5, 'manual');
                                    cohort_add_member($cohort_arr[$key_courses], $existinguser->id);
                                    $isgroup = $DB->get_record('groups', array('name' => $groups, 'courseid' => $key_courses));
                                    groups_add_member($isgroup->id, $existinguser->id);
                                }
                        }
                        }
                    }


                }
            }
        }
    $destination = 'studentinfo.php';
    redirect($destination, get_string('studentaddedsuccessfully','block_enrollforms'), null, \core\output\notification::NOTIFY_SUCCESS);
}


if($page==1) {
    echo '<div class="row" style="padding: 20px;"><h3 style="font-weight: 400;">';
    echo get_string("adduserpateheader", "block_enrollforms", array('tallotment' => $enroll->total_allotment, 'noallotment' => ($enroll->total_allotment -($noofenroll))));
    echo '</div></h3>';
}
echo $formdata->display();

echo $OUTPUT->footer();
?>
<script type="text/javascript">
    $(document).ready(function(){
        $('#id_submitbutton').click(function(event){

            var toenroll = <?=($enroll->total_allotment -($noofenroll))?>;
            var id_noofuser = $('#id_noofuser').val();

            if(id_noofuser > toenroll){
                if (toenroll == 0) {
                    var errmsg = 'Sorry, you have reached your total allotment of students.';
                } else {
                    var errmsg = 'Number of students should not be greater than '+toenroll + '.';
                }
                    event.preventDefault(); 
                    $('#id_error_noofuser').text(errmsg).show();    
            }
            // event.preventDefault();
            var  blank = true;
            $('.studentfields').each(function(index) {
                if($(this).val()==''){
                    blank = false;
                }
            });
            if(!blank){
                var r = confirm("No. of students added are less than students allotted to class. Are you sure?");
                if (r == true) {

                } else {
                    event.preventDefault();
                }
            }

        });
    });
</script>
