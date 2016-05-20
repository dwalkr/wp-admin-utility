<?php
$format = property_exists($this->configData, 'format') ? $this->configData->format : 'yyyy-mm-dd';
$eleId = sanitize_title_with_dashes($this->getKey());
?>

<div class="ui calendar" id="uicalendar_<?=esc_attr($eleId);?>_start">
    <div class="ui input left icon">
        <i class="calendar icon"></i>
        <input type="text"
               name="<?=esc_attr($this->getKey());?>[start]"
               value="<?=esc_attr($this->getStartDate());?>"
        />
    </div>
</div>
<?php if ($this->getConfigData('text_between', false)) : ?>
<div class="adminutility-daterange-between">
    <?=$this->getConfigData('text_between'); ?>
</div>
<?php endif; ?>
<div class="ui calendar" id="uicalendar_<?=esc_attr($eleId);?>_end">
    <div class="ui input left icon">
        <i class="calendar icon"></i>
        <input type="text"
               name="<?=esc_attr($this->getKey());?>[end]"
               value="<?=esc_attr($this->getEndDate());?>"
        />
    </div>
</div>

<script>
    (function($){
        $(document).ready(function(){
           var $calendarStart = $('#uicalendar_<?=esc_attr($eleId);?>_start');
           var $calendarEnd = $('#uicalendar_<?=esc_attr($eleId);?>_end');
           $calendarStart.calendar({
               type: 'date',
               endCalendar: $calendarEnd
            });
            $calendarEnd.calendar({
               type: 'date',
               startCalendar: $calendarStart
            });
        });
    })(jQuery);
</script>
