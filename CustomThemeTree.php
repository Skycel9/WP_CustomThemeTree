<?php

namespace Skycel\CustomTree;

use WP_Theme;
use Error;

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
            "errors"=> "errors",
            "pages"=> "pages",
            "search"=> "search",
            "singles"=> "singles",
            "tags"=> "tags",
            "taxonomies"=> "taxonomies"
        ],
        "stylesheets_subdirs"=> [
            "css"=> "css"
        ]
    );

    public function __construct($dirs = null, $config_file = "theme.php") {

        wp_set_template_globals();

        if ($dirs) {
            $this->default = array_merge_recursive_distinct($this->default, $dirs);
        }

        $this->theme = wp_get_theme();
        $this->theme_directory = $this->theme->get_theme_root() . "/" . get_template();
        $this->config_directory = "config";

        if (!file_exists($this->theme_directory . "/" . $this->config_directory . "/" . $config_file)) {
            if (!is_dir($this->theme_directory . "/config" )) {
                $this->config_directory = $this->create_directory("config");
            }
            file_put_contents($this->theme_directory . "/" . $this->config_directory . "/theme.php", "<?php\n\nconst TEMPLATES_DIR = 'templates';\nconst STYLESHEETS_DIR = 'assets';\n");
        }
        require_once $this->theme_directory . "/" . $this->config_directory . "/" . $config_file;

        $this->init();
    }

    protected function init(): void
    {
        $this->create_custom_directories();
        $this->create_required_files();

        TemplateLoader::init();

        add_filter("index_template", [TemplateLoader::class, "getTemplate"], 100, 3);
        add_filter("single_template", [TemplateLoader::class, "getTemplates"], 100, 3);
        add_filter("404_template", [TemplateLoader::class, "getTemplate"], 100, 3);

        add_filter("frontpage_template", [TemplateLoader::class, "getTemplates"], 100, 3);
        add_filter("home_template", [TemplateLoader::class, "getTemplates"], 100, 3);
        add_filter("page_template", [TemplateLoader::class, "getTemplates"], 100, 3);

        add_filter("get_header", [TemplateLoader::class, "getComponents"], 100, 2);
        add_filter("get_footer", [TemplateLoader::class, "getComponents"], 100, 2);
        add_filter("get_sidebar", [TemplateLoader::class, "getComponents"], 100, 2);
        add_filter("get_topbar", [TemplateLoader::class, "getComponents"], 100, 2);

        add_filter("archive_template", [TemplateLoader::class, "getTemplates"], 100, 3);

        add_filter("tag_template", [TemplateLoader::class, "getTags"], 100, 3);

        add_filter("category_template", [TemplateLoader::class, "getTemplates"], 100, 3);
        add_filter("taxonomy_template", [TemplateLoader::class, "getTemplates"], 100, 3);
    }

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

    private function create_required_files(): void {
//        if (!file_exists($this->theme_directory . "/" . TEMPLATES_DIR . "/index.php")) file_put_contents($this->theme_directory . "/" . TEMPLATES_DIR . "/index.php", "<?php\n\necho '<h1 style=\'text-align:center;\'>New Files tree is operational !</h1>';");
        if (!file_exists($this->theme_directory . "/" . TEMPLATES_DIR . "/components/header.php")) file_put_contents($this->theme_directory . "/" . TEMPLATES_DIR . "/components/header.php", "<?php\n\n");
        if (!file_exists($this->theme_directory . "/" . TEMPLATES_DIR . "/components/footer.php")) file_put_contents($this->theme_directory . "/" . TEMPLATES_DIR . "/components/footer.php", "<?php\n\n");

        if (!file_exists($this->theme_directory . "/" . STYLESHEETS_DIR . "/style.css")) file_put_contents($this->theme_directory . "/" . STYLESHEETS_DIR . "/css/style.css", "");
    }

    public function custom_template_loader($name): void {
        var_dump($name);
    }
}