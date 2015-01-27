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
    $ADMIN->add('development', new admin_externalpage('local_scripts',
        get_string('pluginname', 'local_scripts'),
        new moodle_url('/local/scripts/index.php')));
}