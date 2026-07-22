<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade script.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_local_cert_fanafesa_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2026061808) {

        // Define table.
        $table = new xmldb_table('local_cert_fanafesa_download');

        // Fields.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null,
            XMLDB_NOTNULL, XMLDB_SEQUENCE, null);

        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10',
            null, XMLDB_NOTNULL);

        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10',
            null, XMLDB_NOTNULL);

        $table->add_field('firstdownload', XMLDB_TYPE_INTEGER, '10',
            null, XMLDB_NOTNULL);

        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10',
            null, XMLDB_NOTNULL);

        // Keys.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Unique index.
        $table->add_index(
            'userid_courseid_uix',
            XMLDB_INDEX_UNIQUE,
            ['userid', 'courseid']
        );

        // Create table.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(
            true,
            2026072100,
            'local',
            'cert_fanafesa'
        );
    }

    return true;
}