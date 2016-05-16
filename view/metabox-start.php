<div class="ui form">
    <?php if (property_exists($this->configData, 'description')) : ?>
    <div class="ui message"><?=esc_html($this->configData->description); ?></div>
    <?php endif; ?>
