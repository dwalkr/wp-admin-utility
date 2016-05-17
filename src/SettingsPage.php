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
 * Description of SettingsPage
 *
 *
 * @author DJ Walker <donwalker1987@gmail.com>
 */
class SettingsPage {

    private $configData;
    private $templateHandler;
    private $tabs = array();
    private $optionData;

    public static function createFromConfig($configData, $templateHandler) {
        return new static($configData, $templateHandler);
    }

    public function __construct($configData, $templateHandler) {
        $this->configData = $configData;
        $this->templateHandler = $templateHandler;
        $this->optionData = get_option($this->configData->name);
        if (!is_array($this->optionData)) {
            $this->optionData = array();
        }

        foreach ($this->configData->sections as $sectionData) {
            $tab = property_exists($sectionData, 'tab') ? $sectionData->tab : 'default';

            $this->tabs[$tab][] = new FieldSection($sectionData, $this->templateHandler, $this->optionData);
        }

        add_action('admin_menu', array($this, 'addMenuPages'));
        add_action('admin_action_save_'.$this->configData->name, array($this, 'save'));



    }

    public function addMenuPages() {
        $pageTitle = $this->getConfigData('title');
        $menuTitle = $this->getConfigData('menu/title', $this->getConfigData('title'));
        $capability = $this->getConfigData('menu/capability','manage_options');
        $menuSlug = $this->getConfigData('slug');
        $callback = array($this, 'renderPage');

        if ($this->getConfigData('menu/top')) {
            //add top-level page
            $iconUrl = $this->getConfigData('menu/top/icon','');
            $position = $this->getConfigData('menu/top/position');
            add_menu_page($pageTitle, $menuTitle, $capability, $menuSlug, $callback, $iconUrl, $position);
        }
        if ($this->getConfigData('menu/parent')) {
            //add submenu page(s)
            $parentPages = is_array($this->getConfigData('menu/parent')) ? $this->getConfigData('menu/parent') : array($this->getConfigData('menu/parent'));
            foreach ($parentPages as $parentSlug) {
                add_submenu_page($parentSlug, $pageTItle, $menuTitle, $capability, $menuSlug, $callback);
            }
        }
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

    public function renderPage() {

        require $this->templateHandler->getView('page-start');

        $tabs = array_keys($this->tabs);

        foreach ($this->tabs as $tab=>$sections) {
            foreach ($sections as $section) {
                $section->display();
            }
        }

        require $this->templateHandler->getView('page-end');

    }

    public function save() {
        check_admin_referer(esc_attr($this->configData->name), 'adminUtilityNonce');
        $newOptionData = array();

        foreach ($this->tabs as $tab=>$sections) {
            foreach ($sections as $section) {
                foreach ($section->fields as $field) {
                    if (method_exists($field, 'save')) {
                        $field->save($_POST[$field->getKey()]); //for custom junk
                    } else {
                        $data = $field->prepareData($_POST[$field->getKey()],$post_id);
                        $newOptionData[$field->getKey()] = $data;
                    }
                }
            }
        }

        update_option($this->configData->name, array_merge($this->optionData, $newOptionData));

        wp_redirect(admin_url('admin.php?page='.$_POST['redirect']));
        die;
    }
}
