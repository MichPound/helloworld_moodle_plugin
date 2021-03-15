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
 * Adds link to navigate to plugin.
 *
 * @package     local_helloworld
 * @copyright   2020 Your Name
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Checks admin settings and adds link to plugin in navigation.
 *
 * @param navigation_node $parentnode
 * @return bool
 */

function local_helloworld_extend_navigation_frontpage(navigation_node $parentnode) {
    if (get_config('local_helloworld', 'showinnavigation') == 1 && isloggedin() && !isguestuser()) {
        $parentnode->add(
            get_string('pluginname', 'local_helloworld'),
            new moodle_url('/local/helloworld/index.php'),
            navigation_node::TYPE_CUSTOM,
            null,
            null,
            new pix_icon('i/filter', ''));
    }
}
