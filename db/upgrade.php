<?php
 
function xmldb_local_helloworld_upgrade($oldversion) {
    global $CFG, $DB;
 
    $result = TRUE;

    $dbman = $DB->get_manager();
 
    if ($oldversion < 2021021900) {

        // Define field userid to be added to local_helloworld_messages.
        $table = new xmldb_table('local_helloworld_messages');
        $field = new xmldb_field('userid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'timecreated');

        // Conditionally launch add field userid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
    
        // Define key userid (foreign-unique) to be added to local_helloworld_messages.
        $table = new xmldb_table('local_helloworld_messages');
        $key = new xmldb_key('userid', XMLDB_KEY_FOREIGN_UNIQUE, ['userid'], 'user', ['id']);

        // Launch add key userid.
        $dbman->add_key($table, $key);

        // Helloworld savepoint reached.
        upgrade_plugin_savepoint(true, 2021021900, 'local', 'helloworld');
    }
 
    return $result;
}