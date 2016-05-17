<div class="wrap">
    <div class="ui container">
        <h1 class="header"><?=$this->configData->title;?></h1>
        <form action="<?=admin_url('admin.php');?>" method="POST" class="ui form" id="poststuff">
            <button class="large ui primary button" type="submit">Save</button>
            <input type="hidden" name="action" value="save_<?=esc_attr($this->configData->name);?>" />
            <input type="hidden" name="redirect" value="<?=esc_attr($_GET['page']);?>" />
            <?php wp_nonce_field(esc_attr($this->configData->name), 'adminUtilityNonce'); ?>
