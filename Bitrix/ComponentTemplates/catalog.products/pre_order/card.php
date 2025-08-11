<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var array $arProduct
 */

$name = $arProduct['PROPERTIES']['CML2_ELEMENT_PAGE_TITLE']['VALUE'] ?: $arProduct['NAME'];
?>
<div class="product-card product-card-grid__el" >
    <div class="product-card__wrapper">
        <div class="product-card__tools">
            <div class="product-card__tool">
                #FAVORITE_<?=$arProduct['ID']?>#
            </div>
            <div class="product-card__tool">
                #COMPARE<?=$arProduct['ID']?>#
            </div>
        </div>
        <div class="product-card__top-wrapper">
            <div class="product-card__photo-slider" data-product-card-slider-active="true">
                <a href="<?= $arProduct['DETAIL_PAGE_URL'];?>" class="product-card__photo-wrapper product-card__photo-wrapper_active">
                    <img src="<?=$arProduct['DETAIL_PICTURE']['SRC'];?>" class="product-card__photo">
                </a>
            </div>
        </div>
        <div class="product-card__text-wrapper">
            <div class="product-card__price">
                #PRICE_<?=$arProduct['ID']?>#
            </div>
            #BONUSES_<?=$arProduct['ID']?>#
            <div class="product-card__tools product-card__tools_mobile p">
                <div class="product-card__tool">
                    <?if($arProduct['QUANTITY'] > 0):?>
                        #MOBILEBASKET<?=$arProduct['ID']?>#
                    <?endif;?>
                </div>
                <div class="product-card__tool">
                    #FAVORITE_<?=$arProduct['ID']?>#
                </div>
                <div class="product-card__tool">
                    #COMPARE<?=$arProduct['ID']?>#
                </div>
            </div>
        </div>
    </div>
</div>
