<div class="ui form">
    <?php if (property_exists($this->configData, 'description')) : ?>
    <div class="ui blue compact message"><?=esc_html($this->configData->description); ?></div>
    <?php endif; ?>
