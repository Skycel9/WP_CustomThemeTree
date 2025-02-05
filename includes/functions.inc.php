<?php

/**
 * Merges multiple arrays recursively without duplicating scalar values for numeric keys
 * and overriding values for associative keys.
 *
 * @param array ...$arrays Arrays to be merged.
 * @return array The resulting merged array.
 */
function array_merge_recursive_distinct(array ...$arrays): array {
    $merged = [];

    foreach ($arrays as $array) {
        foreach ($array as $key => $value) {
            if (is_array($value) && array_key_exists($key, $merged) && is_array($merged[$key])) {
                // Appel récursif pour fusionner les tableaux enfants
                $merged[$key] = array_merge_recursive_distinct($merged[$key], $value);
            } elseif (is_array($value) && !array_key_exists($key, $merged)) {
                // Ajouter les nouveaux tableaux si la clé n'existe pas encore
                $merged[$key] = $value;
            } elseif (!is_array($value)) {
                if (is_int($key)) {
                    // Ajouter une valeur unique dans les clés numériques
                    if (!in_array($value, $merged, true)) {
                        $merged[] = $value;
                    }
                } else {
                    // Écraser pour les clés associatives
                    $merged[$key] = $value;
                }
            }
        }
    }

    return $merged;
}

/**
 * Checks if a directory is empty.
 *
 * @param string $dir Path to the directory to check.
 * @return bool True if the directory is empty, false otherwise.
 */
function is_dir_empty(string $dir): bool {
    return (count(scandir($dir)) == 2);
}

/**
 * Retrieves the URL of the blog archive for the specified blog ID.
 *
 * @param int|null $blog_id The ID of the blog. If null, uses the current blog ID.
 * @return bool|string The URL of the blog archive.
 */
function get_blog_url(int $blog_id = null): false|string {
    if (null === $blog_id) $blog_id = get_current_blog_id();

    if (get_current_blog_id() !== $blog_id && get_blogaddress_by_id($blog_id)) switch_to_blog($blog_id);

    return get_post_type_archive_link( 'post' );
}