<?php

if (constant('B_PROLOG_INCLUDED') !== true) {
    die();
}

/**
 * @var array $arResult
 */
if (!empty($arResult['PROMO']['BANNERS']['SECTION_NAME']) && !empty($arResult['PROMO']['VIDEOS']['SECTION_NAME'])) {
    include __DIR__ . '/inc/promo.php';
    return;
} ?>
<div class="brand-page universal-page-mixin p">
    <div class="brand-page__sidebar">
        <div class="brand-page__logo-wrapper">
            <?php if (empty($arResult['INFO']['UF_FILE'])) : ?>
                <h3 class="h3 h3_fw400 h3_m0 h3_tac"><?= $arResult['INFO']['UF_NAME'] ?></h3>
            <?php else : ?>
                <img src="<?= $arResult['INFO']['UF_FILE'] ?>" alt="<?= $arResult['INFO']['UF_NAME'] ?>" class="brand-page__logo" width="130">
            <?php endif; ?>
        </div>
        <div class="brand-page__category-list">
            <h3 class="h3 h3_mb12 h3_fz21">
                Категории товаров бренда
            </h3>
            <div class="category-page__filter-list">
                <?php foreach ($arResult['SECTIONS'] as $sectionName => $section) : ?>
                    <div class="category-page__filter">
                        <div class="category-page__filter-title"><?= $sectionName ?></div>
                        <div class="dash-list p p_fz13 category-page__filter-prop-list">
                            <?php foreach ($section['CHILD'] as $subSectionName => $subSection) : ?>
                                <div class="dash-list__item"><a href="<?=str_replace(' ', '',$subSection['SECTION_PAGE_URL'].mb_strtolower($arResult['INFO']['UF_NAME']))?>" class="bem-link bem-link_wu"><?= $subSectionName ?></a></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="brand-page__content">
        <div class="h1 h1_mb14"><?= $arResult['INFO'][key($arResult['INFO'])]['UF_NAME'] ?></div>
        <div class="row-picture-link row-picture-link_scroll-on-mobile catalog-page__row-picture-link">
            <div class="row-picture-link__wrapper">
                <?php foreach ($arResult['SECTIONS'] as $section) : ?>
                    <?php foreach ($section['CHILD'] as $subSectionName => $subSection) : ?>
                        <a href="<?=str_replace(' ', '',$subSection['SECTION_PAGE_URL'].mb_strtolower($arResult['INFO']['UF_NAME']))?>" class="row-picture-link__el">
                            <div class="row-picture-link__photo">
                                <img class="row-picture-link__img" src="<?= $subSection['PICTURE'] ?>">
                            </div>
                            <div class="row-picture-link__text">
                                <div class="p p_crop2"><?= $subSection['NAME'] ?></div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php include __DIR__ . '/inc/actions.php'; ?>

        <div class="h2 h2_mt mb_15">
            Товары бренда
        </div>
        
        <?php include __DIR__ . '/inc/products.php'; ?>

        <?php if (!empty($arResult['INFO']['UF_FULL_DESCRIPTION'])) : ?>
            <div class="h2 h2_mb20 h2_mt">О бренде <?= $arResult['INFO']['UF_NAME'] ?></div>
            <div class="p">
                <?= $arResult['INFO']['UF_FULL_DESCRIPTION']; ?>
            </div>
        <?php endif; ?>
    </div>
</div>