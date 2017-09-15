<?php

/**
 * Plugin Name: WordPress Admin Utility
 * Plugin URI:
 * Description: Collection of Utilities to enhance wordpress admin and rapidly create pages
 * Version: 0.3.1
 * Author: DJ Walker
 */

/*
 * The MIT License
 *
 * Copyright 2016 DJ Walker <donwalker1987@gmail.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */


namespace dwalkr\WPAdminUtility;

/**
 * psr-4 autoloader
 * http://www.php-fig.org/psr/psr-4/
 */
spl_autoload_register(function($class) {
    $prefix = __NAMESPACE__;
    $base_dir = dirname(__FILE__) . '/src';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

register_activation_hook(__FILE__, function() {
    Plugin::activate();
});
add_action('plugins_loaded', function() {
    Plugin::register(plugin_dir_path(__FILE__), plugin_dir_url(__FILE__));
});
