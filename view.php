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
 * Prints a particular instance of tadc
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod
 * @subpackage tadc
 * @copyright  2013 Talis Education Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/// (Replace tadc with the name of your module and remove this line)

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$t  = optional_param('n', 0, PARAM_INT);  // tadc instance ID - it should be named as the first character of the module

if ($id) {
    $cm         = get_coursemodule_from_id('tadc', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $tadc  = $DB->get_record('tadc', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($n) {
    $tadc  = $DB->get_record('tadc', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $tadc->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('tadc', $tadc->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);
$title = tadc_build_title_string($tadc);
add_to_log($course->id, 'tadc', 'view', "view.php?id={$cm->id}", $title, $cm->id);

/// Print the page header

$PAGE->set_url('/mod/tadc/view.php', array('id' => $cm->id));

$PAGE->set_title(format_string($title));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);
$PAGE->navbar->ignore_active(true);
$PAGE->navbar->add($course->fullname, new moodle_url("/course/view.php", array("id"=>$course->id)));
$PAGE->navbar->add($title, new moodle_url("/mod/tadc/view.php", array("id"=>$cm->id)));

// other things you may want to set - remove if not needed
//$PAGE->set_cacheable(false);
//$PAGE->set_focuscontrol('some-html-id');
//$PAGE->add_body_class('tadc-'.$somevar);

// Output starts here
echo $OUTPUT->header();

//if ($tadc->section_title) { // Conditions to show the intro can change to look for own settings or whatever
//    echo $OUTPUT->box(format_module_intro('tadc', $tadc, $cm->id), 'generalbox mod_introbox', 'tadcintro');
//}

// Replace the following lines with you own code
$requestMarkup = '<div class="tadc-request-metadata">';

if($tadc->section_creator && $tadc->section_creator != $tadc->container_creator)
{
    $requestMarkup .= $tadc->section_creator . ' ';
} elseif ((!$tadc->section_creator && $tadc->container_creator) || ($tadc->section_creator === $tadc->container_creator))
{
    $requestMarkup .= $tadc->container_creator . ' ';
}
if($tadc->publication_date)
{
    $requestMarkup .= $tadc->publication_date . ' ';
}
if($tadc->section_title)
{
    $requestMarkup .= "'" . $tadc->section_title . "' ";
}
if($tadc->type === 'book' && $tadc->section_title && ($tadc->container_title || $tadc->container_identifier))
{
    $requestMarkup .= ' in ';
}
if($tadc->container_title)
{
    $requestMarkup .= '<em>' . $tadc->container_title . '</em> ';
} elseif($tadc->container_identifier)
{
    $requestMarkup .= '<em>' . preg_replace('/^(\w*:)/e', 'strtoupper("$0") . " "', $tadc->container_identifier) . '</em>, ';
}
if($tadc->volume)
{
    $requestMarkup .= 'vol. ' . $tadc->volume . ', ';
}

if($tadc->issue)
{
    $requestMarkup .= 'no. ' . $tadc->issue . ', ';
}

if($tadc->section_creator && $tadc->container_creator && ($tadc->section_creator !== $tadc->container_creator))
{
    $requestMarkup .= $tadc->container_creator . ' ';
}
if($tadc->type === 'book' && $tadc->publisher)
{
    $requestMarkup .= $tadc->publisher;
}
if($tadc->start_page && $tadc->end_page)
{
    $requestMarkup .= 'pp. ' . $tadc->start_page . '-' . $tadc->end_page;
} elseif($tadc->start_page)
{
    $requestMarkup .= 'p.' . $tadc->start_page;
}

$requestMarkup = chop(trim($requestMarkup),",") . '.';

$requestMarkup .= '</div>';
if($tadc->request_status)
{
    $requestMarkup .= '<div class="tadc-request-status"><dl><dt>Status</dt><dd>' . $tadc->request_status . '</dd>';
    if($tadc->status_message)
    {
        $requestMarkup .= '<dt>Reason</dt><dd>' . $tadc->status_message . '</dd>';
    }
    $requestMarkup .= '</dl></div>';
}
echo $OUTPUT->box($requestMarkup);
// Finish the page
echo $OUTPUT->footer();

