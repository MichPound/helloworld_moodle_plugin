<?php

require(__DIR__. '/../../config.php');

$PAGE->set_url(new moodle_url('/local/helloworld/index.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('pluginname', 'local_helloworld'));
$PAGE->set_heading(get_string('pluginname', 'local_helloworld'));

require_login();
if (isguestuser()) print_error('noguest');

global $DB;
$message = optional_param('message', NULL, PARAM_TEXT);
$userfields = get_all_user_name_fields(true, 'u');
$sql = "SELECT m.id, m.message, m.timecreated, u.id AS userid, $userfields
        FROM {local_helloworld_messages} m
        LEFT JOIN {user} u ON u.id = m.userid
        ORDER BY timecreated DESC";
$messages = $DB->get_records_sql($sql);
// $messages = $DB->get_records('local_helloworld_messages'); 

if($message && has_capability('local/helloworld:postmessage', context_system::instance())){
    $record = new stdClass();
    $record->message = $message;
    $record->timecreated = time();
    $record->userid = $USER->id;

    $DB->insert_record('local_helloworld_messages', $record);

    $messages = $DB->get_records_sql($sql);
    // $messages = $DB->get_records('local_helloworld_messages');
}

echo $OUTPUT->header();

if(has_capability('local/helloworld:postmessage', context_system::instance())){echo 'u can post ';}
if(has_capability('local/helloworld:viewmessage', context_system::instance())){echo 'u can view ';}
if(has_capability('local/helloworld:deleteanymessage', context_system::instance())){echo 'u can delete';}

//Form for creating new messages
if(has_capability('local/helloworld:postmessage', context_system::instance())){

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

//Cards for message display
if(has_capability('local/helloworld:viewmessage', context_system::instance())){

    echo html_writer::start_div('card-columns');

    foreach ($messages as $message) {
        echo html_writer::start_div('card');

        echo html_writer::start_div('card-body');

        echo html_writer::tag('p', $message->message, [
            'class' => 'card-text',
        ]);

        echo html_writer::tag('footer', $message->userid, [
            'class' => 'blockquote-footer',
        ]);

        echo html_writer::tag('footer', userdate($message->timecreated), [
            'class' => 'blockquote-footer',
        ]);

        echo html_writer::end_div('card-body');

        echo html_writer::end_div('card');
    }

    echo html_writer::end_div('card-columns');
}

echo $OUTPUT->footer();