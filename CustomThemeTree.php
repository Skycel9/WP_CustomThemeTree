<?php

namespace Skycel\CustomTree;

use WP_Theme;
use Error;

/**
 * Class for managing custom theme directory structures and settings.
 * Provides functionality for initializing themes, creating custom directories,
 * handling templates and stylesheets, and loading configurations.
 */
class CustomThemeTree extends TemplateLoader {
    public WP_Theme $theme;

    public string $theme_directory;
    public string $templates_directory;
    public string $stylesheets_directory;
    public string $config_directory;

    public array $default = array(
        "templates"=> "templates",
        "stylesheets"=> "assets",
        "templates_subdirs"=> [
            "archives"=> "archives",
            "attachments"=> "attachments",
            "authors"=> "authors",
            "categories"=> "categories",
            "components"=> "components",
            "dates"=> "dates",
            "pages"=> "pages",
            "singles"=> "singles",
            "tags"=> "tags",
            "taxonomies"=> "taxonomies"
        ],
        "stylesheets_subdirs"=> [
            "css"=> "css"
        ]
    );

    public function __construct($dirs = null, $config_file = "theme.php") {

    /**
     * Constructor method to initialize theme settings and configuration.
     * Handles the creation of a configuration directory and file if they do not exist,
     * and merges custom configurations if enabled.
     *
     * @return void
     */
    public function __construct() {

        $this->theme = wp_get_theme();
        $this->theme_directory = $this->theme->get_theme_root() . "/" . get_template();
        $this->config_directory = "config";

        if (!file_exists(trailingslashit($this->theme_directory) . trailingslashit($this->config_directory) . "theme.php")) {
            if (!is_dir(trailingslashit($this->theme_directory) . $this->config_directory )) {
                $this->config_directory = $this->create_directory($this->config_directory);
            }
            file_put_contents($this->theme_directory . "/" . $this->config_directory . "/theme.php", "<?php\n\nconst USE_GITKEEP = true;\n\nconst USE_CUSTOMTREE_PLUGIN = true;\nconst CUSTOMTREE = array()");
        }
        require_once $this->theme_directory . "/" . $this->config_directory . "/" . $config_file;
        require_once $this->theme_directory . "/" . $this->config_directory . "/" . "theme.php";

        if (!USE_CUSTOMTREE_PLUGIN) return;

        if (is_array(CUSTOMTREE) && count(CUSTOMTREE) > 0) {
            $this->default = array_merge_recursive_distinct($this->default, CUSTOMTREE);
        }

        define("Skycel\CustomTree\TEMPLATES_DIR", $this->default["templates"]);
        define("Skycel\CustomTree\STYLESHEETS_DIR", $this->default["stylesheets"]);

        $this->init();
    }

    /**
     * Initializes custom directories, required files, and sets up template and component loaders.
     *
     * @return void
     */
    protected function init(): void
    {
        $this->create_custom_directories();
        $this->create_required_files();

        TemplateLoader::init();

        $template_types = get_default_block_template_types();

        foreach ($template_types as $type => $opt) {
            add_filter("{$type}_template", [TemplateLoader::class, 'getTemplates'], 10, 3);
        }

        // Component loader
        $default_components = [
            "header", "footer",
            "sidebar", "search_form",
            "topbar"
        ];
        foreach ($default_components as $component) {
            add_filter("get_$component", [TemplateLoader::class, 'getComponents'], 10, 2);
        }
    }

    /**
     * Creates custom directories for templates and stylesheets, including handling subdirectories and optional .gitkeep files.
     *
     * @return void
     */
    private function create_custom_directories(): void {
        // Make all directories for templates

        $this->templates_directory = $this->create_directory(TEMPLATES_DIR);

        if (!defined("TEMPLATES_SUBDIRS")) {
            define("TEMPLATES_SUBDIRS", $this->default["templates_subdirs"]);
        }
        foreach (TEMPLATES_SUBDIRS as $subdir) {
            $path = $this->theme_directory . "/" . TEMPLATES_DIR . "/" . $subdir;
            if(is_dir($path) && is_dir_empty($path) && defined("USE_GITKEEP") && USE_GITKEEP) {
                file_put_contents($path . "/.gitkeep", "");
            } else if (!USE_GITKEEP && file_exists($path . "/.gitkeep")) {
                unlink($path . "/.gitkeep");
            }
            $this->create_directory(TEMPLATES_DIR . "/" . $subdir);
        }

        // Make all directories for stylesheets
        $this->stylesheets_directory = $this->create_directory(STYLESHEETS_DIR);

        if (!defined("STYLESHEETS_SUBDIRS")) {
            define("STYLESHEETS_SUBDIRS", $this->default["stylesheets_subdirs"]);
        }
        foreach (STYLESHEETS_SUBDIRS as $subdir) {
            $this->create_directory(STYLESHEETS_DIR . "/" . $subdir);
        }
    }

    /**
     * Creates a specified directory within the theme directory. If the directory does
     * not exist, it creates it and optionally adds a .gitkeep file if enabled.
     *
     * @param string $dir_name The name of the directory to be created.
     * @return string|Error The path to the created directory or an error object if the creation fails.
     */
    private function create_directory($dir_name): string|Error {
        if (!file_exists($this->theme_directory . "/" . $dir_name)) {
            $success = mkdir($this->theme_directory . "/" . $dir_name);

            if ($success) {
                if (defined("USE_GITKEEP") && USE_GITKEEP) {
                    file_put_contents($this->theme_directory . "/" . $dir_name . "/" . ".gitkeep", "");
                }
                return $this->theme_directory . "/" . $dir_name;
            } else {
                return new Error("Could not create the directory '" . $dir_name);
            }
        }

        return $this->theme_directory . "/" . $dir_name;
    }

    /**
     * Creates required template and stylesheet files if they do not already exist.
     *
     * @return void
     */
    private function create_required_files(): void {
        // Create Wordpress required files
        if (!file_exists($this->theme_directory . "/index.php")) file_put_contents($this->theme_directory . "/index.php", "<?php\n/**\n * This is a required file for WordPress themes\n */\n\n// Silence is golden.");
        if (!file_exists($this->theme_directory . "/header.php")) file_put_contents($this->theme_directory . "/header.php", "<?php\n/**\n * This is a required file for WordPress themes\n */\n\n// Silence is golden.");
        if (!file_exists($this->theme_directory . "/footer.php")) file_put_contents($this->theme_directory . "/footer.php", "<?php\n/**\n * This is a required file for WordPress themes\n */\n\n// Silence is golden.");

        if (!file_exists($this->theme_directory . "/" . STYLESHEETS_DIR . "/style.css")) file_put_contents($this->theme_directory . "/" . STYLESHEETS_DIR . "/css/style.css", "");

        // Create CustomThemeTree required files
        if (!file_exists($this->theme_directory . "/" . TEMPLATES_DIR . "/index.php")) file_put_contents($this->theme_directory . "/" . TEMPLATES_DIR . "/index.php", "<?php\n\necho '<h1 style=\'text-align:center;\'>New Files tree is operational !</h1>';");
        if (!file_exists($this->theme_directory . "/" . TEMPLATES_DIR . "/components/header.php")) file_put_contents($this->theme_directory . "/" . TEMPLATES_DIR . "/components/header.php", "<?php\n\n");
        if (!file_exists($this->theme_directory . "/" . TEMPLATES_DIR . "/components/footer.php")) file_put_contents($this->theme_directory . "/" . TEMPLATES_DIR . "/components/footer.php", "<?php\n\n");

        if (!file_exists($this->theme_directory . "/" . STYLESHEETS_DIR . "/style.css")) file_put_contents($this->theme_directory . "/" . STYLESHEETS_DIR . "/css/style.css", "");
    }
}