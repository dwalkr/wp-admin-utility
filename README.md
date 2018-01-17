# WordPress Admin Page Utility

**wp-admin-utility** installs as a plugin and provides an API for quickly setting up custom post types and options pages. Whereas other post type builders provide an admin interface for creating post types, this utility reads static configuration files to create post types via a declarative syntax.

## Installation

```
composer require dwalkr/wp-admin-utility
```

## Basic Usage

The `PageCreator` singleton is used to parse configuration files.  It is made available on a custom hook named `adminutility-pagecreator-init`. The hook is triggered on `after_setup_theme`, so just be sure you add your callback before that event (plugins can use `plugins_loaded` and themes can just put their code directly in `functions.php`.)

Use the `addPostType` method to create post types, and `addSettingsPage` to create options pages.

```php
<?php

add_action('adminutility-pagecreator-init', function($pageCreator){
  $pageCreator->addPostType('/path/to/config.yml');
});

```

### Configuration Formats

Configuration files can be in **yaml**, **json**, or **php** format. When passing a PHP file to PageCreator, the file should return the configuration as an associative array.

### Direct Array/Object Config

Instead of having PageCreator parse a configuration file, you can pass an array or PHP object containing the configuration instead of the path to a file. Use the FROM_ARRAY and FROM_OBJECT constants to tell PageCreator how to process the first method parameter.

```php
<?php

$config = [
  'active' => true,
  'name' => 'specials',
  'public' => true
  //...
];
$pageCreator->addPostType($config, \dwalkr\WPAdminUtility\PageCreator::FROM_ARRAY);

```

## Configuration API

Almost all options that can be passed into the [register_post_type](https://codex.wordpress.org/Function_Reference/register_post_type) function can be added to the top-level config for a post type.

To understand how to set up meta boxes with custom fields, see the [post type example](examples/post_type.yml).

To understand how to set up settings pages, see the [settings page example](examples/settings_page.yml).

### Adding Custom Meta Boxes to Existing Post Types

To add a custom fields and meta boxes to an existing post type, just add a configuration file with the `name` property matching the existing post type. If the post type has already been registered, the rest of the top-level configuration is ignored and only `metaboxes` are added.
