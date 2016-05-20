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

namespace dwalkr\WPAdminUtility\Field;

use dwalkr\WPAdminUtility\Field;

/**
 * Save ID and file path together
 *
 * @author DJ
 */
class Attachment extends Field {

    public function render() {
        require $this->templateHandler->getView('field/wrapper-start');
        require $this->templateHandler->getView('field/attachment');
        require $this->templateHandler->getView('field/wrapper-end');
    }

    public static function isImage($filename) {
        $image_extensions = array(
            'jpg',
            'jpeg',
            'png',
            'gif',
            'svg'
        );
        $filenameparts = explode('.',$filename);
        $extension = end($filenameparts);
        return in_array(strtolower($extension), $image_extensions);
    }

    public function getAttachmentId() {
        $data = $this->getFieldValue();
        if (is_array($data)) {
            return $data['id'];
        }
        return null;
    }

    public function getFileUrl() {
        $data = $this->getFieldValue();
        if (is_array($data)) {
            return $data['url'];
        }
        return $data;
    }

    public function prepareData($data) {
        if ($this->getConfigData('include_id', false)) {
            return array(
                'url' => $data,
                'id' => $_POST[$this->getKey().'_id'],
            );
        }
        return $data;
    }

}
