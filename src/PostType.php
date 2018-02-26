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
 * Description of PostType
 *
 * @author DJ Walker <donwalker1987@gmail.com>
 */
class PostType {

    private static $registerOptions = array(//all the options you can pass into register_post_type minus the ones already being used
        'description',
        'public',
        'exclude_from_search',
        'publicly_queryable',
        'show_ui',
        'show_in_nav_menus',
        'show_in_menu',
        'show_in_admin_bar',
        'menu_position',
        'menu_icon',
        'capability_type',
        'capabilities',
        'map_meta_cap',
        'hierarchical',
        'supports',
        'has_archive',
        'permalink_epmask',
        'rewrite',
        'query_var',
        'can_export',
        'show_in_rest',
        'rest_base',
        'rest_controller_class',
    );
    private $configData;
    private $templateHandler;
    private $metaboxes = array();
    private $listing_fields = array();

    public static function createFromConfig($configData, $templateHandler) {
        if (property_exists($configData, 'taxonomies')) {
            foreach ($configData->taxonomies as $taxonomyData) {
                PostTaxonomy::createFromConfig($configData->name, $taxonomyData, $templateHandler);
            }
        }
        return new static($configData, $templateHandler);
    }

    public function __construct($configData, $templateHandler) {
        if (property_exists($configData, 'active') && $configData->active !== true) {
            return;
        }
        $this->configData = $configData;
        $this->templateHandler = $templateHandler;

        add_action('init', array($this, 'register'));
        add_action('save_post', array($this, 'save'));
        add_action("manage_edit-{$this->configData->name}_columns", array($this, 'columns'));
        add_action("manage_{$this->configData->name}_posts_custom_column", array($this, 'columns_content'));
    }

    public function register() {
        if (!post_type_exists($this->configData->name)) {
            $args = self::generatePostArgs($this->configData);
            $args['register_meta_box_cb'] = array($this, 'addMetaBoxes');

            register_post_type($this->configData->name, $args);
        } else {
            add_action('add_meta_boxes_' . $this->configData->name, array($this, 'addMetaBoxes'));
        }
        $this->setupMetaBoxes();
    }

    /**
     * take out of constructor so it doesn't happen every page load
     */
    private function setupMetaBoxes() {
        if (property_exists($this->configData, 'metaboxes')) {
            foreach ($this->configData->metaboxes as $boxData) {
                if (property_exists($boxData, 'repeating') && $boxData->repeating == true) {
                    $ajaxAction = 'save_repeater_' . $this->configData->name . '_' . $boxData->name;
                    $this->metaboxes[] = new RepeatingMetaBox($boxData, $this->templateHandler, $ajaxAction);
                } else {
                    $this->metaboxes[] = new MetaBox($boxData, $this->templateHandler);
                }
            }
        }
    }

    public function addMetaBoxes() {

        foreach ($this->metaboxes as $i => $metabox) {
            add_meta_box($this->configData->name . '_' . sanitize_title_with_dashes($metabox->getTitle()), $metabox->getTitle(), array($metabox, 'display'), $metabox->getScreen(), $metabox->getContext(), $metabox->getPriority());
        }
    }

    public function save($post_id) {
        if (isset($_POST['_inline_edit'])) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }
        if (get_post_type($post_id) !== $this->configData->name) {
            return $post_id;
        }
        foreach ($this->metaboxes as $metabox) {
            $metabox->save($post_id);
        }
    }

    private static function generatePostArgs($data) {
        $args = array();

        if (!property_exists($data, 'labels')) {
            $data->labels = new \stdClass();
        }
        if (!property_exists($data->labels, 'singular') || !$data->labels->singular) {
            $data->labels->singular = $data->name;
        }
        if (!property_exists($data->labels, 'plural') || !$data->labels->plural) {
            $data->labels->plural = $data->labels->singular . 's';
        }

        $args['labels'] = self::generateLabelsArray($data->labels);

        foreach (self::$registerOptions as $key) {
            if (property_exists($data, $key)) {
                $args[$key] = $data->$key;
            }
        }

        if (property_exists($data, 'core_taxonomies')) {
            $args['taxonomies'] = $data->core_taxonomies;
        }

        // deep-convert object to array
        return json_decode(json_encode($args), true);
    }

    private static function generateLabelsArray($labels) {
        return array(
            'name' => $labels->plural,
            'singular_name' => $labels->singular,
            'menu_name' => (!empty($labels->menu_name) ? $labels->menu_name : $labels->plural),
            'new_admin_bar' => $labels->singular,
            'add_new' => 'Add New ' . $labels->singular,
            'add_new_item' => 'Add ' . $labels->singular,
            'new_item' => $labels->singular,
            'edit_item' => $labels->singular,
            'view_item' => 'View ' . $labels->singular,
            'all_items' => 'All ' . $labels->plural,
            'search_item' => 'Search ' . $labels->singular,
            'parent_item_colon' => 'Parent ' . $labels->singular . ':',
            'not_found' => 'No ' . $labels->plural . ' found',
            'not_found_in_trash' => 'No ' . $labels->plural . ' found in trash',
        );
    }

    /**
     * Adds custom columns to the post type defined in the config
     *
     * @param array $columns
     * @return array
     */
    public function columns($columns) {
        foreach ($this->metaboxes as $i => $metabox) {
            foreach ($metabox->getFields() as $field) {
                if ($field->getConfigData('listing')) {
                    $columns[$field->getConfigData('name')] = $field->getConfigData('label');
                    $this->listing_fields[] = $field;
                }
            }
        }
        return $columns;
    }

    /**
     * Displays the content for a custom column
     *
     * @param string $name
     * @return void
     */
    public function columns_content($name) {
        foreach ($this->listing_fields as $field) {
            if ($name == $field->getConfigData('name')) {
                echo $field->getListingContent($field->getConfigData('listing_options', array()));
                return;
            }
        }
        echo '';
    }

}
