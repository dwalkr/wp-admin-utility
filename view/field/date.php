<?php
$format = property_exists($this->configData, 'format') ? $this->configData->format : 'yyyy-mm-dd';
$eleId = sanitize_title_with_dashes($this->getKey());
?>

<div class="ui calendar" id="uicalendar_<?= esc_attr($eleId); ?>">
    <div class="ui input left icon">
        <i class="calendar icon"></i>
        <input type="text"
               name="<?= esc_attr($this->getKey()); ?>"
               value="<?= esc_attr($this->getFieldValue()); ?>"
               />
    </div>
</div>

<script>
    (function ($) {
        $(document).ready(function () {
            $('#uicalendar_<?= esc_attr($eleId); ?> input').datepicker({
               <?php if( property_exists($this->configData, 'format' )){ ?>
                       dateFormat: '<?php echo $format; ?>'
               <?php } ?>
            });
        });
    })(jQuery);
</script>
