<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace dwalkr\WPAdminUtility;

/**
 * Description of RepeatingMetaBox
 *
 * @author DJ Walker <donwalker1987@gmail.com>
 */
class RepeatingMetaBox extends MetaBox {

    private $actionName;
    private $items;
    private static $initialized = false;
    private $postid;

    public function __construct($data, $templateHandler, $ajaxAction) {
        $this->actionName = $ajaxAction;
        add_action('wp_ajax_'.$ajaxAction, array($this, 'save'));
        parent::__construct($data, $templateHandler);
    }

    private function getItems() {
        if (!isset($this->items)) {
            $this->items = get_post_meta(get_the_ID(), $this->configData->name, true);
            if (!is_array($this->items)) {
                $this->items = array();
            }
        }
        return $this->items;
    }

    public function getDataHtml() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : get_the_ID();
        require $this->templateHandler->getView('metabox/repeater-list');
    }

    public function display() {
        $this->postid = get_the_ID();

        $this->addModalToFooter();

        ?><div id="<?=esc_attr($this->configData->name);?>_list"><?php
            $this->getDataHtml();
        ?></div><?php
        require $this->templateHandler->getView('metabox/repeater-add');

    }

    public function addModalToFooter() {
        ob_start();
        $this->addMetaboxModal();
        $content = ob_get_clean();

        add_action('admin_footer', function() use ($content){
            echo $content;
        });
    }

    public function addMetaboxModal() {
        require $this->templateHandler->getView('metabox/repeater-start');
        parent::display();
        require $this->templateHandler->getView('metabox/repeater-end');

        if (self::$initialized === false) { ?>
            <script>var d3AdminUtil_repeaterFields = [];</script>
        <?php
            self::$initialized = true;
        }
        ?><script>
            d3AdminUtil_repeaterFields.push('<?=esc_attr($this->configData->name);?>');
        </script><?php
    }

    /**
     * This saves the actual repeater data via ajax
     */
    public function ajaxSave() {

    }

    /**
     * get data from a single repeater item
     */
    public function getItemData() {

    }

    /**
     * This probably usually won't do anything
     */
    public function save() {
    }


}
