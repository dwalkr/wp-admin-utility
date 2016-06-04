<div class="ui  checkbox">
    <input type="checkbox"
       name="<?=esc_attr($this->getKey());?>"
       value="1"
       <?php if($this->getFieldValue()) { echo 'checked="checked"'; } ?>
    />
    <label><?php echo $this->getConfigData('label'); ?> </label>
</div>
