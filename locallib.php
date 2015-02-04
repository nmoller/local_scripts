<?php
/**
 * Created by PhpStorm.
 * User: nmoller
 * Date: 15-01-26
 * Time: 11:26
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/formslib.php');


/**
 * Settings form for the CAD scorm.....
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_scripts_form extends moodleform {
    protected function definition() {
        $mform = $this->_form;

        $mform->addElement('static', '', '', get_string('info', 'local_scripts'));

        $mform->addElement('textarea', 'path', get_string('path', 'local_scripts'), array('rows' => 5, 'cols' => 90));
        $mform->setType('path', PARAM_TEXT);
        $mform->addRule('path', null, 'required', null, 'client');



        $mform->addElement('submit', 'submitbutton', get_string('create', 'local_scripts'));
    }
}