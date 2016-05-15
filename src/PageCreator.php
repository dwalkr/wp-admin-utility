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
 * Description of PageCreator
 *
 * @author DJ
 */
class PageCreator {

    const FROM_FILE = 1;
    const FROM_ARRAY = 2;
    const FROM_OBJECT = 3;

    private $templateHandler;

    public function __construct($templateHandler) {
        $this->templateHandler = $templateHandler;
    }

    private static function parseFile($file) {
        if (!file_exists($file)) {
            throw new \InvalidArgumentException("File $file not found");
        }
        $extension = pathinfo($file, PATHINFO_EXTENSION);

        switch ($extension) {
            case 'php':
                return self::getConfigObject(require($file), self::FROM_ARRAY);

            case 'json':
                return json_decode(file_get_contents($file));

            case 'yaml':
            case 'yml':
                //TODO: cache parsed YAML data as this parse is less performant than native php
                $parser = new \Symfony\Component\Yaml\Parser();
                return $parser->parse(file_get_contents($file), true, true, true);
        }
    }

    /**
     * return $config object based on method
     * @param type $config
     * @param type $method
     */
    private static function getConfigObject($config, $method = self::FROM_FILE) {
        switch ($method) {
           case self::FROM_FILE:
           default:
               return self::parseFile($config);
           case self::FROM_ARRAY:
               return json_decode(json_encode($config));
           case self::FROM_OBJECT:
               return $config;
       }
    }

    /**
     * Registers and builds admin interface for a custom post type.
     * @param type $config
     * @param type $method
     */
    public function addPostType($config, $method = self::FROM_FILE) {
        $configData = self::getConfigObject($config, $method);
        PostType::createFromConfig($configData, $this->templateHandler);
    }

    /**
     * creates and builds admin interface for a settings page.
     * @param type $config
     * @param type $method
     */
    public function addSettingsPage($config, $method = self::FROM_FILE) {
        $configData = self::getConfigObject($config, $method);
        SettingsPage::createFromConfig($configData, $this->templateHandler);
    }

    /**
     * creates and builds admin interface for custom table data.
     * currently only supports flat tables but is extensible via custom field types.
     * @param type $config
     * @param type $method
     */
    public function addTableEntitiy($config, $method = self::FROM_FILE) {
        $configData = self::getConfigObject($config, $method);
        TableEntity::createFromConfig($configData, $this->templateHandler);
    }

}
