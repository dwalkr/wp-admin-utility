<?php

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
 * Description of Plugin
 *
 * @author DJ
 */
class Plugin {

    private static $registered = false;
    private $basePath;
    private $baseUrl;
    private $templateLoader;
    private $pageCreator;

    /**
     * instantiate singleton and run init
     */
    public static function register($base_path, $base_url) {
        if (!self::$registered) {
            self::$registered = true;
            return new static($base_path, $base_url);
        }
        throw new \RuntimeException(__CLASS__ . 'already initialized');
    }

    public static function activate() {

    }

    private function __construct($base_path, $base_url) {
        $this->basePath = $base_path;
        $this->baseUrl = $base_url;
        $this->templateHandler = new TemplateHandler($this->basePath . '/view', 'wp-admin-utility');
        $this->pageCreator = new PageCreator($this->templateHandler);


        add_action('admin_enqueue_scripts', array($this, 'enqueueAssets'));

        add_action('after_setup_theme', array($this, 'runPageCreator')); //this will catch hooks created in theme functions.php but before 'init'
    }

    public function registerAssets() {
        wp_register_style('semantic-ui-css', $this->baseUrl . '/asset/semantic-ui/semantic.mdehoog.min.css');
        wp_register_style('wp-admin-utility/base-css', $this->baseUrl . '/asset/css/main.css');
        wp_register_script('semantic-ui-js', $this->baseUrl . '/asset/semantic-ui/semantic.mdehoog.min.js');
        wp_register_script('wp-admin-utility/base-js', $this->baseUrl . '/asset/js/main.js', array('jquery', 'semantic-ui-js'));

        //wp_register_style('semantic-ui-calendar-css', $this->baseUrl . '/asset/semantic-ui/components/calendar.min.css', array('semantic-ui-css'));
        //wp_register_script('semantic-ui-calendar-js', $this->baseUrl . '/asset/semantic-ui/components/calendar.js', array('jquery','semantic-ui-js'));

        wp_register_script('wp-admin-utility/attachment-js', $this->baseUrl . '/asset/js/attachment.js', array('jquery'), true);
        wp_register_script('wp-admin-utility/repeater-js', $this->baseUrl . '/asset/js/repeater.js', array('jquery'), true);
    }

    public function enqueueAssets() {
        global $pagenow;
        if (!in_array($pagenow, array('post.php', 'post-new.php', 'admin.php', 'edit.php')) || ($pagenow == 'edit.php' && !isset($_GET['page']))) {
            return;
        }
        $this->registerAssets();
        wp_enqueue_media();
        wp_enqueue_style('wp-admin-utility/base-css');
        wp_enqueue_style('semantic-ui-css');
        wp_enqueue_script('wp-admin-utility/base-js');
        wp_enqueue_script('semantic-ui-js');
        wp_enqueue_script('wp-admin-utility/attachment-js');
        wp_enqueue_style('semantic-ui-calendar-css');
        wp_enqueue_script('semantic-ui-calendar-js');
        wp_enqueue_script('wp-admin-utility/repeater-js');
    }

    public function runPageCreator() {
        //expose pageCreator to userland code
        do_action('adminutility-pagecreator-init', $this->pageCreator);
    }

}
