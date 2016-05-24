<form class="ui modal form" id="<?=esc_attr($this->configData->name);?>_form">
    <div class="ui dimmer inverted"><div class="ui loader"></div></div>
    <div class="header">
        <span class="addedit">Add</span> <?=esc_html($this->configData->title);?>
    </div>
    <div class="content">
        <input type="hidden" name="action" value="<?=esc_attr($this->actionName);?>" />
        <input type="hidden" name="id" vale="<?=esc_attr($this->postid);?>"
        <?php wp_nonce_field('wpadminutility-repeater', '_wpadminutility'); ?>

