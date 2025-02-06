<?php
/**
 * Plugin Name: Embedded themes tree
 * Description: Embed your theme tree files or custom as needed
 * Version: 1.0.0
 * Author: Skycel
 * Author URI: https://skycel.me
 */

/**
 * This file is required for Wordpress plugin system
 * If you use composer, you're not concerned
 */

use Skycel\CustomTree\CustomThemeTree;

require_once __DIR__ . '/src/CustomThemeTree.php';

/**
 * Loads the custom tree plugin if enabled and properly configured.
 *
 * @return void
 */
function load_plugin(): void {
    new CustomThemeTree();
}
add_action("after_setup_theme", "load_plugin");