<?php
$format = property_exists($this->configData, 'format') ? $this->configData->format : 'yyyy-mm-dd';
?>

<div class="ui calendar" id="uicalendar_<?=esc_attr($this->getKey());?>">
    <div class="ui input left icon">
        <i class="calendar icon"></i>
        <input type="text"
               name="<?=esc_attr($this->getKey());?>"
               value="<?=esc_attr($this->getFieldValue());?>"
        />
    </div>
</div>

<script>
    (function($){
        $(document).ready(function(){
           var $calendar = $('#uicalendar_<?=esc_attr($this->getKey());?>');
           $calendar.calendar({
               type: 'time',
               selector: {
                   activator: $calendar.find('.ui.input')
               }
            });
        });
    })(jQuery);
</script>
