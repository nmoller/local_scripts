<?php
/**
 * Created by PhpStorm.
 * User: nmoller
 * Date: 15-01-26
 * Time: 11:25
 */
/**
 * Add page to admin menu.
 *
 * @package    local_scripts
 * @copyright  2015 Collège de Rosemont
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die;



if ($hassiteconfig) { // needs this condition or there is error on login page
    $temp = new admin_settingpage('cadpathscripts', new lang_string('pluginname','local_scripts'));
    $temp->add(new admin_setting_configtext('scormrepositoryname', new lang_string('reponame','local_scripts'),
        new lang_string('reponamemessage','local_scripts'), '', PARAM_ALPHANUM));

    // J'ajoute une nouvelle branche à dev.
    $ADMIN->add('courses', new admin_category('cadscripts', new lang_string('pluginname','local_scripts')));
    $ADMIN->add('cadscripts', $temp);

    $ADMIN->add('cadscripts', new admin_externalpage('local_scripts',
        get_string('taskcreatemanifest', 'local_scripts'),
        new moodle_url('/local/scripts/index.php')));
}