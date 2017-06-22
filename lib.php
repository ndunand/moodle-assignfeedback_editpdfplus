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
 * This file contains the version information for the comments feedback plugin
 *
 * @package assignfeedback_editpdfplus
 * @copyright  2012 Davo Smith
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Serves assignment feedback and other files.
 *
 * @param mixed $course course or id of the course
 * @param mixed $cm course module or id of the course module
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options - List of options affecting file serving.
 * @return bool false if file not found, does not return if found - just send the file
 */
function assignfeedback_editpdfplus_pluginfile($course, $cm, context $context, $filearea, $args, $forcedownload, array $options = array()) {
    global $USER, $DB, $CFG;

    if ($context->contextlevel == CONTEXT_MODULE) {

        require_login($course, false, $cm);
        $itemid = (int) array_shift($args);

        if (!$assign = $DB->get_record('assign', array('id' => $cm->instance))) {
            return false;
        }

        $record = $DB->get_record('assign_grades', array('id' => $itemid), 'userid,assignment', MUST_EXIST);
        $userid = $record->userid;
        if ($assign->id != $record->assignment) {
            return false;
        }

        // Check is users feedback or has grading permission.
        if ($USER->id != $userid and ! has_capability('mod/assign:grade', $context)) {
            return false;
        }

        $relativepath = implode('/', $args);

        $fullpath = "/{$context->id}/assignfeedback_editpdfplus/$filearea/$itemid/$relativepath";

        $fs = get_file_storage();
        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            return false;
        }
        // Download MUST be forced - security!
        send_stored_file($file, 0, 0, true, $options); // Check if we want to retrieve the stamps.
    }
}

/**
 * 
 * @param navigation_node $navigation
 * @param stdClass $course
 * @param context_course $context
 */
function assignfeedback_editpdfplus_extend_navigation_course(navigation_node $navigation, stdClass $course, context_course $context) {
    $url = new moodle_url('/mod/assign/feedback/editpdfplus/view_admin.php', array('id' => $course->id));
    $feedbackadminnode = navigation_node::create('Feedback : configuration', $url, navigation_node::TYPE_CUSTOM, 'Bars d\'outils', 'editpdfplusadmin', new pix_icon('i/grades', ""));
    $navigation->add_node($feedbackadminnode);
}

/* function assignfeedback_editpdfplus_extend_navigation_course(navigation_node $parentnode, $course, $context) {
  $url = new moodle_url('/course/view.php', array('courseid' => $course->id));
  $settingsnode = navigation_node::create('test ND navigation node', $url,
  navigation_node::TYPE_SETTING, null, null, new pix_icon('i/settings', ''));
  $parentnode->add_node($settingsnode);
  } */

function assignfeedback_editpdfplus_output_fragment_axisadd($args) {
    global $CFG, $DB;

    $context = $args['context'];

    if ($context->contextlevel != CONTEXT_COURSE) {
        return null;
    }
    require_once('locallib_admin.php');

    if (has_capability('mod/assignfeedback_editpdfplus:use', $context, null, false)) {
        $course=$DB->get_record('course', array('id' => $context->instanceid), '*', MUST_EXIST);
        $editpdfplus = new assign_feedback_editpdfplus_admin($context, $course);
        return $editpdfplus->getAxisForm();
    }
    
    return null;
}

function assignfeedback_editpdfplus_output_fragment_axisedit($args) {
    global $CFG, $DB;

    $context = $args['context'];
    $axisid = $args['axeid'];

    if ($context->contextlevel != CONTEXT_COURSE) {
        return null;
    }
    require_once('locallib_admin.php');

    if (has_capability('mod/assignfeedback_editpdfplus:use', $context, null, false)) {
        $course=$DB->get_record('course', array('id' => $context->instanceid), '*', MUST_EXIST);
        $editpdfplus = new assign_feedback_editpdfplus_admin($context, $course);
        return $editpdfplus->getAxisForm($axisid);
    }
    
    return null;
}

function assignfeedback_editpdfplus_output_fragment_axisdel($args) {
    global $DB;

    $context = $args['context'];
    $axisid = $args['axeid'];

    if ($context->contextlevel != CONTEXT_COURSE) {
        return null;
    }
    require_once('locallib_admin.php');

    if (has_capability('mod/assignfeedback_editpdfplus:use', $context, null, false)) {
        $course=$DB->get_record('course', array('id' => $context->instanceid), '*', MUST_EXIST);
        $editpdfplus = new assign_feedback_editpdfplus_admin($context, $course);
        return $editpdfplus->getAxisDelForm($axisid);
    }
    
    return null;
}
