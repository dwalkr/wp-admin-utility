<?php
global $post;
$imgUrl = $this->getFieldValue();
$showFile = (strlen($imgUrl) > 0);
$showImage = $showFile && self::isImage(basename($imgUrl));
?>
<input type="hidden"
       name="<?=esc_attr($this->configData->name);?>"
       value="<?=esc_attr($imgUrl);?>" />
<div class="filepreview">
    <img src="<?=esc_attr($imgUrl);?>"<?php if (!$showImage) { echo ' style="display:none;"';} ?> />
    <a class="filename" href="<?=esc_attr($imgUrl);?>" target="_blank"<?php if (!$showFile) { echo ' style="display:none;"';} ?>>
        <?=esc_attr(basename($imgUrl));?>
    </a>
</div>
<button class="btn btn-default ptconfig-attachment-upload">
    Upload File
</button>
<button class="btn btn-danger ptconfig-attachment-remove"<?php if (!$showFile) { echo ' style="display:none;"';} ?>>
    Remove
</button>
