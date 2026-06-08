<?php 
use oat\tao\helpers\Layout;
/* alpha|beta|sandbox message */
$releaseMsgData = Layout::getReleaseMsgData();
if($releaseMsgData['link']):?>
    <a href="#" title="<?=$releaseMsgData['msg']?>" class="lft">
    <?php else:?>
        <div class="lft">
        <?php endif;?>
        <img src="<?=$releaseMsgData['logo']?>" alt="CBT UNAND" id="tao-main-logo"/>
        <?php if($releaseMsgData['link']):?>
    </a>
<?php else:?>
    </div>
<?php endif;?>