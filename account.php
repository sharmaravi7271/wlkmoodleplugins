<?php

require_once(dirname(dirname(__FILE__)) . '../../config.php');

defined('MOODLE_INTERNAL') || die();
global $PAGE, $CFG, $DB, $OUTPUT;
$PAGE->set_context(context_system::instance());

require_once($CFG->dirroot.'/blocks/enrollforms/formslib.php');
require_once($CFG->dirroot.'/blocks/enrollforms/locallib.php');
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string("chooseusernamepassword", "block_enrollforms"));
$PAGE->set_heading(get_string("chooseusernamepassword", "block_enrollforms"));
$PAGE->set_url($CFG->wwwroot . "/blocks/enrollforms/account.php");
$PAGE->requires->jquery();
$user = required_param('user',PARAM_TEXT);
$sql = "select * from {enroll_users} where md5(id) ='$user'";
require_once($CFG->libdir.'/authlib.php');
require_once($CFG->dirroot.'/user/lib.php');
require_once($CFG->dirroot.'/group/lib.php');

$userdata = $DB->get_record_sql($sql); 

$existinguser = $DB->get_record('user',array('email'=>$userdata->email));
if($existinguser){
    if(complete_user_login($existinguser)){
        redirect('loginsuccess.php');
    }
}

$existurl = $CFG->wwwroot . "/blocks/enrollforms/account.php?user=" . $user . "&exists=1";

$account = new account_form('account.php?user='.$user,array('user'=>$userdata));
if ($account->is_cancelled()) {
    $currenturl = "$CFG->wwwroot/blocks/enrollforms/view.php";
    redirect($currenturl);
}elseif($account->is_submitted()) {

    $username = strtolower(trim($_POST['username']));
    $sqlusers = "select * from {user} where username ='$username'";
    $dbusers = $DB->get_record_sql($sqlusers);

    if ($dbusers) {
        redirect($existurl, 'The username is already in use. Please select another username.', error, 0);
        exit;
    }


    $sql = "select * from {enroll_users} where md5(id) ='$user'";
    $userdata = $DB->get_record_sql($sql);

    $teachergroups = $userdata->teachergroups;
    $tegroups = preg_split("/\r\n|\n|\r/", $userdata->teachergroups);

    $insert = new stdClass();
    $insert->firstname = $userdata->firstname;
    $insert->lastname = $userdata->lastname;
    $insert->email = $userdata->email;
    $insert->username = strtolower(trim($_POST['username']));
    $insert->password = trim($_POST['password']);
    $insert->auth = 'manual';
    $insert->mnethostid = 1;
    $insert->confirmed = 1;
    user_create_user($insert);
    $existinguser = $DB->get_record('user',array('email'=>$insert->email));

    if(complete_user_login($existinguser)){
        $role = $DB->get_record('role',array('shortname'=>'editingteacher'));
        $tecourses = preg_split("/\r\n|\n|\r/", $userdata->teacherenrolledcourse);
            foreach($tecourses as $idnumber){
                if($idnumber){
               $course =  $DB->get_record('course',array('idnumber'=>$idnumber));
                user_enroll_to_course($course->id,$existinguser->id,$role->id,'manual');

                foreach ($tegroups as $tgroup) {
                    $data = new stdClass();
                    $data->courseid = $course->id;
                    $data->name = $tgroup;
                    $isgroup =  $DB->get_record('groups',array('name'=>$tgroup,'courseid'=>$course->id));
                    if(!$isgroup){
                        $isgroup->id  =  groups_create_group($data, $editform=false, $editoroptions=null);
                    }

                    $usersgroups = groups_get_user_groups($course->id, $existinguser->id);

                    $arr_usersgroups = $usersgroups[0];

                    if (!in_array($isgroup->id, $arr_usersgroups)) {
                        groups_add_member($isgroup->id, $existinguser->id);
                    }
                    }


                }
            }
        redirect('loginsuccess.php');
    }
}
echo $OUTPUT->header();
echo $account->display();
echo $OUTPUT->footer();
