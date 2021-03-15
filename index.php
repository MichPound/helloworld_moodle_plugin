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
 * Main page of the plugin.
 *
 * @package     local_helloworld
 * @copyright   2020 Your Name
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__. '/../../config.php');

$PAGE->set_url(new moodle_url('/local/helloworld/index.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('pluginname', 'local_helloworld'));
$PAGE->set_heading(get_string('pluginname', 'local_helloworld'));

require_login();
if (isguestuser()) {
    print_error('noguest');
}

global $DB;
$message = optional_param('message', null, PARAM_TEXT);
$id = optional_param('deleteid', null, PARAM_INT);

$userfields = get_all_user_name_fields(true, 'u');

$sql = "SELECT m.id, m.message, m.timecreated, u.id AS userid, $userfields
        FROM {local_helloworld_messages} m
        LEFT JOIN {user} u ON u.id = m.userid
        ORDER BY timecreated DESC";

$messages = $DB->get_records_sql($sql);

// Inserting a new message into database.
if ($message) {
    require_capability('local/helloworld:postmessage', context_system::instance());
    $record = new stdClass();
    $record->message = $message;
    $record->timecreated = time();
    $record->userid = $USER->id;

    $DB->insert_record('local_helloworld_messages', $record);

    $messages = $DB->get_records_sql($sql);
}

// Deleting message from database.
if ($id && has_capability('local/helloworld:deleteanymessage', context_system::instance())) {
    require_sesskey();

    $DB->delete_records('local_helloworld_messages', array('id' => $id));

    $messages = $DB->get_records_sql($sql);
}

echo $OUTPUT->header();

// Form for creating new messages.
if (has_capability('local/helloworld:postmessage', context_system::instance())) {

    $ctx = new \stdClass();

    echo $OUTPUT->render_from_template('local_helloworld/helloworld_form', $ctx);
}

// Cards for displaying messages.
if (has_capability('local/helloworld:viewmessage', context_system::instance())) {

    $data = new \stdClass();

    foreach ($messages as $message) {
        $message->timecreated = userdate($message->timecreated);
        $message->fullname = fullname($message);
        $data->messages[] = $message;
    }

    $data->sesskey_name = 'sesskey';
    $data->sesskey_value = sesskey();
    $data->delete_id = 'deleteid';

    if (has_capability('local/helloworld:deleteanymessage', context_system::instance())) {
        $data->delete = true;
    } else {
        $data->delete = false;
    }

    echo $OUTPUT->render_from_template('local_helloworld/helloworld_cards', $data);
}

echo $OUTPUT->footer();