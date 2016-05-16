<?php
$format = property_exists($this->configData, 'format') ? $this->configData->format : 'yyyy-mm-dd';
?>
<div class="ui fluid icon input"
     data-provide="datepicker"
     data-date-format="<?=esc_attr($format);?>">
    <input type="text"
           name="<?=esc_attr($this->getKey());?>"
           value="<?=esc_attr($this->getFieldValue());?>"/>
    <i class="calendar link icon"></i>
</div>
