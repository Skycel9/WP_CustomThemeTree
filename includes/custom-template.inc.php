<?php
// Add some functions to quickly load template

/**
 * Loads the appropriate topbar template file.
 *
 * @param string|null $name Optional. The specific topbar name to load.
 * @param array $args Optional. Additional arguments to pass to the template.
 * @return bool True if the template was found and loaded, false otherwise.
 */
function tree_get_topbar(string $name = null, array $args = array()): bool {

    do_action("get_topbar", $name, $args);

    $templates = array();
    $name      = (string) $name;
    if ( '' !== $name ) {
        $templates[] = "topbar-{$name}.php";
    }

    $templates[] = 'topbar.php';

    if ( ! locate_template( $templates, true, true, $args ) ) {
        return false;
    }
    return true;
}