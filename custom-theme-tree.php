<?php
/**
 * Plugin Name: Embedded themes tree
 * Description: Embed your theme tree files or custom as needed
 * Version: 1.0.0
 * Author: Skycel
 * Author URI: https://skycel.me
 */

use Skycel\CustomTree\CustomThemeTree;

require_once __DIR__ . "/includes/custom-template.inc.php";
require_once __DIR__ . "/includes/functions.inc.php";

/**
 * Loads the custom tree plugin if enabled and properly configured.
 *
 * @return void
 */
function load_plugin(): void {
    new CustomThemeTree();
}
add_action("after_setup_theme", "load_plugin");