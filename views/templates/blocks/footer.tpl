<?php
use oat\tao\helpers\Layout;
use oat\tao\helpers\Template;
use oat\tao\helpers\ApplicationHelper;
?>
<footer aria-label="<?=__('About')?>" class="dark-bar">
    <div class="lft">
        © 2013 - <?= date('Y') ?> · <span class="tao-version"><?= ApplicationHelper::getVersionName() ?></span> ·
        <a href="#" target="_blank">Lembaga Bahasa Internasional FIB UI </a>
        · <?= __('All rights reserved.') ?>
        <?php $releaseMsgData = Layout::getReleaseMsgData();
        if ($releaseMsgData['msg'] && ($releaseMsgData['is-unstable'] || $releaseMsgData['is-sandbox'])): ?>
            <span class="rgt">
                <?php if ($releaseMsgData['is-unstable']): ?>
                    <span class="icon-warning"></span>
                <?php endif; ?>
                <?=$releaseMsgData['version-type']?> ·
            <a href="#" target="_blank"><?=$releaseMsgData['msg']?></a></span>
        <?php endif; ?>
    </div>
    <div class="rgt">
        <?php $operatedByData = Layout::getOperatedByData();
        if (! empty($operatedByData['name'])): ?>
            <?= __('Operated By ') ?>
            <?php if (! empty($operatedByData['email'])): ?>
                <a href="#"><?= $operatedByData['name'] ?></a>
            <?php else: ?>
                <?= $operatedByData['name'] ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>

</footer>
<?php Template::inc('blocks/careers-js.tpl', 'tao'); ?>