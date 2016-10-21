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
 * Description of MetaBox
 *
 * @author DJ
 */
class MetaBox {

    protected $configData;
    protected $fields = array();
    protected $templateHandler;

    public function __construct($data, $templateHandler) {
        $this->configData = $data;
        $this->templateHandler = $templateHandler;

        foreach ($this->configData->fields as $fieldData) {
            if (array_key_exists('post',$_GET)) {
                $post_id = $_GET['post'];
                $data = get_post_meta($post_id, $fieldData->name, true);
            } else {
                $data = false;
            }

            if ($data === false && property_exists($fieldData, 'default')) {
                $data = $fieldData->default;
            }
            $this->fields[] = Field::create($fieldData, $this->templateHandler, $data);
        }
    }

    public function getTitle() {
        return $this->configData->title;
    }

    public function getScreen() {
        if (property_exists($this->configData, 'screen')) {
            return $this->configData->screen;
        }
        return null;
    }

    public function getContext() {
        if (property_exists($this->configData, 'context')) {
            return $this->configData->context;
        }
        return 'normal';
    }

    public function getPriority() {
        if (property_exists($this->configData, 'priority')) {
            return $this->configData->screen;
        }
        return 'default';
    }

    public function display() {
        require $this->templateHandler->getView('metabox/start');
        foreach ($this->fields as $field) {
            $field->render();
        }
        require $this->templateHandler->getView('metabox/end');
    }

    public function save($post_id) {

        if (!$post_id) return;
        foreach ($this->fields as $field) {
            if(!array_key_exists($field->getKey(), $_POST)) {
                continue;
            }
            $data = $_POST[$field->getKey()];
            if (method_exists($field, 'save')) {
                $field->save($data); //for custom junk
            } else {
                $data = $field->prepareData($data, $post_id);
                update_post_meta($post_id, $field->getKey(), $data);
            }
        }
    }
}
