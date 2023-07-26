<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Library of interface functions and constants.
 *
 * @package     mod_eflok
 * @copyright   2023 Data Magnum <officialdatamagnum@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Return if the plugin supports $feature.
 *
 * @param string $feature Constant representing the feature.
 * @return true | null True if the feature is supported, null otherwise.
 */

//define('MOD_EFLOK_FRONTEND_BASE_URL', 'http://localhost:3000');
//define('MOD_EFLOK_GET_BOARD_LIST_URL', 'https://b59d-110-227-212-183.ngrok-free.app/v2/meeting/getMoodleBoardList');
//define('MOD_EFLOK_CREATE_BOARD_URL',   'https://b59d-110-227-212-183.ngrok-free.app/v2/meeting/createMoodleMeeting');

define('MOD_EFLOK_FRONTEND_BASE_URL', 'https://eflok.com');
define('MOD_EFLOK_GET_BOARD_LIST_URL','https://prod-api.eflok.com/v2/meeting/getMoodleBoardList');
define('MOD_EFLOK_CREATE_BOARD_URL',  'https://prod-api.eflok.com/v2/meeting/createMoodleMeeting');

function eflok_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        default:
            return null;
    }
}

/**
 * Saves a new instance of the mod_eflok into the database.
 *
 * Given an object containing all the necessary data, (defined by the form
 * in mod_form.php) this function will create a new instance and return the id
 * number of the instance.
 *
 * @param object $moduleinstance An object from the form.
 * @param mod_eflok_mod_form $mform The form.
 * @return int The id of the newly inserted record.
 */
function eflok_add_instance($moduleinstance, $mform = null) {
    global $DB;

    $moduleinstance->timecreated = time();

    $id = $DB->insert_record('eflok', $moduleinstance);

    return $id;
}

/**
 * Updates an instance of the mod_eflok in the database.
 *
 * Given an object containing all the necessary data (defined in mod_form.php),
 * this function will update an existing instance with new data.
 *
 * @param object $moduleinstance An object from the form in mod_form.php.
 * @param mod_eflok_mod_form $mform The form.
 * @return bool True if successful, false otherwise.
 */
function eflok_update_instance($moduleinstance, $mform = null) {
    global $DB;

    $moduleinstance->timemodified = time();
    $moduleinstance->id = $moduleinstance->instance;

    return $DB->update_record('eflok', $moduleinstance);
}

/**
 * Removes an instance of the mod_eflok from the database.
 *
 * @param int $id Id of the module instance.
 * @return bool True if successful, false on failure.
 */
function eflok_delete_instance($id) {
    global $DB;

    $exists = $DB->get_record('eflok', array('id' => $id));
    if (!$exists) {
        return false;
    }

    $DB->delete_records('eflok', array('id' => $id));

    return true;
}
