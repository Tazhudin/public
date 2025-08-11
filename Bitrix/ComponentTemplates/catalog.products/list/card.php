<?php


if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}


/**
 * @var array $arProduct
 * @var array $placeholders
 */

$name = !empty($arProduct['PROPERTIES']['CML2_ELEMENT_PAGE_TITLE']['VALUE'])
    ? $arProduct['PROPERTIES']['CML2_ELEMENT_PAGE_TITLE']['VALUE']
    : $arProduct['NAME'];
?>
<div class="product-card product-card-grid__el product-card_adaptive" id="each_elem_<?= $arProduct['ID']; ?>">
    <div class="product-card__wrapper">
        <div class="product-card__tools">
            <div class="product-card__tool product-card__tool_favorite window-cart-favorite-show">
                <?= $placeholders['FAVORITE']; ?>
            </div>
            <div class="product-card__tool product-card__tool_compare">
                <?= $placeholders['COMPARE']; ?>
            </div>
        </div>
        <div class="product-card__top-wrapper">
            <div class="product-card__photo-slider" data-product-card-slider-active="true">
                <a href="<?= $arProduct['DETAIL_PAGE_URL']; ?>" class="product-card__photo-wrapper product-card__photo-wrapper_active">
                    <img src="<?= CResizer2Resize::ResizeGD2($arProduct['DETAIL_PICTURE']['SRC'], 38); ?>" class="product-card__photo">
                </a>
            </div>
        </div>
        <div class="product-card__text-wrapper">
            <div class="product-card__sale-markers">
                #STICKERS_<?= $arProduct['ID'] ?>#
            </div>
            <div class="product-card__price"><?= $placeholders['PRICE']; ?></div>
            <?php
            foreach ($arProduct['STICKERS'] as $sticker) :
            ?>
                <a href="<?= $sticker['LINK']; ?>">
                    <span class="product-card__text-marker product-card__text-marker_new">
                        <?= $sticker['UF_NAME']; ?>
                    </span>
                </a>
            <?php
            endforeach;
            ?>
            <div class="product-card__title p">
                <a href="<?= $arProduct['DETAIL_PAGE_URL']; ?>" class="bem-link bem-link_wu"><?= $name; ?></a>
            </div>
            <div class="product-card__reviews"><?php// Добавлен чтоб был необходимый отступ?></div>
            <div class="product-card__tool product-card__tool_cart">
                <?if($arProduct['QUANTITY'] > 0):?>
                #BASKET_<?= $arProduct['ID'] ?>#
                <?endif;?>
            </div>
            #STATUS_<?= $arProduct['ID'] ?>#
            <div class="product-card__tools product-card__tools_mobile p">
                <div class="product-card__tool product-card__tool_cart">
                    <?if($arProduct['QUANTITY'] > 0):?>
                    #MOBILE_BASKET_<?= $arProduct['ID'] ?>#
                    <?endif;?>
                </div>
                <div class="product-card__tool product-card__tool_favorite window-cart-favorite-show">
                    <?= $placeholders['FAVORITE']; ?>
                </div>
                <div class="product-card__tool product-card__tool_compare window-cart-compare-show">
                    <?= $placeholders['COMPARE']; ?>
                </div>
                #STATUS_<?= $arProduct['ID'] ?>#
            </div>
        </div>
    </div>
</div>