<?php
require_once(dirname(dirname(__FILE__)).'../../config.php');
global $CFG,$USER, $DB;

require "$CFG->libdir/tablelib.php";


class teacherinfo_table extends table_sql
{
    function __construct($uniqueid)
    {
        parent::__construct($uniqueid);
        // Define the list of columns to show.

        $columns = array('firstname', 'lastname', 'email', 'username', 'password', 'action');
        $this->define_columns($columns);

        // Define the titles of columns to show in header.

        $headers = array(
            get_string('firstname', 'block_enrollforms'),
            get_string('lastname', 'block_enrollforms'),
            get_string('email', 'block_enrollforms'),
            get_string('username', 'block_enrollforms'),
            get_string('password', 'block_enrollforms'),
            get_string('action', 'block_enrollforms'),
        );
        $this->define_headers($headers);
    }

    function col_username($value)
    {
        if ($value->user_id) {
            return $value->username;
        } else {
             $html ='<input type="text" name="username" id="username_' . $value->id . '" class="form-control enrollid" placeholder="Username">';
            return $html .='<span class="alert-danger" style="display:none;" id="usererror_'.$value->id.'">'.get_string('usernamerequire','block_enrollforms').'</span>';
        }
    }

    function col_password($value)
    {
        if ($value->user_id) {
            return $value->password;
        } else {
            $html =  '<input type="text" name="password" id="password_' . $value->id . '" class="form-control enrollid" placeholder="Password">';
            return $html .='<span class="alert-danger" id="passerror_'.$value->id.'" style="display:none;">'.get_string('passwordrequire','block_enrollforms').'</span>';
        }


    }

    function col_action($value)
    {
        if ($value->user_id) {

        } else {
            return '<input type="button" name="enroll_update" id="enroll_update_' . $value->id . '" class="btn btn-success enroll_update" value="Submits">';
        }


    }


}