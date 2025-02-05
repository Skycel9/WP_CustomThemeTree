# WordPress Custom Theme Tree

A WordPress plugin that provides a structured and organized theme development environment with customizable directory structures.

## Overview

This plugin helps WordPress theme developers by implementing a standardized and customizable directory structure. It automatically creates and manages theme directories, handles templates, and provides a clean organization for your theme files.

## Features

- 🗂️ Organized directory structure for templates and assets
- 🔧 Customizable configuration
- 🚀 Automatic creation of required WordPress theme files
- 📁 Support for template subdirectories (archives, pages, components, etc.)
- 🎨 Dedicated assets organization
- 📌 Optional .gitkeep file management

## Directory Structure

By default, the plugin creates the following structure:

```
theme-root/
├── assets/
│   ├── js
│   ├── css
│   └── img
├── config/
│   └── theme.php
├── templates/
│   ├── archives
│   ├── attachments
│   ├── authors
│   ├── categories
│   ├── components
│   ├── dates
│   ├── pages
│   ├── singles
│   ├── tags
│   └── taxonomies
├── footer.php
├── functions.php
├── header.php
├── index.php
└── style.css
```


## Installation

There are three ways to install this plugin:

### 1. Standard Plugin Installation

1. Clone this repository into your WordPress plugins directory:
```bash
cd wp-content/plugins/
git clone <repository-url> custom-theme-tree
```
2. Activate the plugin through the WordPress admin interface

### 2. Must-Use Plugin Installation

1. Create the `mu-plugins` directory if it doesn't exist:
```bash
mkdir -p wp-content/mu-plugins
```

2. Copy or clone the plugin into the mu-plugins directory:
```bash
cd wp-content/mu-plugins/
git clone <repository-url> custom-theme-tree
```

3. Create a loader file named `custom-theme-tree.php` in the mu-plugins root:
```php
<?php
/**
 * Load Custom Theme Tree Must-Use Plugin
 */
require_once __DIR__ . '/custom-theme-tree/CustomThemeTree.php';
```

The plugin will be automatically activated as a Must-Use plugin.

### 3. Theme Integration

1. Create a `plugins` or `includes` directory in your theme:
```bash
mkdir -p wp-content/themes/your-theme/includes
```

2. Copy or clone the plugin into your theme directory:
```bash
cd wp-content/themes/your-theme/includes
git clone <repository-url> custom-theme-tree
```

3. Add the following code to your theme's `functions.php`:
```php
<?php
// Load Custom Theme Tree
require_once get_template_directory() . '/includes/custom-theme-tree/CustomThemeTree.php';

// Initialize the Custom Theme Tree
new CustomThemeTree();
```

Choose the installation method that best suits your needs:
- Standard plugin: For regular themes and easy activation/deactivation
- Must-Use plugin: For required functionality across all sites in your installation
- Theme integration: For theme-specific implementation and distribution

## Configuration

Create or modify the `theme.php` file in your theme's `config` directory:

```php
<?php

const USE_GITKEEP = true;
const USE_CUSTOMTREE_PLUGIN = true;
const CUSTOMTREE = array();
```

### Configuration Options

- `USE_GITKEEP`: Enable/disable .gitkeep files in empty directories
- `USE_CUSTOMTREE_PLUGIN`: Enable/disable the plugin functionality
- `CUSTOMTREE`: Array for custom directory structure configuration

## Usage

The plugin works automatically after activation. It will:

1. Create the necessary directory structure
2. Set up required WordPress theme files
3. Initialize template loaders for different content types
4. Handle component loading (header, footer, sidebar, etc.)

### Custom Directory Structure

You can customize the directory structure by modifying the `CUSTOMTREE` constant in your `theme.php`:

```php
const CUSTOMTREE = array(
    "templates" => "custom-templates",
    "stylesheets" => "custom-assets",
    "templates_subdirs" => [
        "custom-section" => "custom-section"
    ]
);
```

## Requirements

- WordPress 5.0 or higher
- PHP 8.0 or higher

## License

This project is licensed under the MIT License.