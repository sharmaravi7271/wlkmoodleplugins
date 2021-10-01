<?php
function xmldb_block_enrollforms_upgrade($oldversion)
{
    global $DB;

    $dbman = $DB->get_manager();
    if ($oldversion < 2020110907) {

        // Define field teachergroups to be added to enroll_users.
        $table = new xmldb_table('enroll_users');
        $field = new xmldb_field('teachergroups', XMLDB_TYPE_TEXT, null, null, null, null, null, 'status');

        // Conditionally launch add field teachergroups.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Enrollforms savepoint reached.
        upgrade_block_savepoint(true, 2020110907, 'enrollforms');
    }
}
