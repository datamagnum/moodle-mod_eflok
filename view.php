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
 * Prints an instance of mod_eflok.
 *
 * @package     mod_eflok
 * @copyright   2023 Data Magnum <officialdatamagnum@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course module id.
$id = optional_param('id', 0, PARAM_INT);


// Activity instance id.
$e = optional_param('e', 0, PARAM_INT);
$u        = optional_param('eventName', 0, PARAM_TEXT);         // URL instance id
$redirect = optional_param('redirect', 0, PARAM_TEXT);
$forceview = optional_param('forceview', 0, PARAM_TEXT);
//echo "<pre>";print_r($e);exit();

if ($id) {
    $cm = get_coursemodule_from_id('eflok', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $moduleinstance = $DB->get_record('eflok', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
    $moduleinstance = $DB->get_record('eflok', array('id' => $e), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $moduleinstance->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('eflok', $moduleinstance->id, $course->id, false, MUST_EXIST);
}

require_login($course, true, $cm);

$PAGE->set_url('/mod/eflok/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);

echo $OUTPUT->header();

echo $OUTPUT->footer();
