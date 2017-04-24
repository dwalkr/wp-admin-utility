<?php
$eleId = sanitize_title_with_dashes($this->getKey());
?>

<div class="ui calendar" id="uicalendar_<?=esc_attr($eleId);?>">
    <div class="ui input left icon">
        <i class="wait icon"></i>
        <input type="text"
               name="<?=esc_attr($this->getKey());?>"
               value="<?=esc_attr($this->getFieldValue());?>"
        />
    </div>
</div>

<script>
    (function($){
        $(document).ready(function(){
           var $calendar = $('#uicalendar_<?=esc_attr($eleId);?>');
           $calendar.calendar({
               type: 'time'
            });
        });
    })(jQuery);
</script>
