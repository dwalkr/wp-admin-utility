<?php
$format = property_exists($this->configData, 'format') ? $this->configData->format : 'yyyy-mm-dd';
?>
<div class="input-group date"
     data-provide="datepicker"
     data-date-format="<?=esc_attr($format);?>">
    <input type="text"
           name="<?=esc_attr($this->configData->name);?>"
           class="form-control <?=property_exists($this->configData,'class') ? esc_attr($this->configData->class) : '';?>"
           value="<?=esc_attr($this->getFieldValue());?>"/>
    <div class="input-group-addon">
        <span class="fa fa-calendar"></span>
    </div>
</div>
