<?php
$PAGE->requires->js('/mod/eflok/js/jquery.min.js');

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
 * The main mod_eflok configuration form.
 *
 * @package     mod_eflok
 * @copyright   2023 Data Magnum <officialdatamagnum@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use search_solr\schema;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');
require_once("lib.php");

/**
 * Module instance settings form.
 *
 * @package     mod_eflok
 * @copyright   2023 Data Magnum <officialdatamagnum@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_eflok_mod_form extends moodleform_mod
{

    /**
     * Defines forms elements
     */
    public function definition()
    {
        global $CFG, $PAGE, $USER, $OUTPUT, $DB;
        $mform = $this->_form;




        $options = $DB->get_records('eflok_configuration');
        $configuration = json_decode(json_encode($options), true);
        if (count($configuration)) {
            $orgId = $configuration[1]['organisation_id'];
        } else {
            exit(get_string('basic_configuration_error_message', 'mod_eflok'));
        }

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('eflokname', 'mod_eflok'), array('size' => '64'));


        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }

        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'eflokname', 'mod_eflok');


        if ($CFG->branch >= 29) {
            $this->standard_intro_elements();
        } else {
            $this->standard_intro_elements();
        }

        $searchareas = \core_search\manager::get_search_areas_list(true);

        //FETCH USER BOARD LIST START
        $curl = new curl();
        $moodleBoardListJson = $curl->get(MOD_EFLOK_GET_BOARD_LIST_URL, array());
        $moodleBoardList = json_decode($moodleBoardListJson, true);
        
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, MOD_EFLOK_GET_BOARD_LIST_URL);

        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        // $moodleBoardListJson = curl_exec($ch);
        // $moodleBoardList = json_decode($moodleBoardListJson, true);
        // echo "<pre>";print_r($moodleBoardList);exit();

        $tempBoardArr = [];
        if (isset($moodleBoardList) && isset($moodleBoardList['data']) && count($moodleBoardList['data']) != 0) {
            foreach ($moodleBoardList['data'] as $key => $value) {
                $tempBoardArr[$value['meetingId']] = $value['eventName'] ? $value['eventName'] : $value['meetingId'];
            }
        }

        $encryptionattr = ['selected' => 1]; //'disabled' => false,

        $mform->addElement('header', 'eflokcustomeblock', get_string('eflokcustomeblock', 'mod_eflok'));

        $mform->addGroup([
            $mform->createElement(
                'radio',
                'board_type',
                '',
                get_string('board_type_new_board', 'mod_eflok'),
                "1",
                $encryptionattr
            ),
            $mform->createElement(
                'radio',
                'board_type',
                '',
                get_string('board_type_existing_board', 'mod_eflok'),
                "2",
                $encryptionattr
            )
        ], 'board_type_group', get_string('board_type_radio', 'mod_eflok'), null, false);
        $mform->setDefault('board_type', 1);

        $mform->addElement('html', '<div class="new_board_block">');

        $mform->addElement('text', 'eventName', get_string('board_name', 'mod_eflok'), array('size' => '64'));

        $mform->addElement('textarea', 'eventDescription', get_string("board_description", "mod_eflok"), 'wrap="virtual" rows="10" cols="50"');

        $mform->addElement('select', 'isProctoring', get_string('is_proctoring', 'mod_eflok'), array("false" => get_string('false', 'mod_eflok'), "true" => get_string('true', 'mod_eflok')));

        $mform->addElement('select', 'external_board_access', get_string('board_access', 'mod_eflok'), array("can-edit" => get_string('can-edit', 'mod_eflok'), "can-view" => get_string('can-view', 'mod_eflok')));

        $mform->addElement('hidden', 'initatorUsername', $USER->email);

        $mform->addElement('hidden', 'organisation_id', $orgId); //'6452447d76a3531279fbe583'

        $mform->addElement('html', '</div>');


        $mform->addElement('html', '<div class="existing_board_block hide">');

        $mform->addElement('autocomplete', 'existing_board_id', get_string('select_board', 'mod_eflok'), $tempBoardArr, array('multiple' => false, 'noselectionstring' => get_string('select_board', 'mod_eflok')));

        $mform->addElement('html', '</div>');

        // Add standard elements.
        $this->standard_coursemodule_elements();



        // Add standard buttons.
        $this->add_action_buttons();

        //JQUERY CODE

        echo '<script>            
        document.addEventListener("DOMContentLoaded", () => {
                $("#id_board_type_1").click(function () {
                    if ($(this).is(":checked")) {
                        $(".existing_board_block").hide();
                        $(".new_board_block").show();
                    }
                });
                $("#id_board_type_2").click(function () {
                    if ($(this).is(":checked")) {
                        $(".new_board_block").hide();
                        $(".existing_board_block").show();
                    }
                });
            });
            </script>';
    }

    public function definition_after_data()
    {
    }

    public function data_postprocessing($data)
    {
        parent::data_postprocessing($data);


        $array = json_decode(json_encode($data), true);

        if ($array['board_type'] == 2) {
            $meetingUrl = MOD_EFLOK_FRONTEND_BASE_URL . '/board#room=' . $array['existing_board_id'];

            $data->introeditor['text'] = $array->introeditor['text'] . ' &nbsp; <a href=' . $meetingUrl . ' target="_blank" >' . get_string('click_to_view_board', 'mod_eflok') . '</a>';
        } else {
            $payload = json_encode($data);
            $headers = array(
                'Content-Type: application/json',
            );

            $curl = new curl();
            $curl->setopt(array('CURLOPT_RETURNTRANSFER' => true, 'CURLOPT_HTTPHEADER' => $headers, 'CURLOPT_POSTFIELDS' => $payload));
            $moodleBoardListJson = $curl->post(MOD_EFLOK_CREATE_BOARD_URL, $payload);
            $finalResult = json_decode($moodleBoardListJson, true);
            

            //STORE DATA
            // $ch = curl_init(MOD_EFLOK_CREATE_BOARD_URL);
            // $headers = array(
            //     'Content-Type: application/json',
            // );
            // $payload = json_encode($data);
            // curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // $result = curl_exec($ch);
            // curl_close($ch);
            // $finalResult = json_decode($result, true);

            if (isset($finalResult['status']) && $finalResult['status']) {
                $meetingUrl = MOD_EFLOK_FRONTEND_BASE_URL . '/board#room=' . $finalResult['data']['meetingId'];
                $data->introeditor['text'] = $data->introeditor['text'] . ' &nbsp; <a href=' . $meetingUrl . ' target="_blank" >' . get_string('click_to_view_board', 'mod_eflok') . '</a>';
            }
        }
    }

    public function data_preprocessing(&$defaultvalues)
    {
    }
}
