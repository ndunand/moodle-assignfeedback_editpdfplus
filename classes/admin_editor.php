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
 * This file contains the editor class for the assignfeedback_editpdfplus plugin
 *
 * @package   assignfeedback_editpdfplus
 * @copyright  2016 Université de Lausanne
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignfeedback_editpdfplus;

use assignfeedback_editpdfplus\axis;

/**
 * This class performs crud operations on comments and annotations from a page of a response.
 *
 * No capability checks are done - they should be done by the calling class.
 *
 * @package   assignfeedback_editpdfplus
 * @copyright 2017 Université de Lausanne
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_editor {

    /**
     * 
     * @global type $DB
     * @param type $axisLabel
     * @param type $context
     * @return type
     */
    public static function add_axis($axisLabel, $context) {
        global $DB;

        $record = $DB->get_record_sql('SELECT max(order_axis) as order_max FROM {assignfeedback_editpp_axis} WHERE contextid = :contextid', array('contextid' => $context));

        $axis = new axis();
        $axis->contextid = $context;
        $axis->label = $axisLabel;
        if ($record->order_max == null) {
            $axis->order_axis = 1;
        } else {
            $axis->order_axis = $record->order_max + 1;
        }

        return $DB->insert_record('assignfeedback_editpp_axis', $axis);
    }

    /**
     * 
     * @global type $DB
     * @param type $axisLabel
     * @param type $context
     * @return type
     */
    public static function add_tool($data, $contextid) {
        global $DB;

        $tools = array();
        $records = $DB->get_records('assignfeedback_editpp_tool', array('axis' => $data->axisid));
        foreach ($records as $record) {
            array_push($tools, new tool($record));
        }
        usort($tools, function($a, $b) {
            $al = $a->order_tool;
            $bl = $b->order_tool;
            if ($al == $bl) {
                return 0;
            }
            return ($al > $bl) ? +1 : -1;
        });

        $compteurPrecedent = null;
        $decalage = 1;
        foreach ($tools as $tool) {
            if ($compteurPrecedent == null) {
                $compteurPrecedent = $tool->order_tool;
            } else {
                $compteurCourant = $tool->order_tool;
                if ($compteurCourant <= $compteurPrecedent) {
                    $tool->order_tool = $compteurPrecedent + $decalage;
                    //$decalage++;
                }
                $compteurPrecedent++;
            }
        }

        $maxindice = $compteurPrecedent;

        $tool = new tool();
        $tool->axis = $data->axisid;
        $tool->cartridge = $data->libelle;
        $tool->cartridge_color = $data->cartridgecolor;
        $tool->contextid = $contextid;
        $tool->label = $data->button;
        if ($data->reply == "on") {
            $tool->reply = 1;
        } else {
            $tool->reply = 0;
        }
        $tool->texts = $data->texts;
        $tool->type = $data->typetool;
        if ($maxindice == null) {
            $tool->order_tool = 1;
        } elseif ($data->order && intval($data->order) < 1000) {
            $tool->order_tool = $data->order;

            $compteurPrecedent = null;
            $decalage = 1;
            foreach ($tools as $toolOr) {
                if ($compteurPrecedent == null && $data->order == $toolOr->order_tool) {
                    $compteurPrecedent = $toolOr->order_tool;
                    $toolOr->order_tool++;
                } else {
                    $compteurCourant = $toolOr->order_tool;
                    if ($compteurCourant == $compteurPrecedent) {
                        $toolOr->order_tool = $compteurPrecedent + $decalage;
                    }
                }
            }
            //$this->reorder_tool($data->axisid, 'id', 'desc');
        } else {
            $tool->order_tool = $maxindice;
        }

        $toolid = $DB->insert_record('assignfeedback_editpp_tool', $tool);
        foreach ($tools as $toolOr) {
            $DB->update_record('assignfeedback_editpp_tool', $toolOr);
        }
        if ($toolid > 0) {
            //$tool->id = $toolid;
            return $tool;
        }
        return null;
    }

    /**
     * 
     * @global type $DB
     * @param type $axisLabel
     * @param type $context
     * @return type
     */
    public static function edit_axis($axeid, $axisLabel) {
        global $DB;

        $axis = $DB->get_record('assignfeedback_editpp_axis', array('id' => $axeid), '*', MUST_EXIST);
        $axis->label = $axisLabel;
        return $DB->update_record('assignfeedback_editpp_axis', $axis);
    }

    /**
     * 
     * @global type $DB
     * @param type $axisid
     * @return type
     */
    public static function del_axis($axeid) {
        global $DB;
        return $DB->delete_records('assignfeedback_editpp_axis', array('id' => $axeid));
    }

    public static function del_tool($tool) {
        global $DB;
        return $DB->delete_records('assignfeedback_editpp_tool', array('id' => $tool->toolid));
    }

    public static function get_tools_by_axis($axisid) {
        global $DB;
        $tools = array();
        $records = $DB->get_records('assignfeedback_editpp_tool', array('axis' => $axisid));
        foreach ($records as $record) {
            //if ($record->id == $tool->id) {
            //    array_push($tools, $tool);
            //} else {
            array_push($tools, new tool($record));
            //}
        }
        usort($tools, function($a, $b) {
            $al = $a->order_tool;
            $bl = $b->order_tool;
            if ($al == $bl) {
                return 0;
            }
            return ($al > $bl) ? +1 : -1;
        });
        return $tools;
    }

    /**
     * 
     * @global type $DB
     * @param type $axisLabel
     * @param type $context
     * @return type
     */
    public static function edit_tool($toolJson) {
        global $DB;

        $tools = array();
        $records = $DB->get_records('assignfeedback_editpp_tool', array('axis' => $toolJson->axisid));
        foreach ($records as $record) {
            array_push($tools, new tool($record));
        }
        usort($tools, function($a, $b) {
            $al = $a->order_tool;
            $bl = $b->order_tool;
            if ($al == $bl) {
                return 0;
            }
            return ($al > $bl) ? +1 : -1;
        });
        $compteurPrecedent = null;
        $decalage = 1;
        foreach ($tools as $tool) {
            if ($compteurPrecedent == null) {
                $compteurPrecedent = $tool->order_tool;
            } else {
                $compteurCourant = $tool->order_tool;
                if ($compteurCourant <= $compteurPrecedent) {
                    $tool->order_tool = $compteurPrecedent + $decalage;
                    //$decalage++;
                }
                $compteurPrecedent++;
            }
        }
        foreach ($tools as $toolOr) {
            $DB->update_record('assignfeedback_editpp_tool', $toolOr);
        }

        $record = $DB->get_record('assignfeedback_editpp_tool', array('id' => $toolJson->toolid), '*', MUST_EXIST);
        $tool = new tool($record);
        $tool->type = $toolJson->typetool;
        $tool->colors = $toolJson->color;
        $tool->cartridge = $toolJson->libelle;
        $tool->cartridge_color = $toolJson->catridgecolor;
        $tool->texts = $toolJson->texts;
        $tool->label = $toolJson->button;
        $tool->enabled = $toolJson->enabled;
        if ($toolJson->reply == "on") {
            $tool->reply = 1;
        } else {
            $tool->reply = 0;
        }
        $tool->order_tool = $toolJson->order;
        if ($DB->update_record('assignfeedback_editpp_tool', $tool)) {
            return $tool;
        }
        return null;
    }

    /**
     * Get all the type tools.
     * @param array $contextidlist
     * @return type_tool
     */
    public static function get_typetools() {
        global $DB;
        $typetools = array();
        $records = $DB->get_records('assignfeedback_editpp_typet', array('configurable' => 1));
        foreach ($records as $record) {
            array_push($typetools, new type_tool($record));
        }
        return $typetools;
    }

}
