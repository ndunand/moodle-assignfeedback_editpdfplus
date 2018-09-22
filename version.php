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
 * @copyright  2016 Université de Lausanne
 * The code is based on mod/assign/feedback/editpdf/version.php by Davo Smith.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2018092002;
$plugin->requires  = 2017050500; // Moodle 3.3
$plugin->maturity = MATURITY_STABLE;
$plugin->release = "2.2.2 (Build: 2018092002)";
$plugin->dependencies = array(
    'assignfeedback_editpdf' => 2017051500
);
$plugin->component = 'assignfeedback_editpdfplus';

