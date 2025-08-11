<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

/**
 * @var array $arResult
 * @var array $arParams
 * @var CMain $APPLICATION
 */


$itemsIds = [];

?>
<div class="row-slider__arrow row-slider__arrow_right"></div>
<div class="row-slider__arrow row-slider__arrow_left"></div>
<div class="row-slider__slider-wrapper">
    <div class="row-slider__slider">
        <?php
        foreach ($arResult['ITEMS'] as $id => $item):
            $tmp = uniqid("slider_{$id}");

            $itemsIds[$id] = [
                'PRICE'    => "<!--price_{$tmp}-->",
                'COMPARE'  => "<!--cmp_{$tmp}-->",
                'FAVORITE' => "<!--favorite_{$tmp}-->",

                'AMOUNT_IN_PACK' => $item['PROPERTIES']['CML2_AMOUNT_IN_PACK']['VALUE'],
            ];

            $img = CResizer2Resize::ResizeGD2($item['DETAIL_PICTURE']['SRC'], 45);
            ?>
            <div class="product-card row-slider__product">
                <div class="product-card__wrapper">
                    <div class="product-card__tools">
                        <div class="product-card__tool product-card__tool_favorite">
                            <?= $itemsIds[$id]['FAVORITE']; ?>
                        </div>
                        <div class="product-card__tool product-card__tool_compare">
                            <?= $itemsIds[$id]['COMPARE']; ?>
                        </div>
                    </div>
                    <div class="product-card__top-wrapper">
                        <div class="product-card__photo-slider">
                            <a href="<?= $item['DETAIL_PAGE_URL'] ?>" class="product-card__photo-wrapper product-card__photo-wrapper_active">
                                <img data-src="<?= $img; ?>" class="product-card__photo bem-lazy loaded"
                                     src="<?= $img; ?>" data-was-processed="true">
                            </a>
                        </div>
                    </div>
                    <div class="product-card__text-wrapper"><?= $itemsIds[$id]['PRICE']; ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php

$arResult['ITEMS_ID'] = $itemsIds;

// Ключи сохраняемые в результат
$this->getComponent()->setResultCacheKeys(['ITEMS_ID']);
