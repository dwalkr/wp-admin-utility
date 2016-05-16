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
 * Description of Field
 *
 * @author DJ
 */
abstract class Field {

    protected $configData;
    protected $templateHandler;

    abstract function render();

    public function __construct($data, $templateHandler) {
        $this->configData = $data;
        $this->templateHandler = $templateHandler;
        if (method_exists($this, 'init')) {
            $this->init();
        }
    }

    public static function create($fieldData, $templateHandler) {
        switch ($fieldData->type) {
            case 'text':
            default:
                return new Field\Text($fieldData, $templateHandler);
            case 'textarea':
                return new Field\Textarea($fieldData, $templateHandler);
            case 'select':
                return new Field\Select($fieldData, $templateHandler);
            case 'date':
                return new Field\Date($fieldData, $templateHandler);
            case 'post':
                $fieldData->options = self::getPostRelOptions($fieldData);
                return new Field\Select($fieldData, $templateHandler);
            case 'rel':
                $fieldData->options = self::getRelOptions($fieldData);
                return new Field\Select($fieldData, $templateHandler);
            case 'class':
                $className = '\\'.$fieldData->class;
                return new $className($fieldData, $templateHandler);
            case 'attachment':
                return new Field\Attachment($fieldData, $templateHandler);
            case 'editor':
            case 'wysiwyg':
                return new Field\Editor($fieldData, $templateHandler);
        }
    }

    /**
     * return array of id=>title pairs
     * @param type $fieldData
     */
    public static function getPostRelOptions($fieldData) {
        global $wpdb;
        $from = "SELECT p.ID,p.post_title FROM $wpdb->posts p";
        $where = '';
        $numJoins = 0;
        foreach ($fieldData->filter as $filter) {
            if (!$filter->compare) {
                $filter->compare = '=';
            }
            if ($filter->meta_key) {
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

        $results = $wpdb->get_results("$from WHERE $where");
        $return = array();
        foreach ($results as $result) {
            $option = new \stdClass();
            $option->value = $result->ID;
            $option->label = $result->post_title;
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
        return get_post_meta(get_the_ID(), $this->getKey(), true);
    }

    public function getKey() {
        return $this->getConfigData('name');
    }

    protected function getConfigData($key = false, $default = null) {
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

    public function prepareData($data, $post_id) {
        return $data;
    }

}
