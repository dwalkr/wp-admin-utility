<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace dwalkr\WPAdminUtility;

/**
 * Description of PostTaxonomy
 *
 * @author DJ Walker <donwalker1987@gmail.com>
 */
class PostTaxonomy {

    private static $registerOptions = array(
        'public',
        'show_ui',
        'show_in_menu',
        'show_in_nav_menus',
        'show_tagcloud',
        'show_in_quick_edit',
        'meta_box_cb',
        'show_admin_column',
        'description',
        'hierarchical',
        'update_count_callback',
        'query_var',
        'rewrite',
        'capabilities',
        'sort',
    );

    private $objectType;
    private $configData;
    private $templateHandler;

    public static function createFromConfig($objectType, $configData, $templateHandler) {
        return new static($objectType, $configData, $templateHandler);
    }

    public function __construct($objectType, $configData, $templateHandler) {
        $this->objectType = $objectType;
        $this->configData = $configData;
        $this->templateHandler = $templateHandler;
        add_action('init', array($this, 'register'));
    }

    public function register() {
        $args = self::generatePostArgs($this->configData);
        
        register_taxonomy($this->configData->name, $this->objectType, $args);
        register_taxonomy_for_object_type($this->configData->name, $this->objectType);
    }

    private static function generatePostArgs($data) {
        $args = array();

        if (!$data->labels->singular) {
            $data->labels->singular = $data->name;
        }
        if (!$data->labels->plural) {
            $data->labels->plural = $data->labels->singular . 's';
        }

        $args['labels'] = self::generateLabelsArray($data->labels);

        foreach (self::$registerOptions as $key) {
            if (property_exists($data, $key)) {
                $args[$key] = $data->$key;
            }
        }

        return $args;
    }

    private static function generateLabelsArray($labels) {
        return array(
            'name' => $labels->plural,
            'singular_name' => $labels->singular,
            'menu_name' => $labels->plural,
            'all_items' => 'All '.$labels->plural,
            'edit_item' => $labels->singular,
            'view_item' => 'View '.$labels->singular,
            'update_item' => 'Update '.$labels->singular,
            'add_new_item' => 'Add '.$labels->singular,
            'new_item_name' => 'New '.$labels->singular . ' Name',
            'parent_item' => 'Parent ' .$labels->singular,
            'parent_item_colon' => 'Parent ' .$labels->singular . ':',
            'search_item' => 'Search ' . $labels->singular,
            'popular_items' => 'Popular '.$labels->plural,
            'separate_items_with_commas' => 'Separate ' . $labels->plural . ' with commas',
            'add_or_remove_items' => 'Add or remove '.$labels->plural,
            'choose_from_most_used' => 'Choose from most used ' . $labels->plural,
            'not_found' => 'No '.$labels->plural . ' found',
        );
    }
}
