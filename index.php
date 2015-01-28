<?php
/**
 * Created by PhpStorm.
 * User: nmoller
 * Date: 15-01-26
 * Time: 11:25
 */

/**
 * Run the code checker from the web.
 *
 * @package    local_codechecker
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/local/scripts/locallib.php');

$path = optional_param('path', '', PARAM_PATH);

$pageparams = array();
if ($path) {
    $pageparams['path'] = $path;
}

admin_externalpage_setup('local_scripts', '', $pageparams);

/*
global $OUTPUT, $PAGE;

$PAGE->set_url('/local/scripts/index.php');
$PAGE->set_context(context_system::instance());
*/
$PAGE->set_pagelayout('admin');


require_once(__DIR__."/classes/Cad/Scripts/Helpers.php");
use Cad\Scripts\Helpers as hp;

$mform = new local_scripts_form(new moodle_url('/local/scripts/'));
$mform->set_data((object)$pageparams);
if ($data = $mform->get_data()) {
    redirect(new moodle_url('/local/scripts/', $pageparams));
}

echo $OUTPUT->header();

echo html_writer::tag('h2', get_string('titlecreate', 'local_scripts'));

// On s'attend à: path1 (path2) par ligne.
// TODO: boucle pour plusieurs.
if (isset($path) && $path!='') {
    $error = false;
    $several_files = explode(")", trim($path));
    // Efface le dernier valeur qui est vide
    if ($several_files[count($several_files)-1] == '')
        unset($several_files[count($several_files)-1]);

    $treated_files = array();
    foreach($several_files as $line){
        $file = new \stdClass();
        $vars = explode('(', trim($line));
        $path = (isset($vars[0])?trim($vars[0]):'');
        $pathrepository = (isset($vars[1])?trim($vars[1]):'');
        if ($path) {
            $error = false;
            // Valider que le lien pointe vers un manifest.
            if (substr_count($path, 'imsmanifest.xml') ==0){
                $error = true;
            }
            if (substr_count($pathrepository, 'imsmanifest.xml') ==0){
                $error = true;
            }
        }
        // On procède
        if (!$error) {
            global $DB, $CFG;
            // TODO: ne pas oublier sur le serveur de mettre comme deuxième paramètre le nom du rep.
            $test = new \stdClass();
            if (isset($CFG->scormrepositoryname) && $CFG->scormrepositoryname !='') {
                $test = new hp($DB, $CFG->scormrepositoryname);
            }
            else $test = new hp($DB);

            try {
                $res = $test->create_manifest_link($path, $pathrepository);
            }
            catch (Exception $e) {
                $res = false;
            }
            $file->file1 = $path;
            $file->file2 = $pathrepository;
            $file->message = ($res?get_string('manifestcreatedok', 'local_scripts', $file):
                get_string('manifestcreationissue', 'local_scripts', $file));

        }
        else {
            $file->message = get_string('nomanifest', 'local_scripts');
        }

        array_push($treated_files,$file);

    }

    // Message pour chaque élément traité
    foreach($treated_files as $file) echo($file->message);

}


$mform->display();
echo $OUTPUT->footer();