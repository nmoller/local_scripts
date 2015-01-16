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


function hide_section_piea($c_sec_ob){
    if (isset($c_sec_ob->visible)) $c_sec_ob->visible = 0;
    return $c_sec_ob;
}

/**
 * J'assume que c'est la seule condition que l'on a mis sur la section des devoirs....
 * @param $c_sec_ob
 * @return mixed
 */
function delete_section_restriction_on_piea($c_sec_ob){
    if (isset($c_sec_ob->availability)) $c_sec_ob->availability = NULL;
    return $c_sec_ob;
}

function clear_restrictions_hw($course_id){
    global $DB;
    $section2 = $DB->get_record('course_sections', array('course'=>$course_id, 'section'=>2));
    $section2 = hide_section_piea($section2);
    $DB->update_record('course_sections',$section2);

    $section4 = $DB->get_record('course_sections', array('course'=>$course_id, 'section'=>4));
    $section4 = delete_section_restriction_on_piea($section4);
    $DB->update_record('course_sections',$section4);
}
///////////////////***********************************///////////////////////////
$pieas = get_course_piea_ids();
echo 'Cours ids: ';
foreach ($pieas as $cp){
    echo $cp[0].'|';
    delete_piea_by_cmid(get_piea_cmid($cp[0], $cp[1]));
    clear_restrictions_hw($cp[0]);
}

exit(0);