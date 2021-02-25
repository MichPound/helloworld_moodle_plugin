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

if ($message) {
    require_capability('local/helloworld:postmessage', context_system::instance());
    $record = new stdClass();
    $record->message = $message;
    $record->timecreated = time();
    $record->userid = $USER->id;

    $DB->insert_record('local_helloworld_messages', $record);

    $messages = $DB->get_records_sql($sql);
}

if ($id && has_capability('local/helloworld:deleteanymessage', context_system::instance())) {
    require_sesskey();

    $DB->delete_records('local_helloworld_messages', array('id' => $id));

    $messages = $DB->get_records_sql($sql);
}

echo $OUTPUT->header();

// Form for creating new messages.
if (has_capability('local/helloworld:postmessage', context_system::instance())) {

    echo html_writer::start_tag('form', [
        'method' => 'post',
    ]);

    echo html_writer::tag('textarea', '', [
        'placeholder' => get_string('entermessage', 'local_helloworld'),
        'name' => 'message',
        'class' => 'form-control',
    ]);

    echo html_writer::tag('br', '');

    echo html_writer::tag('input', '', [
        'type' => 'submit',
        'value' => get_string('submit'),
    ]);

    echo html_writer::end_tag('form');
}

// Cards for message display.
if (has_capability('local/helloworld:viewmessage', context_system::instance())) {

    echo html_writer::start_div('card-columns');

    foreach ($messages as $message) {
        echo html_writer::start_div('card');

        echo html_writer::start_div('card-body');

        echo html_writer::tag('p', $message->message, [
            'class' => 'card-text',
        ]);

        echo html_writer::tag('footer', fullname($message), [
            'class' => 'blockquote-footer',
        ]);

        echo html_writer::tag('footer', userdate($message->timecreated), [
            'class' => 'blockquote-footer',
        ]);

        echo "<br>";

        if (has_capability('local/helloworld:deleteanymessage', context_system::instance())) {
            // Insert delete button.
            echo html_writer::start_tag('form', [
                'method' => 'post',
            ]);

            echo html_writer::empty_tag('input', [
                'type' => 'hidden',
                'name' => 'sesskey',
                'value' => sesskey(),
            ]);

            echo html_writer::start_tag('button', [
                'type' => 'submit',
                'name' => 'deleteid',
                'class' => 'btn btn-danger rounded',
                'value' => $message->id,
            ]);

            echo html_writer::tag('i', get_string('delete'));

            echo html_writer::end_tag('button');

            echo html_writer::end_tag('form');
        }

        echo html_writer::end_div('card-body');

        echo html_writer::end_div('card');
    }

    echo html_writer::end_div('card-columns');
}

echo $OUTPUT->footer();