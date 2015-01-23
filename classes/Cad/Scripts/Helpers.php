<?php
/**
 * Created by PhpStorm.
 * User: nmoller
 * Date: 15-01-19
 * Time: 15:10
 */

namespace Cad\Scripts;
//TODO: révoir selon l'emplacement final.
define('CLI_SCRIPT', true);
require_once __DIR__."/../../../../../config.php";


/**
 * Class Helpers
 * @package Cad\Scripts
 *
 * Usage:
 * L'importation d'un package SCORM va produire un message d'erreur du type
 * /1147/mod_scorm/package/0/imsmanifest.xml (420104FD-60-03/M1/M1 intro/imsmanifest.xml)
 *
 * Ce sont les deux paramètres dont on a besoin pour faire la création du  lien vers le fichier manifest.
 * Code nécessaire :
 * <pre>
 * <code class="php">
 * use Cad\Scripts\Helpers as hp;
 * global $DB;
 * $test = new hp($DB, '[repository_name]');
 * $test->create_manifest_link("/4462/mod_scorm/package/0/imsmanifest.xml","cours1/imsmanifest.xml" );
 * </code>
 * </pre>
 *
 */
class Helpers{

    private $db;
    private $repository_name;
    //sha1("") = EMPTY_HASH
    const EMPTY_HASH = "da39a3ee5e6b4b0d3255bfef95601890afd80709";

    /**
     * @param \moodle_database $DB
     * Un peu de DI pour rendre le code testable :)
     */
    public function __construct(\moodle_database $DB, $repository_name='scorm'){
        $this->db = $DB;
        $this->repository_name = $repository_name;
    }

    /**
     * @param $moodle_file_system_path -> "/1147/mod_scorm/package/0/imsmanifest.xml"
     * @param $repository_file_path    -> "420104FD-60-03/M1/M1 intro/imsmanifest.xml"
     */
    public function create_manifest_link($moodle_file_system_path, $repository_file_path){
        //créer root dans le système de fichier
        $root_path = $this->get_root_path($moodle_file_system_path);
        $rf_parts = explode('/', $root_path);
        $rf = new \stdClass();
        $rf->contenthash = self::EMPTY_HASH;
        $rf->pathnamehash = sha1($root_path);
        $rf->contextid = $rf_parts[1];
        $rf->component = $rf_parts[2];
        $rf->filearea = $rf_parts[3];
        $rf->itemid = $rf_parts[4];
        $rf->filepath = '/';
        $rf->filename = $rf_parts[5];
        $rf->userid = 2;
        $rf->filesize = 0;
        $rf->status = 0;
        $rf->timecreated = time();
        $rf->timemodified = time();
        $rf->sortorder = 0;

        $transaction = $this->db->start_delegated_transaction();
        $this->db->insert_record('files', $rf);
        //créer la référence au manifest
        $ref_id = $this->create_manifest_reference($repository_file_path);
        //créer le lien vers manifest dans le fs
        $mf_parts = explode('/', $moodle_file_system_path);
        $mf = new \stdClass();
        $mf->contenthash = self::EMPTY_HASH;
        $mf->pathnamehash = sha1($moodle_file_system_path);
        $mf->contextid = $mf_parts[1];
        $mf->component = $mf_parts[2];
        $mf->filearea = $mf_parts[3];
        $mf->itemid = $mf_parts[4];
        $mf->filepath = '/';
        $mf->filename = $mf_parts[5];
        $mf->userid = 2;
        //TODO: Validate!
        $mf->filesize = 777;
        $mf->mimetype = "application/xml";
        $mf->status = 0;
        $mf->source = $repository_file_path;
        $mf->timecreated = time();
        $mf->timemodified = time();
        $mf->sortorder = 0;
        $mf->referencefileid = $ref_id;

        $this->db->insert_record('files', $mf);
        $this->db->commit_delegated_transaction($transaction);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get_root_content_hash($name="imsmanifest.xml"){
        $query = "Select contenthash from {files} where filename=? order by id DESC ";//limit 1";
        $ret = $this->db-> get_record_sql($query, array($name),IGNORE_MULTIPLE);
        return $ret->contenthash;
    }

    /**
     * @param string $name
     * TODO: mettre le bon nom du repository
     * @return mixed
     */
    public function get_repository(){
        $query = "SELECT i.*, r.type AS repositorytype, r.sortorder, r.visible
                  FROM {repository} r, {repository_instances} i
                 WHERE i.typeid = r.id and i.name = ? and r.type='filesystem' ";

        return $this->db->get_record_sql($query, array($this->repository_name));
    }

    /**
     * Dans la table files references, on a la contrainte d'unicité sur
     * (referencehash, repositoryid)
     * @param $repository_file_path
     * @return bool|int
     *
     */
    public function create_manifest_reference($repository_file_path){
        $repo = $this->get_repository();

        $fr= new \stdClass();
        $fr->repositoryid = $repo->id;
        $fr->reference = $repository_file_path;
        $fr->referencehash = sha1($repository_file_path);

        //returne le id de la reference dont j'ai besoin pour créer la reference dans files
        if ($this->db->record_exists('files_reference',
            array('repositoryid'=>$fr->repositoryid, 'referencehash'=>$fr->referencehash))) {

            $ref = $this->db->get_record('files_reference',
                array('repositoryid'=>$fr->repositoryid, 'referencehash'=>$fr->referencehash), 'id');
            return $ref->id;
        }
        else
            return $this->db->insert_record('files_reference', $fr);

    }

    /**
     * Path pour .
     * @param $moodle_file_system_path
     * @return mixed
     */
    public function get_root_path($moodle_file_system_path){
        if (substr_count($moodle_file_system_path, 'imsmanifest.xml') ==0){
            die();
        }
        $len = strlen('imsmanifest.xml');
        return substr_replace($moodle_file_system_path, '.',-$len);
    }
}

//TODO: Commenter si l'on l'utilise ailleurs...

use Cad\Scripts\Helpers as hp;
global $DB;

$test = new hp($DB);
//var_dump($test->get_root_content_hash());
//var_dump($test->get_repository());
//echo $test->create_manifest_reference("cours1/imsmanifest.xml");
//echo $test->get_root_path("cours1/imsmanifest.xml");
## /4464/mod_scorm/package/0/imsmanifest.xml (420104FD-60-03/M1/M1 intro/imsmanifest.xml)
$test->create_manifest_link("/4464/mod_scorm/package/0/imsmanifest.xml","cours2/M1/M1 intro/imsmanifest.xml" );


