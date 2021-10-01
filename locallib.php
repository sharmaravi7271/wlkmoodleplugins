<?php
defined('MOODLE_INTERNAL') || die();
global $PAGE, $CFG, $DB, $OUTPUT,$USER;
require_once("{$CFG->libdir}/completionlib.php");
require_once($CFG->dirroot.'/course/lib.php');
//$PAGE->set_context(context_system::instance());




function generate_email_user($email, $name = '', $id = -99) {
    $emailuser = new stdClass();
    $emailuser->email = trim(filter_var($email, FILTER_SANITIZE_EMAIL));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailuser->email = '';
    }
    $name = format_text($name, FORMAT_HTML, array('trusted' => false, 'noclean' => false));
    $emailuser->firstname = trim(filter_var($name, FILTER_SANITIZE_STRING));
    $emailuser->lastname = '';
    $emailuser->maildisplay = true;
    $emailuser->mailformat = 1; // 0 (zero) text-only emails, 1 (one) for HTML emails.
    $emailuser->id = $id;
    $emailuser->firstnamephonetic = '';
    $emailuser->lastnamephonetic = '';
    $emailuser->middlename = '';
    $emailuser->alternatename = '';
    return $emailuser;
}


function remove_http($url) {
    $disallowed = array('http://', 'https://');
    foreach($disallowed as $d) {
        if(strpos($url, $d) === 0) {
            return str_replace($d, '', $url);
        }
    }
    return $url;
}


function user_enroll_to_course($courseid, $userid, $roleid, $enrolmethod = 'manual') {
    global $DB,$USER;
    global $DB;


    $user = $DB->get_record('user', array('id' => $userid, 'deleted' => 0), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

    //$context = context_course::instance($course->id);

    $context = context_course::instance($courseid);

    if (!is_enrolled($context, $user)) {
        $enrol = enrol_get_plugin($enrolmethod);
        if ($enrol === null) {
//                return false;
        }
        $instances = enrol_get_instances($courseid, true);
        $manualinstance = null;



        foreach ($instances as $instance) {


            if ($instance->enrol == $enrolmethod) {
                $manualinstance = $instance;
                    break;
            }
        }

        if ($manualinstance !== null) {

            //$instanceid = $enrol->add_default_instance($course);

            $instanceid = $manualinstance->id;

            if ($instanceid === null) {
                $instanceid = $enrol->add_instance($course);
            }

            $instance = $DB->get_record('enrol', array('id' => $instanceid));

        }


        $startdate = time();
        $enddate =0;

        $enrol->enrol_user($instance, $userid, $roleid,$startdate,$enddate);

    }
        return true;
}


function sendmailtoteacher($to_user){
    global $DB,$USER,$CFG;

   $teacher =  generate_email_user($to_user->email,$to_user->firstname);


    $noreplyaddressdefault = 'noreply@' . get_host_from_url($CFG->wwwroot);
    $noreplyaddress = empty($CFG->noreplyaddress) ? $noreplyaddressdefault : $CFG->noreplyaddress;

    if (!validate_email($noreplyaddress)) {
        debugging('email_to_user: Invalid noreply-email '.s($noreplyaddress));
        $noreplyaddress = $noreplyaddressdefault;
    }
    $link= 'Please <a href="'.$CFG->wwwroot.'/blocks/enrollforms/account.php?user='.md5($to_user->id).'"> Click here </a> to activate your account';
        $messagehtml =  get_string('teachermailtemplate','block_enrollforms',array('firstname'=>$teacher->firstname,'link'=>$link));


    $messagetext = html_to_text($messagehtml);
    $subject = get_string('teacheraccountsubject','block_enrollforms');
    $formmail =\core_user::get_noreply_user();
    $success = email_to_user($teacher, $formmail, $subject, $messagetext, $messagehtml, '', '', true, $formmail->email);
}

function fictitious_username($firstname,$lastname,$attempt =0){
global $DB;
    $intlastname = substr($lastname,0,1);
if(!$attempt){
    $username = strtolower(preg_replace("/\s+/", "", $firstname.$intlastname));
}else{
    $username = strtolower(preg_replace("/\s+/", "", $firstname.$intlastname).$attempt);
}
$username = str_replace("'", '',$username);
if($DB->count_records('user',array('username'=>$username))){
        $attempt++;
    return strtolower(fictitious_username($firstname,$lastname,$attempt));
}else{
    return strtolower( str_replace("'", '', $username));
}
}
function fictitious_password($length){
    $chars = "abcdefghijklmnopqrstuvwxyz";
  $uppercase =   substr( str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'),0,1);
  $spacial =   substr(str_shuffle('!?~@#-_+<>[]{}'),0,1);
  $digit =   substr(str_shuffle('1234567890'),0,1);
    $char =  substr(str_shuffle($chars),0,6);
    return str_shuffle($char.$spacial.$uppercase.$digit);

}

function fictitious_passwordbylastname($lastname,$courseid){
    $cid =   substr($courseid,-3,3);
    return   strtolower($lastname.$cid);


}

function fictitious_email($username,$attempt=0){
    global $DB,$CFG;
    if(!$attempt){
        $email =  $username.'@'.remove_http($CFG->wwwroot);
    }else{
        $email =  $username.$attempt.'@'.remove_http($CFG->wwwroot);

    }
    if($DB->count_records('user',array('email'=>$email))){
        $attempt++;
      return  fictitious_email($username,$attempt);
    }else{
        return $email;
    }
}
?>
