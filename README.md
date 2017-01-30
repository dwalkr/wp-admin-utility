# WordPress Admin Page Utility

**wp-admin-utility** installs as a plugin and provides an API for quickly setting up custom post types and options pages. Whereas other post type builders provide an admin interface for creating post types, this utility reads static configuration files to create post types via a declarative syntax.

## Installation
r 
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
$pageCreator->addPostType($config, \dwalkr\WPAdminUtility::FROM_ARRAY);

```

## Configuration API

### Adding Custom Meta Boxes to Existing Post Types



