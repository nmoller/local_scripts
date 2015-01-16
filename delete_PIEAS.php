<?php



/**
 * @package    core
 * @subpackage cli
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->libdir.'/clilib.php');

list($options, $unrecognized) = cli_get_params(array('help' => false), array('h' => 'help'));

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized), 2);
}

if ($options['help']) {
    $help =
        "Fait le ménage...

        Options:
        -h, --help            Print out this help

        Example:
        \$sudo -u www-data /usr/bin/php admin/cli/purge_caches.php
        ";

    echo $help;
    exit(0);
}

;

function get_course_ids_array(){
    global $DB;
    $courses_id = $DB->get_records_select('quiz',"name like 'Qualité de la langue%'",null,null,'course');
    $ret = array();
    if (is_array($courses_id) && !empty($courses_id)){
        foreach($courses_id as $id => $c_id){
            array_push($ret, $id);
        }
    }
    return $ret;
}

/**
 * On obtient un array de  course_id et quiz_id(PIEA)
 *
 * @return array
 */
function get_course_piea_ids(){
    global $DB;
    $courses_id = $DB->get_records_select('quiz',"name like 'Qualité de la langue%'",null,null,'course, id');
    $ret = array();
    if (is_array($courses_id) && !empty($courses_id)){
        foreach($courses_id as $id => $c_id){
            array_push($ret, array($id, $c_id->id));
        }
    }
    return $ret;
}

function delete_piea_by_cmid($cmid){
    global $CFG;
    require_once $CFG->dirroot.'/course/lib.php';
    return course_delete_module($cmid);
}

function get_piea_cmid($course_id, $instance_id){
    global $DB;
    $cm_id_arr= $DB->get_field('course_modules','id', array('module'=>16, 'course'=>$course_id, 'instance'=>$instance_id) );

    return (int) $cm_id_arr;

}

//var_dump(get_course_ids_array());
$pieas = get_course_piea_ids();
echo get_piea_cmid($pieas[0][0], $pieas[0][1]);

exit(0);