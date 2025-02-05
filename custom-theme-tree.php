<?php
/**
 * Plugin Name: Embedded themes tree
 * Description: Embed your theme tree files or custom as needed
 * Version: 1.0.0
 * Author: Skycel
 * Author URI: https://skycel.me
 */

use Skycel\CustomTree\CustomThemeTree;

require_once __DIR__ . "/custom-template.php";
require_once __DIR__ . "/functions.php";
require_once __DIR__ . "/includes/custom-template.inc.php";
require_once __DIR__ . "/includes/functions.inc.php";

function test_plugin(): void {
    if (!\defined('USE_CUSTOMTREE_PLUGIN') || USE_CUSTOMTREE_PLUGIN !== true) {
        return;
    }

    if (!\defined("CUSTOMTREE") || !is_array(CUSTOMTREE)) {
        new CustomThemeTree();
    } else {
        new CustomThemeTree(\CUSTOMTREE);
    }

/**
 * Loads the custom tree plugin if enabled and properly configured.
 *
 * @return void
 */
function load_plugin(): void {
    new CustomThemeTree();
}
add_action("after_setup_theme", "load_plugin");