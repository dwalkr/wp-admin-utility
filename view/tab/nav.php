<div></div>
<div class="ui compact menu top adminTabs">
    <?php foreach ($tabs as $i=>$tab) : ?>
    <a class="item<?=($i==0) ? ' active' : '';?>" data-tab="<?=sanitize_title_with_dashes($tab);?>"><?=esc_html(ucwords(str_replace('-',' ',$tab)));?></a>
    <?php endforeach; ?>
</div>
