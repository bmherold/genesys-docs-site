<?php
  require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/autoload.php';
  use GitElephant\Repository;

/**
 * Used to handle all Git/Bitbucket translations operations
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Yoda_WP
 * @subpackage Yoda_WP/includes
 */

/**
 * This class defines all code necessary to handle the wizard/announcement translations management.
 *
 * @since      1.0.0
 * @package    Yoda_WP
 * @subpackage Yoda_WP/includes
 * @author     Serge Margovsky <smargovsky@gmail.com>
 */
class Yoda_WP_Translations {

	public function __construct() {

	}

  public static function init_repository() {

    $tempFolder = plugin_dir_path( dirname( __FILE__ ) ) . 'tmp/yoda-translations-repo';
    // $tempFolder = tempnam(sys_get_temp_dir(), 'ge');
    $gitUrl = 'git@bitbucket.org:inindca/yoda-translations.git';

    error_log("&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&");
    error_log($tempFolder."\n");
    if (!file_exists($tempFolder)) {
      mkdir($tempFolder, 0755, true);
    }
    error_log("----------------------------------------------------");

    try {
      $repo = Repository::createFromRemote($gitUrl, $tempFolder);
    } catch (Exception $e) {
      return "Couldn't clone the repo";
    }

    return "init_repository() happened!";

  }

}
