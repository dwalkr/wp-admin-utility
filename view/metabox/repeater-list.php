<?php foreach ($this->getItems as $row_id=>$row) : ?>
    <div class="item">
        <div class="right floated content">
            <button title="Delete item" type="button" class="ui mini red icon button delete-item" data-rowid="<?=$row_id;?>" data-postid="<?=$id;?>"><i class="ui icon close"></i> Remove</button>
            <button title="Edit item" type="button" class="ui mini blue icon button edit-item" data-rowid="<?=$row_id;?>" data-postid="<?=$id;?>"><i class="ui icon pencil"></i> Edit</button>
        </div>
        <i class="ui icon resize vertical"></i>
        <div class="content">
            <div class="header"><?=esc_html(key($row[0]));?>: <?=esc_html($row[0]);?></div>
            <div class="content">
                <table>
                <?php for ($j=0; $j<count($row); $j++) : ?>
                    <tr>
                        <th><?=esc_html(key($row[$j]));?></th><td><?=$row[$j];?></td>
                    </tr>
                <?php endfor; ?>
                </table>
            </div>
        </div>
        <div style="clear:both;"></div>

<?php endforeach; ?>
