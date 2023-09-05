<?php foreach(['errors' => 'danger', 'success' => 'success', 'warnings' => 'warning', 'infos' => 'info'] as $key => $label) {

if($notify_list = $this->notify->get($key)) foreach($notify_list as $notify) { ?>
    <div class="note note-<?=$label?>">
        <?php if($label != 'success') { ?>
        <div class="note-icon"><i class="fas fa-exclamation-triangle fa-sm"></i></div>
        <?php } ?>
        <div class="note-content p-10px">
            <h4><b><?=$notify->title?></b></h4>
            <p><?=$notify->text?></p>
            <?php if(!empty($notify->show_btn)) { ?>
            <p class="mt-15">
                <?php if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != '' && $_SERVER['HTTP_REFERER'] != SITE_URL){ ?>
                    <a class="btn btn-warning" href="<?=$_SERVER['HTTP_REFERER']?>"><?=$this->text('Повернутися назад!', 0)?></a>
                <?php } ?>
                <a class="btn btn-info" href="<?=SITE_URL?>"><?=$this->text('На головну!', 0)?></a>
            </p>
            <?php } ?>
        </div>
    </div>
<?php }

}