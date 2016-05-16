<?php
global $post;
$imgUrl = $this->getFieldValue();
$showFile = (strlen($imgUrl) > 0);
$showImage = $showFile && self::isImage(basename($imgUrl));
$icon = self::isImage(basename($imgUrl)) ? 'file image outline' : 'file outine';
?>
<input type="hidden"
       name="<?=esc_attr($this->getKey());?>"
       value="<?=esc_attr($imgUrl);?>" />
<div class="filepreview">
    <img src="<?=esc_attr($imgUrl);?>"<?php if (!$showImage) { echo ' style="display:none;"';} ?> />
    <a class="filename" href="<?=esc_attr($imgUrl);?>" target="_blank"<?php if (!$showFile) { echo ' style="display:none;"';} ?>>
        <?=esc_attr(basename($imgUrl));?>
    </a>
</div>
<button type="button" class="ui primary right labeled icon button ptconfig-attachment-upload">
    Upload File
    <i class="icon <?=$icon;?>"></i>
</button>
<button type="button" class="ui red right labeled icon button ptconfig-attachment-remove"<?php if (!$showFile) { echo ' style="display:none;"';} ?>>
    Remove
    <i class="icon remove"></i>
</button>
