<?php
// Add some functions to quickly load template

function tree_get_topbar($name = null, $args = array()) {

    do_action("get_topbar", $name, $args);

    return false;

    $templates = array();
    $name      = (string) $name;
    if ( '' !== $name ) {
        $templates[] = "topbar-{$name}.php";
    }

    $templates[] = 'topbar.php';

    if ( ! locate_template( $templates, true, true, $args ) ) {
        return false;
    }
}

function get_blog_url($blog_id = null) {
    if (null === $blog_id) $blog_id = get_current_blog_id();

    if (get_current_blog_id() !== $blog_id && get_blogaddress_by_id($blog_id)) switch_to_blog($blog_id);

    return get_post_type_archive_link( 'post' );
}