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
 * @package    block_learningplans
 * @copyright  2021 Learning Plan
 * @author
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(__FILE__)) . '../../config.php');

defined('MOODLE_INTERNAL') || die();
require_login();
require_once('locallib.php');
require_once($CFG->dirroot.'/user/lib.php');
global $DB,$USER;
$request = required_param('request', PARAM_TEXT);


if($request =='updateteacher'){
    $enrollid = required_param('id',PARAM_INT);
    $userdata =  $DB->get_record('enroll_users',array('id'=>$enrollid));
    $username= required_param('username',PARAM_TEXT);
    $password= required_param('password',PARAM_TEXT);

    $insert = new stdClass();
    $insert->firstname = $userdata->firstname;
    $insert->lastname = $userdata->lastname;
    $insert->email = $userdata->email;
    $insert->username = strtolower(trim($username));
    $insert->password = trim($password);
    $insert->auth = 'manual';
    $insert->mnethostid = 1;
    $insert->confirmed = 1;
    user_create_user($insert);
    $existinguser = $DB->get_record('user',array('email'=>$insert->email));
    $userdata->username = $insert->username;
    $userdata->password = trim($_POST['password']);
    $userdata->user_id = $existinguser->id;
    $DB->update_record('enroll_users',$userdata);
    echo 1 ;
    die();
}

