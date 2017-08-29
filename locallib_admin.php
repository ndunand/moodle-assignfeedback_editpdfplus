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
 * Description of locallib_admin
 *
 * @package   assignfeedback_editpdfplus
 * @copyright  2017 Université de Lausanne
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

use \assignfeedback_editpdfplus\page_editor;
use \assignfeedback_editpdfplus\widget_admin;
use \assignfeedback_editpdfplus\form\axis_form;
use \assignfeedback_editpdfplus\form\axis_import_form;
use \assignfeedback_editpdfplus\form\axis_del_form;
use \assignfeedback_editpdfplus\admin_editor;

class assign_feedback_editpdfplus_admin {

    private $course = null;
    private $context = null;

    function __construct(stdClass $context, stdClass $course) {
        $this->context = $context;
        $this->course = $course;
    }

    /**
     * todo.
     *
     * @return string
     */
    public function view() {
        global $PAGE;

        //$PAGE->requires->js_call_amd('assignfeedback_editpdfplus/admin_panel', 'init');

        $html = '';
        //$toform = null;
        $renderer = $PAGE->get_renderer('assignfeedback_editpdfplus');
        /* $formAddAxis = new axis_form(new moodle_url('/mod/assign/feedback/editpdfplus/view_admin.php?id=' . $this->course->id, array('id' => $this->course->id))); //Form processing and displaying is done here
          if ($formAddAxis->is_cancelled()) {
          //Handle form cancel operation, if cancel button is present on form
          } else if ($fromform = $formAddAxis->get_data()) {
          //In this case you process validated data. $mform->get_data() returns data posted in form.
          $label = $fromform->label;
          $axe = $this->addAxis($label);
          $formAddAxis->set_data($toform);
          return $axe; //"<option>tutu</option>";
          } else {
          // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
          // or on the first display of the form.
          //Set default data (if any)
          $formAddAxis->set_data($toform);
          } */
        $axisimportform = new axis_import_form(null, array('id' => $this->course->id), null, null, array('id' => "assignfeedback_editpdfplus_import_axis"));
        $axisimportform->id = "assignfeedback_editpdfplus_import_axis";
        $axisimportform->title = "";
        $axisimportform->action = "import";
        $widget = $this->get_widget();
        //$widget->axisaddform = $formAddAxis;
        $widget->axisimportform = $axisimportform;
        $widget->courseid = $this->course->id;
        $html .= $renderer->render_assignfeedback_editpdfplus_widget_admin($widget);
        return $html;
    }

    public function getAxisForm($axeid = null) {
        global $PAGE, $DB;

        $html = '';
        $toform = null;
        $formAxis = null;
        $axis = null;
        if ($axeid != null) {
            $axis = $DB->get_record('assignfeedback_editpp_axis', array('id' => $axeid), '*', MUST_EXIST);
        }
        if ($axis != null) {
            $formAxis = new axis_form(null, array('id' => $this->course->id), null, null, array('id' => "assignfeedback_editpdfplus_edit_axis")); //Form processing and displaying is done here
            $formAxis->set_data(array('axeid' => $axeid, 'label' => $axis->label));
            $formAxis->id = "assignfeedback_editpdfplus_edit_axis";
            $formAxis->title = "Renommer l'axe";
            $formAxis->action = "edit";
        } else {
            $formAxis = new axis_form(null, array('id' => $this->course->id), null, null, array('id' => "assignfeedback_editpdfplus_add_axis")); //Form processing and displaying is done here
            $formAxis->set_data($toform);
            $formAxis->id = "assignfeedback_editpdfplus_add_axis";
            $formAxis->title = "Ajouter un nouvel axe";
            $formAxis->action = "add";
        }
        $renderer = $PAGE->get_renderer('assignfeedback_editpdfplus');
        $formAxis->courseid = $this->course->id;
        $html .= $renderer->render_assignfeedback_editpdfplus_widget_admin_axisform($formAxis);
        return $html;
    }

    public function getAxisDelForm($axeid) {
        global $PAGE, $DB;

        $html = '';
        $formAxis = null;
        $axis = null;
        if ($axeid != null) {
            $axis = $DB->get_record('assignfeedback_editpp_axis', array('id' => $axeid), '*', MUST_EXIST);
        }
        if ($axis != null) {
            $formAxis = new axis_del_form(null, array('id' => $this->course->id), null, null, array('id' => "assignfeedback_editpdfplus_del_axis")); //Form processing and displaying is done here
            $formAxis->set_data(array('axeid' => $axeid, 'label' => $axis->label));
        }
        $formAxis->id = "assignfeedback_editpdfplus_del_axis";
        $formAxis->title = "Supprimer l'axe";
        $formAxis->action = "del";
        $renderer = $PAGE->get_renderer('assignfeedback_editpdfplus');
        $formAxis->courseid = $this->course->id;
        $html .= $renderer->render_assignfeedback_editpdfplus_widget_admin_axisdelform($formAxis);
        return $html;
    }

    public function getToolForm($toolid = null, $axisid = null) {
        global $PAGE, $DB;

        $html = '';
        $data = new stdClass;
        $data->courseid = $this->course->id;
        $data->sesskey = sesskey();
        $data->actionurl = "/moodle/lib/ajax/service.php";
        $data->formid = "assignfeedback_editpdfplus_edit_tool";
        if ($toolid != null) {
            $data->tool = $DB->get_record('assignfeedback_editpp_tool', array('id' => $toolid), '*', MUST_EXIST);
            $data->tool->removable = true;
            $nbEnregistrements = $DB->get_record_sql('SELECT count(*) as val FROM {assignfeedback_editpp_annot} WHERE toolid = ?', array('toolid' => $toolid));
            if ($nbEnregistrements->val > 0) {
                $data->tool->removable = false;
            }
        } else {
            $tool = new \assignfeedback_editpdfplus\tool();
            $tool->contextid = $this->context->id;
            $tool->enabled = true;
            $tool->axis = $axisid;
            $tool->removable = true;
            $data->tool = $tool;
        }
        $tooltexts = $data->tool->texts;
        if ($tooltexts) {
            $tooltextsarray = explode("\",\"", $tooltexts);
            $compteur = 0;
            foreach ($tooltextsarray as $value) {
                if ($value && $value != '"') {
                    $obj = new object();
                    $obj->text = /* substr( */$value/* ,1,-1) */;
                    if (substr($obj->text, 0, 1) == '"') {
                        $obj->text = substr($obj->text, 1);
                    }
                    if (substr($obj->text, -1) == '"') {
                        $obj->text = substr($obj->text, 0, -1);
                    }
                    $obj->index = $compteur;
                    $data->tool->textsarray[] = $obj;
                    $compteur++;
                }
            }
        } else {
            $data->tool->textsarray = null;
        }
        $data->tools = admin_editor::get_typetools();
        foreach ($data->tools as $toolRef) {
            $toolRef->libelle = get_string('typetool_' . $toolRef->label, 'assignfeedback_editpdfplus');
        }
        $renderer = $PAGE->get_renderer('assignfeedback_editpdfplus');
        //$formTool->courseid = $this->course->id;
        $html .= $renderer->render_assignfeedback_editpdfplus_widget_admin_toolform($data);
        return $html;
    }

    private function get_widget() {
        global $USER;

        // get the costum toolbars
        $coursecontext = context::instance_by_id($this->context->id);
        $coursecontexts = array_filter(explode('/', $coursecontext->path), 'strlen');
        $tools = page_editor::get_tools($coursecontexts);
        $typetools = page_editor::get_typetools(null);
        $axis = page_editor::get_axis(array($this->context->id));
        $toolbars = $this->prepareToolbar($axis, $tools);
        // get all accessibled toolbars
        $courses = get_courses();
        $contextListToCheck = array();
        foreach ($courses as $course) {
            $contextid = context_course::instance($course->id);
            $coursecontextsTmp = array_filter(explode('/', $contextid->path), 'strlen');
            foreach ($coursecontextsTmp as $value) {
                if ($value != $this->context->id && !in_array($value, $contextListToCheck)) {
                    $contextListToCheck[] = $value;
                }
            }
        }
        $axisDispo = array();
        $toolDispo = page_editor::get_tools($contextListToCheck);
        foreach ($contextListToCheck as $value) {
            $axistmp = page_editor::get_axis(array($value));
            if ($axistmp && sizeof($axistmp) > 0) {
                $axisDispo = array_merge($axisDispo, $axistmp);
            }
        }
        $toolbarsDispo = $this->prepareToolbar($axisDispo, $toolDispo);
        $widget = new widget_admin($this->context, $this->course, $USER, $toolbars, $axis, $typetools, $toolbarsDispo);
        return $widget;
    }

    private function prepareToolbar($axis, $tools) {
        $toolbars = array();
        foreach ($axis as $ax) {
            $ax->children = 0;
            $toolbar = new stdClass();
            $toolbar->axis = $ax;
            $toolbar->tools = array();
            //$toolbars[$ax->id]['label'] = $ax->label;
            foreach ($tools as $tool) {
                if ($tool->axis == $ax->id) {
                    if ($tool->enabled == "1") {
                        $tool->button = "btn-default";
                        $tool->style = "";
                    } else {
                        $tool->button = "";
                        $tool->style = "background-image:none;background-color:#CCCCCC;";
                    }
                    if ($tool->type == "4") {
                        $tool->label = '| ' . $tool->label . ' |';
                    } elseif ($tool->type == "5") {
                        $tool->label = '| ' . $tool->label;
                    }
                    if ($tool->type == "4" || $tool->type == "1") {
                        $tool->style .= "text-decoration: underline;";
                    }
                    $toolbar->tools[] = $tool;
                    $ax->children++;
                }
            }
            $toolbars[] = $toolbar;
        }
        return $toolbars;
    }

}