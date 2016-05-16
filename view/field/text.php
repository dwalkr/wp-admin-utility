<input type="text"
       name="<?=esc_attr($this->configData->name);?>"
       class="form-control <?=property_exists($this->configData,'class') ? esc_attr($this->configData->class) : '';?>"
       value="<?=esc_attr($this->getFieldValue());?>"
/>
