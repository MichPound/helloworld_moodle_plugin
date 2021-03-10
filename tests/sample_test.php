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

class local_helloworld_sample_testcase extends advanced_testcase{
    public function test_creation_deletion() {
        global $DB;

        $sql = "SELECT m.id, m.message, m.timecreated
        FROM {local_helloworld_messages} m
        ORDER BY timecreated DESC";

        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();

        $record = new stdClass();
        $record->message = "Hello there";
        $record->timecreated = time();
        $record->userid = $user->id;

        $DB->insert_record('local_helloworld_messages', $record);
        $this->assertTrue(count($DB->get_records_sql($sql)) == 1);

        $DB->delete_records('local_helloworld_messages', array('userid' => $user->id));
        $this->assertEmpty($DB->get_records_sql($sql));
    }

    // Test guest users.
    // Test non-admin delet.
}
