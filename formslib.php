<?php

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir.'/formslib.php');
class addteacher_form extends moodleform {

    /**
     * Defines the form.
     */
    public function definition() {
        global $DB;
        $mform = $this->_form;

        $mform->addElement('header', 'addteacher', get_string('addteacher', 'block_enrollforms'));

        $mform->addElement('text', 'email', get_string('email', 'block_enrollforms'), array('size' => '70'));
        $mform->setType('email', PARAM_RAW);

        $mform->addRule('email', null, 'required');
        $mform->addHelpButton('email', 'email', 'block_enrollforms');


        $mform->addElement('text', 'firstname', get_string('firstname', 'block_enrollforms'), array('size' => '70'));
        $mform->setType('firstname', PARAM_TEXT);
        $mform->addRule('firstname', null, 'required');
        $mform->addHelpButton('firstname', 'firstname', 'block_enrollforms');


        $mform->addElement('text', 'lastname', get_string('lastname', 'block_enrollforms'), array('size' => '70'));

        $mform->addRule('lastname', null, 'required');
        $mform->setType('lastname', PARAM_TEXT);
        $mform->addHelpButton('lastname', 'lastname', 'block_enrollforms');


        $mform->addElement('text', 'totalallottedstudents', get_string('totalallottedstudents', 'block_enrollforms'), array('size' => '10'));

        $mform->addRule('totalallottedstudents', null, 'required');
        $mform->addRule('totalallottedstudents', null, 'required','','client');
        $mform->addRule('totalallottedstudents', null, 'numeric', null, 'client');
        $mform->setType('totalallottedstudents', PARAM_INT);
        $mform->addHelpButton('totalallottedstudents', 'totalallottedstudents', 'block_enrollforms');

        $mform->addElement('textarea', 'teacherenrolledcourses', get_string('teacherenrolledcourses', 'block_enrollforms'),
            'wrap="virtual" rows="8" cols="70"');

        // NEW ADDITION
        $mform->addElement('textarea', 'teachergroups', get_string('groups'),
            'wrap="virtual" rows="8" cols="70"');

        $this->add_action_buttons();

    }

    /**
     * Validate the data from the form.
     *
     * @param  array $data form data
     * @param  array $files form files
     * @return array An array of error messages.
     */

}
class account_form extends moodleform {

    /**
     * Defines the form.
     */
    public function definition() {
        global $DB;

$user = $this->_customdata['user'];

        $mform = $this->_form;

        $mform->addElement('static', 'email', get_string('teachersemail', 'block_enrollforms'),$user->email);




        $mform->addElement('text', 'username', get_string('username', 'block_enrollforms'), array('size' => '70'));
        $mform->setType('username', PARAM_RAW);

        $mform->addRule('username', null, 'required');



        $mform->addElement('text', 'password', get_string('password', 'block_enrollforms'), array('size' => '70'));

        $mform->addRule('password', null, 'required');
        $mform->setType('password', PARAM_RAW);

        $mform->addElement('static', 'firstname', get_string('teachersfirstname', 'block_enrollforms'),$user->firstname);
        $mform->addElement('static', 'lastname', get_string('teacherslastname', 'block_enrollforms'),$user->lastname);



        $this->add_action_buttons();

    }

    /**
     * Validate the data from the form.
     *
     * @param  array $data form data
     * @param  array $files form files
     * @return array An array of error messages.
     */

}

class addstudent_form extends moodleform {

    /**
     * Defines the form.
     */
    public function definition() {
        global $DB,$CFG;

        $mform =& $this->_form;

        $enroll = $this->_customdata['enroll'];
        $page = $this->_customdata['page'];
        $data = $this->_customdata['data'];

        $tecourses = preg_split("/\r\n|\n|\r/", $enroll->teacherenrolledcourse);
//        $mform = $this->_form;
        $noofuser = 0;
        $checkboxarray=array();

        $cdetails = array();



        foreach ($tecourses as $key => $course) {

        if($DB->count_records('course',array('idnumber'=>trim($course)))){

           $cdetails =  $DB->get_record('course',array('idnumber'=>trim($course)));


           $coursename =$cdetails->fullname;
           if ($cdetails->id > 1) {
           $checkboxarray[] = $mform->createElement('advcheckbox', 'courses[' . $cdetails->id . ']', '', $coursename, $cdetails->id);
           }

//            $checkboxarray[] = $mform->createElement('advcheckbox', 'courses[]', '', $coursename, array('group' => 1), array(0, 1), array('script'=>'alert("ABC")'));

        }else{
            if (!empty($cdetails)) {
                //$checkboxarray[] = $mform->createElement('advcheckbox', 'courses[' . $cdetails->id . ']', '', $coursename, $cdetails->id, 'disabled');
            }
        }
//        $typeitem[] = &$mform->createElement('advcheckbox', $key, '', $course[], array('name' => $key, 'group' => 1), $key);

    }

    $tegroups = preg_split("/\r\n|\n|\r/", "Assigned\r\n" . $enroll->teachergroups);

    $groupsarray=array();

    foreach ($tegroups as $key => $groups) {
        if ($groups!="") {
            $groupsarray[] = $mform->createElement('radio', 'groups', '', $groups, $groups, '');
        }
 
    }


        $mform->addGroup($checkboxarray, 'selectcourse', get_string('selectcourse', 'block_enrollforms'), '<br>', false);

//        $mform->addElement('text', 'chkcourse', 'Number of Courses selected', array('size' => '10', 'disabled'));

        $mform->addGroup($groupsarray, 'selectgroup', get_string('group'), '<br>', false);

//    $mform->addGroup($typeitem, 'selectcourse', get_string('selectcourse', 'block_enrollforms'), '<br>');
     $mform->addRule("selectcourse", null, 'required','','client');

     $mform->addRule('selectgroup', null, 'required','','client');

     $mform->setDefault("groups","Assigned");

//        $this->add_checkbox_controller(1);


    $mform->addElement('text', 'noofuser', get_string('noofuser', 'block_enrollforms'), array('size' => '10'));
//    $mform->setType('id', PARAM_RAW);
    $mform->addRule('noofuser', null, 'required','','client');
    $mform->addRule('noofuser', null, 'numeric', null, 'client');
        $mform->setType('noofuser', PARAM_INT);

    $mform->addElement('text', 'description', get_string('description', 'block_enrollforms'), array('size' => '70'));
        $mform->setType('description', PARAM_TEXT);
    $this->add_action_buttons(true,get_string('continue', 'block_enrollforms'));

    }

    /**
     * Validate the data from the form.
     *
     * @param  array $data form data
     * @param  array $files form files
     * @return array An array of error messages.
     */

}
class addstudent2_form extends moodleform {

    /**
     * Defines the form.
     */
    public function definition() {
        global $DB,$CFG;

        $mform =& $this->_form;

        $enroll = $this->_customdata['enroll'];
        $page = $this->_customdata['page'];
        $data = $this->_customdata['data'];
        $mform->addElement('html', '<p style="text-align:right;"><a href="usercsv.csv">Download Sample CSV</a></p>');


//         echo "<pre>";
//         print_r($enroll);
//         echo "<br>PAGE<br>";
//         print_r($page);
//         echo "<br>DATA<br>";
//         print_r($data);
//         echo "</pre>";

        $coursenames = "";

        if (!empty($data["courses"])) {
            foreach ($data["courses"] as $single_key => $value) {
                //echo $single_key .  " " . $value;

                $arr_course = $DB->get_record('course', array('id' => $single_key));

                $course_name = $arr_course->fullname;

                //echo $course_name . "<br>";

                if ($value == 1) {
                    $coursenames .= $course_name . "<br>";
                }
            }
        }

//exit;

        if ($coursenames!="") {
            $mform->addElement('html', '<h3><b>' . $coursenames . '</b></h3>');
        }
        if (isset($data["groups"]) && $data["groups"]!="") {
            $mform->addElement('html', '<p>' . $data["groups"] . '</p>');
        }
        if (isset($data["description"]) && $data["description"]!="") {
            $mform->addElement('html', '<p><b>' . $data["description"] . '</b></p>');
        }

    $noofuser = isset($data['noofuser'])?$data['noofuser']:0;
    $html = '<table style="text-align: center;">
                <tr>
                <th>'.get_string('stufirstname','block_enrollforms').'</th>
                <th>'.get_string('stulastname','block_enrollforms').'</th>
                </tr>';
    for($i=1;$i <= $noofuser; $i++){


    $html .= '<tr>
                <td style=" padding: 10px;"><input type="text" class="form-control studentfields" name="firstname['.$i.']"></td>
                <td style=" padding: 10px;"><input type="text" class="form-control studentfields" name="lastname['.$i.']"></td>

                </tr>';
    }
    $html .= ' </table>';

    $mform->addElement('hidden', 'pagedata', serialize($data));
        $mform->setType('pagedata', PARAM_RAW);

    $mform->addElement('html', '<div class="col-md-11" style="text-align: -webkit-center;">'.$html);

    $mform->addElement('html', '<h2>OR</h2>');
    $mform->addElement('filepicker', 'file', get_string('csvupload','block_enrollforms'), null,
        array('maxbytes' => 1000, 'accepted_types' => 'csv'));
        $this->add_action_buttons(false,get_string('continue', 'block_enrollforms'));

    }

    /**
     * Validate the data from the form.
     *
     * @param  array $data form data
     * @param  array $files form files
     * @return array An array of error messages.
     */

}
?>
