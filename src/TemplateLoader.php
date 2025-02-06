<?php

namespace Skycel\CustomThemeTree;

use WP_Error;

/**
 * Handles loading and rendering of WordPress templates based on their type
 * and subdirectories. Supports various template types, such as pages, archives,
 * taxonomies, components, and more.
 */
class TemplateLoader {

    static array $templates_parts;

    /**
     * Initializes the template rendering process.
     *
     * @return void
     */
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

    /**
     * Retrieves and processes templates based on the specified type and template data.
     *
     * @param string $template The primary template name or path.
     * @param string $type The type of template being requested (e.g., "home", "frontpage").
     * @param array $templates A list of additional template names or paths.
     *
     * @return void
     */
    public static function getTemplates($template, $type, $templates) {
        if ($type === "home" || $type === "frontpage") $type = "page";
        $key = (int)$type !== 0 ? "errors" : preg_replace("/y$/m", "ie", $type)."s";

        $template_path = get_template_directory() . "/" . TEMPLATES_DIR . "/" . @TEMPLATES_SUBDIRS[$key];

        $func_name = "get".ucfirst($key);
        call_user_func([self::class, $func_name], $template_path, $type, $templates);

        self::$templates_parts[] = get_template_directory() . "/" . TEMPLATES_DIR . "/index.php";

        do_action("template_render", self::$templates_parts, $type);
    }

    /**
     * Processes templates and builds template parts based on the given type and path.
     *
     * @param string $template_path The base path for the templates.
     * @param string $type The type prefix to be removed from template names.
     * @param array $templates List of templates to process.
     * @return void
     */
    public static function getDefault($template_path, $type, $templates): void {
        foreach ($templates as $t) {
            $t = preg_replace("/^$type-/m", "", $t);
            self::$templates_parts[] = $template_path . "/" . $t;
        }
    }


    /**
     * Retrieves pages based on the provided template path, type, and templates.
     *
     * @param string $template_path The path to the template.
     * @param string $type The type of the template.
     * @param array $templates The list of templates.
     * @return void
     */
    public static function getPages($template_path, $type, $templates): void {
        self::getDefault($template_path, $type, $templates);
    }

    /**
     * Retrieves and processes archive templates.
     *
     * @param string $template_path Path to the template.
     * @param string $type Type of template.
     * @param array $templates List of templates.
     * @return void
     */
    public static function getArchives($template_path, $type, $templates): void {
        self::getDefault($template_path, $type, $templates);
    }

    /**
     * Retrieves authors based on the provided parameters.
     *
     * @param string $template_path The path of the template.
     * @param string $type The type of the template.
     * @param array $templates The list of templates.
     * @return void
     */
    public static function getAuthors($template_path, $type, $templates): void {
        self::getDefault($template_path, $type, $templates);
    }

    /**
     * Retrieves errors by processing templates with a default method.
     *
     * @param string $template_path Path to the template directory.
     * @param string $type Type of templates being processed.
     * @param array $templates List of template files.
     * @return void
     */
    public static function getErrors($template_path, $type, $templates): void {
        self::getDefault($template_path, $type, $templates);
    }

    /**
     * Retrieves and processes category templates.
     *
     * @param string $template_path Path to the template directory.
     * @param string $type Type of the template.
     * @param array $templates List of templates to process.
     * @return void
     */
    public static function getCategories($template_path, $type, $templates): void {
        self::getDefault($template_path, $type, $templates);
    }

    /**
     * Generates an array of single templates with processed paths and stores them in the template parts.
     *
     * @param string $template_path The base path to the templates.
     * @param string $type The type of template being processed.
     * @param array $templates A list of template names to process.
     * @return void
     */
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

    /**
     * Generates and sets attachment file paths based on provided templates.
     *
     * @param string $template_path Base path for the templates.
     * @param string $type Type of attachment (not explicitly used in method logic).
     * @param array $templates List of template filenames.
     * @return void
     */
    public static function getAttachments($template_path, $type, $templates): void {
        foreach($templates as $i => $t) {
            $folder = "";
            preg_match("/^([A-z]+)-/m", $t, $folder);
            $folder = count($folder) > 1 ? trailingslashit($folder[1]) : "";
            $filename = sanitize_file_name(str_replace(sanitize_file_name($folder), "", $t));

            self::$templates_parts[] = trailingslashit($template_path) . $folder . $filename;
        }
    }

    /**
     * Updates the list of templates with taxonomy-specific paths based on the provided data.
     *
     * @param string $template_path The base path to the templates.
     * @param string $type The type of taxonomy being processed.
     * @param array $templates An array of template file names to process.
     *
     * @return void
     */
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

    /**
     * Retrieves tags based on the provided parameters.
     *
     * @param string $template_path The path to the template.
     * @param string $type The type of the template.
     * @param array $templates The list of templates.
     * @return void
     */
    public static function getTags($template_path, $type, $templates): void {
        self::getDefault($template_path, $type, $templates);
    }

    /**
     * Generates date-based template paths and appends them to the templates parts list.
     *
     * @param string $template_path The base path for the templates.
     * @param string $type The type of template to append.
     * @param array $templates Not used in this method.
     * @return void
     */
    public static function getDates($template_path, $type, $templates): void {
        self::$templates_parts[] = is_year() ?  $template_path . "/year.php" : (is_month() ? $template_path ."/month.php" : $template_path . "/day.php");
        self::$templates_parts[] = $template_path . "/$type.php";
    }

    /**
     * Appends a search template path to the templates array.
     *
     * @param string $template_path The base path to the templates.
     * @param string $type The type of the search template.
     * @param array $templates The list of existing templates.
     * @return void
     */
    public static function getSearchs($template_path, $type, $templates): void {
        self::$templates_parts[] = $template_path . "/$type.php";
    }

    /**
     * Retrieves and processes the components associated with the current filter hook.
     *
     * @param string $name The name of the component being retrieved.
     * @param mixed|null $args Optional. Additional arguments passed to the component.
     * @return void
     */
    public static function getComponents($name, $args = null) {
        $hook_name = current_filter();

        $component_name = preg_replace("/^get_/m", "", $hook_name);

        $template_path = get_template_directory() . "/" . TEMPLATES_DIR . "/" . TEMPLATES_SUBDIRS["components"];

        $components[] = $template_path . "/" . $component_name . ".php";

        do_action("template_render", $components, $hook_name);
    }
}