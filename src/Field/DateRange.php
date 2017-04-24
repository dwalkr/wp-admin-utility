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
 * Description of Text
 *
 * @author DJ
 */
class DateRange extends Field {
    
    protected $defaultSaveFormat = 'Y-m-d';
    protected $displayFormat = 'F j, Y';

    public function render() {
        require $this->templateHandler->getView('field/wrapper-start');
        require $this->templateHandler->getView('field/daterange');
        require $this->templateHandler->getView('field/wrapper-end');
    }
    
    public function getFormat() {
        return property_exists($this->configData, 'format') ? $this->configData->format : $this->defaultSaveFormat;
    }
    
    public function formatForUI($date) {
        if (!$date) {
            return;
        }
        return \DateTime::createFromFormat($this->getFormat(), $date)->format($this->displayFormat);
    }

    public function getStartDate() {
        $data = $this->getFieldValue();
        return is_array($data) ? $this->formatForUI($data['start']) : '';
    }

    public function getEndDate() {
        $data = $this->getFieldValue();
        return is_array($data) ? $this->formatForUI($data['end']) : '';
    }
    
    public function prepareData($data) {
        if ($data['start']) {
            $data['start'] = date($this->getFormat(), strtotime($data['start']));
        }
        if ($data['end']) {
            $data['end'] = date($this->getFormat(), strtotime($data['end']));
        }
        return $data;
    }

}
