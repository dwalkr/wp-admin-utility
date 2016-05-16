<?php

namespace dwalkr\PTConfig\Field;

use dwalkr\PTConfig\Field;

/**
 * Save ID and file path together
 *
 * @author DJ
 */
class Attachment extends Field {

    public function init() {
        wp_enqueue_script('wp-admin-utility/attachment-js');
    }

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
        $extension = end(explode('.',$filename));
        return in_array(strtolower($extension), $image_extensions);
    }

}
