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
 * This file contains the annotation class for the assignfeedback_editpdfplus plugin
 *
 * @package   assignfeedback_editpdfplus
 * @copyright 2012 Davo Smith
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignfeedback_editpdfplus;

/**
 * This class adds and removes annotations from a page of a response.
 *
 * @package   assignfeedback_editpdfplus
 * @copyright 2012 Davo Smith
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class annotation {

    /** @var int unique id for this annotation */
    public $id = 0;

    /** @var int gradeid for this annotation */
    public $gradeid = 0;

    /** @var int page number for this annotation */
    public $pageno = 0;

    /** @var int starting location in pixels. Image resolution is 100 pixels per inch */
    public $x = 0;

    /** @var int ending location in pixels. Image resolution is 100 pixels per inch */
    public $endx = 0;

    /** @var int starting location in pixels. Image resolution is 100 pixels per inch */
    public $y = 0;

    /** @var int ending location in pixels. Image resolution is 100 pixels per inch */
    public $endy = 0;
    public $cartridgex = 0;
    public $cartridgey = 0;

    /** @var string path information for drawing the annotation. */
    public $path = '';

    /** @var int toolid for this annotation. */
    public $toolid = 0;

    /** @var string textannot, contains the text of this annotation */
    public $textannot = '';

    /** @var string colour - One of red, yellow, green, blue, white */
    public $colour = 'yellow';

    /** @var bool displaylock for displaying this annotation */
    public $displaylock = 0;

    /** @var bool displayrotation for displaying the sign of the annotation */
    public $displayrotation = 0;

    /** @var string borderstyle */
    public $borderstyle = '';

    /** @var int $parent_annot */
    public $parent_annot = 0;

    /**
     * Convert a compatible stdClass into an instance of this class.
     * @param stdClass $record
     */
    public function __construct(\stdClass $record = null) {
        if ($record) {
            $intcols = array('endx', 'endy', 'x', 'y');
            foreach ($this as $key => $value) {
                if (isset($record->$key)) {
                    if (in_array($key, $intcols)) {
                        $this->$key = intval($record->$key);
                    } else {
                        $this->$key = $record->$key;
                    }
                }
            }
        }
    }

}
