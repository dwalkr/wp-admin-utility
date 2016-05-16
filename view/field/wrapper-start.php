<div class="ptconfig-form-field field <?=$this->getConfigData('class');?>">
    <?php if (property_exists($this->configData, 'label')) : ?>
    <label><?=esc_html($this->configData->label);?></label>
    <?php endif;?>

