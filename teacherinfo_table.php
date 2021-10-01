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

        $columns = array('firstname','lastname','email','username','password');
        $this->define_columns($columns);

        // Define the titles of columns to show in header.

        $headers = array(
            get_string('stufirstname', 'block_enrollforms'),
            get_string('stulastname','block_enrollforms'),
            get_string('email','block_enrollforms'),
            get_string('username','block_enrollforms'),
            get_string('password', 'block_enrollforms'),

        );
        $this->define_headers($headers);
    }

    function col_username($value){
        if($value->user_id){
          return  $value->username;
        }else{
         return '<input type="text" name="username" id="enrollid_$value->id" class="enrollid">';
        }


    }

}