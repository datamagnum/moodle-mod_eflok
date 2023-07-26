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
 * @package    mod_eflok
 * @subpackage backup-moodle2
 * @copyright  2023 Data Magnum <officialdatamagnum@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the restore steps that will be used by the restore_eflok_activity_task
 */

/**
 * Structure step to restore one eflok activity
 */
class restore_eflok_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();
        //$userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('eflok', '/activity/eflok');
        $paths[] = new restore_path_element('eflok_configuration', '/activity/eflok/configuration');
        

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_eflok($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        // Any changes to the list of dates that needs to be rolled should be same during course restore and course reset.
        // See MDL-9367.

        // insert the eflok record
        $newitemid = $DB->insert_record('eflok', $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }

    protected function process_eflok_configuration($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->eflok = $this->get_new_parentid('eflok');
        //$data->userid = $this->get_mappingid('user', $data->userid);

        $newitemid = $DB->insert_record('eflok_configuration', $data);

        $this->set_mapping('eflok_configuration', $data->id, $newitemid);
        // No need to save this mapping as far as nothing depend on it
        // (child paths, file areas nor links decoder)
    }
    protected function after_execute() {
        // Add eflok related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_eflok', 'intro', null);
    }
}
