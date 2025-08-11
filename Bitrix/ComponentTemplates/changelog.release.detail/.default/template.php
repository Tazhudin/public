<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
    $isAuth = $USER->IsAuthorized();?>
    <div class="release-page universal-page-mixin">
        <div class="release-page__container">
            <div class="p p_fz13 grey-text release-page__date"><?= $arResult['RELEASE']['UF_DATE'] ?></div>
            <h2 class="h2 release-page__title"><?= $arResult['RELEASE']['UF_NAME'] ?></h2>
            <div class="release-page__content inner-tag-transform">
                <?php foreach ($arResult['RELEASE']['CHANGES'] as $changeType => $arChanges): ?>
                    <h3><?= $changeType ?></h3>
                    <ul>
                        <?php foreach ($arChanges as $id => $change): ?>
                            <li><?= $change['CHANGE_NAME'] ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endforeach; ?>
                <p><?= $arResult['RELEASE']['UF_DESCRIPTION'] ?></p>

                <?php foreach ($arResult['RELEASE']['CHANGES'] as $changeType => $arChanges): ?>
                    <?php foreach ($arChanges as $id => $change): ?>
                        <h3><?= $change['CHANGE_NAME'] ?></h3>
                        <?php if (!empty($change['CHANGE_FILES'])):
                            for ($i = 0; $i < count($change['CHANGE_FILES']); $i++) {
                                $imgSrc = CFile::GetPath($change['CHANGE_FILES'][$i]); ?>
                                <figure>
                                    <img src="<?= $imgSrc ?>" alt="<?= $change['CHANGE_NAME'] ?>">
                                    <?php if (!empty($change['CHANGE_IMG_TITLES'][$i])): ?>
                                        <figcaption><?= $change['CHANGE_IMG_TITLES'][$i] ?></figcaption>
                                    <?php endif; ?>
                                </figure>
                            <? } ?>
                        <?php endif; ?>
                        <p><?= $change['CHANGE_DESCRIPTION'] ?></p>
                    <?php endforeach; ?>
                <?php endforeach; ?>

                <div class="release-page__rate">
                    <div class="release-page__img-box"></div>
                    <div class="release-page__release">
                        <div class="release-page__release-text">
                            <div class="release-page__release-title release-page__release-title_active">Оцените релиз
                            </div>
                            <div class="release-page__release-title release-page__release-title_green">Спасибо за
                                оценку!
                            </div>
                            <div class="release-page__release-subtitle">Нам очень важна ваша оценка</div>
                        </div>
                        <div class="release-page__rating release-page-rating_js">
                            <div class="bem-product-rating <?= $isAuth ? 'bem-product-rating_interactive' : ''; ?>">
                                <?php
                                $userRate = ($arResult['RELEASE']['UF_RATE']) ?: 0;
                                for ($i = 1; $i <= 5; $i++) {?>
                                        <div class="bem-product-rating__el release-page__star <?= ((int)$userRate >= $i) ? 'bem-product-rating__el_active' : '' ?>"
                                        <?= $isAuth ? 'data-rate="'. $i .'"' : '' ?>></div>
                                <?php
                                }?>
                            </div>
                            <div class="release-page__rating-count"><span class="black-text" data-rate="user-rate"><?= $userRate ?></span> из 5.0</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if ($isAuth):?>
        <script>
            document.addEventListener("DOMContentLoaded", function (event) {
                let changelog = new JSChangelogSwitcher(
                    'release-page-rating_js',
                    <?= json_encode($component->GetName()); ?>,
                    <?= json_encode($arResult['RELEASE']['ID']); ?>,
                    <?= json_encode($userID = $USER->GetID()); ?>,
                    <?= json_encode($arResult['AJAX_REQUEST_HANDLER_NAME']); ?>,
                );
                changelog.init();
            });
        </script>
    <?php endif;
?>