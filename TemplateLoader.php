<?php

namespace Skycel\CustomTree;

use Error;
use WP_Error;

class TemplateLoader {

    public array $templates_parts;

    protected function init() {
        add_filter("template_render", function($templates_path, $type) {
//            dd($templates_path);
            foreach ($templates_path as $path) {
                if (file_exists($path)) {
//                    dd($path);
                    load_template($path);
                    exit;
                }
            }

            return new WP_Error("404", "Template not found");
        }, 10, 2);
    }

    public static function getTemplates($template, $type, $templates) {

        if ($type === "home" || $type === "frontpage") $type = "page";
        $key = preg_replace("/y$/m", "ie", $type)."s";

        $template_path = get_template_directory() . "/" . TEMPLATES_DIR . "/" . TEMPLATES_SUBDIRS[$key];

        if ($type === "taxonomy") {
            foreach($templates as $i => $t) {
                $filename = preg_replace("/^taxonomy-/m", "", $t);
                $tax = "";
                preg_match("/[A-z0-9]+/", $filename, $tax);

                if (count($templates) - 1 <= $i) {
                    $tax = "";
                } else {
                    $tax = $tax[0]."/";
                }

                $filename = str_replace( $tax . "-", "", $filename);

                $templates_parts[] = $template_path . "/" . $tax . $filename;
            }
        } else {
            foreach ($templates as $t) {
                $t = preg_replace("/^$type-/m", "", $t);
                $templates_parts[] = $template_path . "/" . $t;
            }
        }
        $templates_parts[] = get_template_directory() . "/" . TEMPLATES_DIR . "/index.php";
        do_action("template_render", $templates_parts, $type);
    }



    // Old code
    public static function getTemplate($template, $type, $templates): string|null {
        if ($type === "index") {
            return get_theme_root() . "/" . get_template() . "/" . TEMPLATES_DIR . "/" . $type . ".php";
        } elseif ($type === "single") {
            $type = str_replace(".php", "", $templates[count($templates) - 2]);
            return get_theme_root() . "/" . get_template() . "/" . TEMPLATES_DIR . "/" . TEMPLATES_SUBDIRS["components"] . "/" . $type . ".php";
        } else {
            return get_theme_root() . "/" . get_template() . "/" . TEMPLATES_DIR . "/" . TEMPLATES_SUBDIRS["components"] . "/" . $type . ".php";
        }
    }

    public static function getPages($template, $type, $templates): string|Error {
        global $wp_stylesheet_path;
        $wp_stylesheet_path = get_theme_root() ."/" . get_template() . "/" . TEMPLATES_DIR . "/" . TEMPLATES_SUBDIRS["pages"];

        if ($type === "frontpage") {
            $file = $wp_stylesheet_path . "/$type.php";

            return file_exists($file) ? $file : $wp_stylesheet_path . "/index.php";
        } elseif ($type === "home") {
            $file = $wp_stylesheet_path . "/home.php";

            return file_exists($file) ? $file : $wp_stylesheet_path . "/index.php";
        }

        $template_name = "";
        foreach($templates as $i => $t) {
            $filename = preg_replace("/^page-/m", "", $t);


            if (file_exists($wp_stylesheet_path . "/" . $filename)) {
                $template_name = $filename;
                break;
            } elseif ($filename === "page.php" && !file_exists($wp_stylesheet_path . "/" . $filename)) {
                $template_name = "index.php";
            } else  {
                $wp_stylesheet_path = $wp_stylesheet_path . "/";
                $template_name = "index.php";
            }
        }

        $file = $wp_stylesheet_path . "/" . $template_name;
        if (!file_exists($file)) {
            do_action("template_error", $template_name, $templates);
            return throw new Error("Template file not found: " . $file);
        }

        return $file;
    }
    /*public static function getPages($template, $type, $templates): string|Error {
        global $wp_stylesheet_path;
        $wp_stylesheet_path = get_theme_root() ."/" . get_template() . "/" . TEMPLATES_DIR . "/" . TEMPLATES_SUBDIRS["pages"];

        if ($type === "frontpage") {
            $file = $wp_stylesheet_path . "/$type.php";

            return file_exists($file) ? $file : $wp_stylesheet_path . "/index.php";
        } elseif ($type === "home") {
            $file = $wp_stylesheet_path . "/home.php";

            return file_exists($file) ? $file : $wp_stylesheet_path . "/index.php";
        }

        $template_name = "";
        foreach($templates as $i => $t) {
            $filename = preg_replace("/^page-/m", "", $t);


            if (file_exists($wp_stylesheet_path . "/" . $filename)) {
                $template_name = $filename;
                break;
            } elseif ($filename === "page.php" && !file_exists($wp_stylesheet_path . "/" . $filename)) {
                $template_name = "index.php";
            } else  {
                $wp_stylesheet_path = $wp_stylesheet_path . "/";
                $template_name = "index.php";
            }
        }

        $file = $wp_stylesheet_path . "/" . $template_name;
        if (!file_exists($file)) {
            do_action("template_error", $template_name, $templates);
            return throw new Error("Template file not found: " . $file);
        }

        return $file;
    }*/



    public static function getComponents($name, $args):string {
        global $wp_stylesheet_path;
        $wp_stylesheet_path = get_theme_root() . "/" . get_template() . "/" . TEMPLATES_DIR . "/" . TEMPLATES_SUBDIRS["components"];

        $hook_name = current_filter();

        $component_name = preg_replace("/^get_/m", "", $hook_name);

        $file = $wp_stylesheet_path . "/" . $component_name . ".php";

        if (file_exists($file)) {
            return $file;
        } else {
            return throw new Error("Template file not found: " . $file);
        }
    }

    public static function getArchives($template, $type, $templates): string {
        global $wp_stylesheet_path;
        $wp_stylesheet_path = get_theme_root() . "/" . get_template() . "/" . TEMPLATES_DIR . "/" . TEMPLATES_SUBDIRS["archives"];

        $template_name = preg_replace("/^archive-/m", "", str_replace(".php", "", $templates[count($templates) - 2]));

        return $wp_stylesheet_path . "/" . $template_name . ".php";
    }

    public static function getTags($template, $type, $templates): string|Error {
        global $wp_stylesheet_path;
        $wp_stylesheet_path = get_theme_root() . "/" . get_template() . "/" . TEMPLATES_DIR . "/" . TEMPLATES_SUBDIRS["tags"];

        $template_name = "";
        foreach($templates as $i => $t) {
            $filename = preg_replace("/^tag-/m", "", $t);
            if (file_exists($wp_stylesheet_path . "/" . $filename)) {
                $template_name = $filename;
                break;
            } else {
                $template_name = "index.php";
            }
        }

        $file = $wp_stylesheet_path . "/" . $template_name;
        if (!file_exists($file)) return throw new Error("Template file not found: " . $file);

        return $file;
    }

    public static function getCategories($template, $type, $templates): string|Error {
        global $wp_stylesheet_path;
        $wp_stylesheet_path = get_theme_root() . "/" . get_template() . "/" . TEMPLATES_DIR . "/" . TEMPLATES_SUBDIRS["categories"];

        $template_name = "";
        foreach($templates as $i => $t) {
            $filename = preg_replace("/^category-/m", "", $t);
            if (file_exists($wp_stylesheet_path . "/" . $filename)) {
                $template_name = $filename;
                break;
            } else {
                $template_name = "index.php";
            }
        }

        $file = $wp_stylesheet_path . "/" . $template_name;
        if (!file_exists($file)) return throw new Error("Template file not found: " . $file);

        return $file;
    }

    public static function getTaxonomies($template, $type, $templates): string|Error {
        global $wp_stylesheet_path;
        $wp_stylesheet_path = get_theme_root() . "/" . get_template() . "/" . TEMPLATES_DIR . "/" . TEMPLATES_SUBDIRS["taxonomies"];

        $template_name = "";
        foreach($templates as $i => $t) {
            $filename = preg_replace("/^taxonomy-/m", "", $t);
            $tax = "";
            preg_match("/[A-z0-9]+/", $filename, $tax);

            $filename = str_replace($tax[0] .  "-", "", $filename);
            var_dump($filename);
            if (file_exists($wp_stylesheet_path . "/" . $tax[0] . "/" . $filename)) {
                $template_name = $tax[0] . "/" . str_replace($tax[0] .  "-", "", $filename);
                break;
            } else if (file_exists($wp_stylesheet_path . "/" . $tax[0] . "/index.php")) {
                $template_name = $tax[0] . "/index.php";
                break;
            } else {
                $template_name = "index.php";
            }
        }

        $file = $wp_stylesheet_path . "/" . $template_name;
        if (!file_exists($file)) return throw new Error("Template file not found: " . $file);

        return $file;
    }
}