<?php
require_once(dirname(dirname(__FILE__)).'../../config.php');
global $CFG,$USER, $DB;

require "$CFG->libdir/tablelib.php";


class studentinfo_table extends table_sql
{
    function __construct($uniqueid)
    {
        parent::__construct($uniqueid);
        // Define the list of columns to show.

        $columns = array('firstname','lastname', 'username','password');
        $this->define_columns($columns);

        // Define the titles of columns to show in header.

        $headers = array(
            get_string('stufirstname', 'block_enrollforms'),
            get_string('stulastname','block_enrollforms'),
            get_string('username','block_enrollforms'),
            get_string('password', 'block_enrollforms'),

        );
        $this->define_headers($headers);
    }
}