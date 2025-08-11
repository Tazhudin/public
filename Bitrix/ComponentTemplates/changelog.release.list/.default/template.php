<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}?>

<div class="changelog-page universal-page-mixin">
    <div class="changelog-page__header">
        <div class="changelog-page__header-left"></div>
        <div class="changelog-page__header-center">
            <div class="changelog-page__header-icon"></div>
            <h1 class="changelog-page__header-title">Изменения на сайте</h1>
        </div>
        <div class="changelog-page__header-right"></div>
    </div>
    <div class="changelog-page__content">
        <?php foreach ($arResult['RELEASES'] as $release): ?>
            <?php
            // если релиз отмечен как важный changelog-page__el_highlighted
            $importantReleaseClass = ($release['UF_IMPORTANT'] == 1) ? "changelog-page__el_highlighted" : "";
            ?>
            <div class="changelog-page__el <?= $importantReleaseClass ?>">
                <div class="p p_fz13 grey-text"><?= $release['UF_DATE'] ?></div>
                <a class="bem-link" href="release/<?= $release['ID'] ?>">
                    <h2 class="h2 h2_mb20"><?= $release['UF_NAME'] ?></h2>
                </a>

                <div class="changelog-page__el-content inner-tag-transform">
                    <?php if (!empty($release['CHANGES'])): ?>
                        <?php foreach ($release['CHANGES'] as $changeType => $changes): ?>
                            <h3><?= $changeType ?></h3>
                            <ul>
                                <?php foreach ($changes as $change): ?>
                                    <li><?= $change['CHANGE_NAME'] ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="changelog-page__nav">
        <?php
        $GLOBALS['APPLICATION']->IncludeComponent('bitrix:system.pagenavigation', 'products_list', ['NAV_RESULT' => $arResult['CDB_RESULT']]);
        ?>
    </div>

</div>
