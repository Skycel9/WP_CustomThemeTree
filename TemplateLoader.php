<?php

namespace Skycel\CustomTree;

use Error;
use WP_Error;

class TemplateLoader {

    static array $templates_parts;

    protected function init() {
        add_filter("template_render", function ($templates_path, $type) {
            foreach ($templates_path as $path) {
                if (file_exists($path)) {
                    load_template($path);
                    exit;
                }
            }

            return new WP_Error("404", "Template not found");
        }, 10, 2);
    }

    public static function getTemplates($template, $type, $templates) {
        if ($type === "home" || $type === "frontpage") $type = "page";
        $key = (int)$type !== 0 ? "errors" : preg_replace("/y$/m", "ie", $type)."s";

        $template_path = get_template_directory() . "/" . TEMPLATES_DIR . "/" . @TEMPLATES_SUBDIRS[$key];

        $func_name = "get".ucfirst($key);
        call_user_func([self::class, $func_name], $template_path, $type, $templates);

        self::$templates_parts[] = get_template_directory() . "/" . TEMPLATES_DIR . "/index.php";

        do_action("template_render", self::$templates_parts, $type);
    }

    public static function getDefault($template_path, $type, $templates): void {
        foreach ($templates as $t) {
            $t = preg_replace("/^$type-/m", "", $t);
            self::$templates_parts[] = $template_path . "/" . $t;
        }
    }


    public static function getPages($template_path, $type, $templates): void {
        self::getDefault($template_path, $type, $templates);
    }

    public static function getArchives($template_path, $type, $templates): void {
        self::getDefault($template_path, $type, $templates);
    }

    public static function getAuthors($template_path, $type, $templates): void {
        self::getDefault($template_path, $type, $templates);
    }

    public static function getErrors($template_path, $type, $templates): void {
        self::getDefault($template_path, $type, $templates);
    }

    public static function getCategories($template_path, $type, $templates): void {
        self::getDefault($template_path, $type, $templates);
    }

    public static function getSingles($template_path, $type, $templates): void {
        $tmpl = [];

        foreach($templates as $i => $t) {
            $filename = preg_replace("/^single-/m", "", $t);
            $tax = "";
            preg_match("/[A-z0-9]+/", $filename, $tax);

            $tax = count($templates) - 1 <= $i ? "" : trailingslashit($tax[0]);

            $filename = str_replace( str_replace("/", "", $tax) . "-", "", $filename);

            $tmpl[] =  trailingslashit($template_path) . $tax . $filename;
        }
        $tmpl[] = $template_path . "/singular.php";

        self::$templates_parts = $tmpl;
    }

    public static function getAttachments($template_path, $type, $templates): void {
        foreach($templates as $i => $t) {
            $folder = "";
            preg_match("/^([A-z]+)-/m", $t, $folder);
            $folder = count($folder) > 1 ? trailingslashit($folder[1]) : "";
            $filename = sanitize_file_name(str_replace(sanitize_file_name($folder), "", $t));

            self::$templates_parts[] = trailingslashit($template_path) . $folder . $filename;
        }
    }

    public static function getTaxonomies($template_path, $type, $templates): void {
        $tmpl = [];

        foreach($templates as $i => $t) {
            $filename = preg_replace("/^taxonomy-/m", "", $t);
            $tax = "";
            preg_match("/[A-z0-9]+/", $filename, $tax);

            $tax = count($templates) - 1 <= $i ? "" : trailingslashit($tax[0]);

            $filename = str_replace( str_replace("/", "", $tax) . "-", "", $filename);

            $tmpl[] =  trailingslashit($template_path) . $tax . $filename;
        }

        self::$templates_parts = $tmpl;
    }

    public static function getTags($template_path, $type, $templates): void {
        self::getDefault($template_path, $type, $templates);
    }

    public static function getDates($template_path, $type, $templates): void {
        self::$templates_parts[] = is_year() ?  $template_path . "/year.php" : (is_month() ? $template_path ."/month.php" : $template_path . "/day.php");
        self::$templates_parts[] = $template_path . "/$type.php";
    }

    public static function getSearchs($template_path, $type, $templates): void {
        self::$templates_parts[] = $template_path . "/$type.php";
    }

    public static function getComponents($name, $args = null) {
        $hook_name = current_filter();

        $component_name = preg_replace("/^get_/m", "", $hook_name);

        $template_path = get_template_directory() . "/" . TEMPLATES_DIR . "/" . TEMPLATES_SUBDIRS["components"];

        self::$templates_parts[] = $template_path . "/" . $component_name . ".php";
//        comments_template();

        do_action("template_render", self::$templates_parts, $hook_name);
    }



    // Old code



    /*public static function getComponents($name, $args):string {
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
    }*/
}