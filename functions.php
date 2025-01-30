<?php
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

function is_dir_empty($dir): bool {
    return (count(scandir($dir)) == 2);
}