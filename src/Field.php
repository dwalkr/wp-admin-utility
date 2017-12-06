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
 *
 *  todo: right now getFieldValue() returns a get_post_meta call which only applies to post types.
 * need a way to pass contexts into fields or something. maybe send them their data. idk.
 * but fields need to be usable in non-post type contexts. they should just display UI and possibly validate/prepare data
 *
 * @author DJ
 */
abstract class Field {

    protected $configData;
    protected $templateHandler;
    protected $data;

    abstract function render();

    public function __construct($configData, $templateHandler, $data) {
        $this->configData = $configData;
        $this->templateHandler = $templateHandler;
        $this->data = $data;
        if (method_exists($this, 'init')) {
            $this->init();
        }
    }

    public static function create($fieldConfig, $templateHandler, $data) {

        if (is_array($fieldConfig)) {
            $fieldConfig = json_decode(json_encode($fieldConfig));
        }

        switch ($fieldConfig->type) {
            case 'text':
            default:
                return new Field\Text($fieldConfig, $templateHandler, $data);
            case 'textarea':
                return new Field\Textarea($fieldConfig, $templateHandler, $data);
            case 'select':
                return new Field\Select($fieldConfig, $templateHandler, $data);
            case 'date':
                return new Field\Date($fieldConfig, $templateHandler, $data);
            case 'daterange':
                return new Field\DateRange($fieldConfig, $templateHandler, $data);
            case 'datetime':
                return new Field\DateTime($fieldConfig, $templateHandler, $data);
            case 'time':
                return new Field\Time($fieldConfig, $templateHandler, $data);
            case 'post':
                $fieldConfig->options = self::getPostRelOptions($fieldConfig);
                return new Field\Select($fieldConfig, $templateHandler, $data);
            case 'rel':
                $fieldConfig->options = self::getRelOptions($fieldConfig);
                return new Field\Select($fieldConfig, $templateHandler, $data);
            case 'class':
                $className = '\\'.$fieldConfig->class;
                return new $className($fieldConfig, $templateHandler, $data);
            case 'attachment':
                return new Field\Attachment($fieldConfig, $templateHandler, $data);
            case 'editor':
            case 'wysiwyg':
                return new Field\Editor($fieldConfig, $templateHandler, $data);
            case 'checkbox':
                return new Field\Checkbox($fieldConfig, $templateHandler, $data);
			case 'password':
				return new Field\Password($fieldConfig, $templateHandler, $data);
        }
    }

    /**
     * return array of id=>title pairs
     * @param type $fieldData
     */
    public static function getPostRelOptions($fieldData) {
        global $wpdb;
        $from = "SELECT p.ID,p.post_title,p.post_status FROM $wpdb->posts p";
        $where = "p.post_status IN ('publish','draft')";
        $numJoins = 0;
        if (property_exists($fieldData, 'filters') && is_array($fieldData->filters)) {
            foreach ($fieldData->filters as $filter) {
                if (!property_exists($filter,'compare')) {
                    $filter->compare = '=';
                }
                if (property_exists($filter, 'meta_key')) {
                    $numJoins++;
                    $join = 'pm'.$numJoins;
                    $from .= " INNER JOIN $wpdb->postmeta $join ON (p.ID = $join.post_id AND $join.meta_key = '".esc_sql($filter->meta_key) . "')";
                    if ($where) {
                        $where .= ' AND ';
                    }
                    $where .= "$join.meta_key " . esc_sql($filter->compare) . " '" . esc_sql($filter->value)."'";
                } else {
                    if ($where) {
                        $where .= ' AND ';
                    }
                    $where .= esc_sql($filter->column) . ' ' . esc_sql($filter->compare) . " '" . esc_sql($filter->value) . "'";
                }
            }
        }

        $results = $wpdb->get_results("$from WHERE $where");
        $return = array();
        foreach ($results as $result) {
            $option = new \stdClass();
            $option->value = $result->ID;
            $option->label = $result->post_title;
            if ($result->post_status === 'draft') {
                $option->label .= ' [DRAFT]';
            }
            $return[] = $option;
        }
        return $return;
    }

    /**
     * return array of id=>value pairs
     * @param type $fieldData
     */
    public static function getRelOptions($fieldData) {
        //implementation forthcoming
    }

    public function getFieldValue() {
        if (isset($this->data)) {
            return $this->data;
        }
    }

    public function getKey() {
        return $this->getConfigData('name');
    }

    public function getConfigData($key = false, $default = null) {
        if (!$key) {
            return $this->configData;
        }
        else {
            $keys = explode('/', $key);
            $returnData = $this->configData;
            foreach ($keys as $key) {
                if (property_exists($returnData, $key)) {
                    $returnData = $returnData->$key;
                } else {
                    return $default;
                }
            }
            return $returnData;
        }
    }

    public function prepareData($data) {
        return $data;
    }
	
	public function getListingContent($options = array()){
		global $post;
		return get_post_meta($post->ID, $this->getConfigData('name'), true);
	}
	
}
