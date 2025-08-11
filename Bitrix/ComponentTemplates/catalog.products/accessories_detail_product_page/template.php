<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult
 */

$itemsId = [];

if (count($arResult['ITEMS']) > 0): ?>
    <div class="product-card-grid product-card-grid_without-margins product-card-grid_brand-adaptive"><?php
    foreach ($arResult['ITEMS'] as $arItem):

        $itemsId[$arItem['ID']] = ["<!--detail_prod_acces-{$arItem['ID']}-->", (int)$arItem['PROPERTIES']['CML2_AMOUNT_IN_PACK']['VALUE']];

        $img = CResizer2Resize::ResizeGD2($arItem['DETAIL_PICTURE']['SRC'], 25);
        ?>
        <div class="product-card product-card-grid__el">
        <div class="product-card__wrapper">
            <a href="<?= $arItem['DETAIL_PAGE_URL'] ?>" class="product-card__photo-wrapper">
                <img src="<?= $img; ?>" class="product-card__photo">
            </a>
            <div class="product-card__title">
                <a href="<?= $arItem['DETAIL_PAGE_URL'] ?>"
                   class="bem-link bem-link_wu"><?= $arItem['NAME_FOR_SITE']; ?></a>
            </div>
            <div class="product-card__price product-card__price_two"><?= $itemsId[$arItem['ID']][0]; ?></div>
        </div>
        </div><?php
    endforeach; ?>
    </div><?php
else:?>
    <div>Список аксессуаров пуст</div><?php
endif;

$this->getComponent()->SetResultCacheKeys(['ITEMS_ID']);

$arResult['ITEMS_ID'] = $itemsId;
