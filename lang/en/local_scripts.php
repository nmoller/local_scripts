<?php
/**
 * Created by PhpStorm.
 * User: nmoller
 * Date: 15-01-26
 * Time: 11:31
 */
/**
 * Language strings.
 *
 * @package    local_scripts
 * @copyright  2015 Collège de Rosemont
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'CAD local scripts';

$string['info'] = '<p>Recreates link to imsmanifest file after the transfer of a package from one instance to another.</p>
                   <p>Please, <b>be sure</b> that the repository contains the files before starting</p>
                   <p>Once you do the transfer your are going to get a warning like:
/4460/mod_scorm/package/0/imsmanifest.xml (420104FD-60-03/M1/M1 intro/imsmanifest.xml)</p>
<p>You are going to need both values.</p>';

$string['taskcreatemanifest'] = 'Create imsmanifest link';

$string['path'] = 'Message from scorm import process';
$string['pathrepository'] = 'Path in the repository';
$string['create'] = 'Link imsmanifest';
$string['cadscripts'] = 'Cad admin scripts. (Beta)';
$string['nomanifest'] = '<div class="alert alert-error" >The paths dont point to a imsmanifest.xml file!</div>';
$string['reponame'] = 'Repository Name';
$string['reponamemessage'] = 'Enter the name of the filesystem repository where you have unpackaged the scorm';

$string['manifestcreatedok'] = '<div class="alert alert-success" >OK {$a->file1} | {$a->file2}</div>';
$string['manifestcreationissue'] = '<div class="alert alert-error" >Error {$a->file1} | {$a->file2}</div>';