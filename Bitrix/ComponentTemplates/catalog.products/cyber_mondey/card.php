<?php
$name = !empty($arProduct['PROPERTIES']['CML2_ELEMENT_PAGE_TITLE']['VALUE'])
			? $arProduct['PROPERTIES']['CML2_ELEMENT_PAGE_TITLE']['VALUE']
			: $arProduct['NAME'];
?>
<div class="product-card product-card-grid__el product-card_adaptive"
     id="each_elem_<?=$arProduct['ID'];?>"
     data-article="<?=$arProduct['PROPERTIES']['CML2_ARTICLE']['VALUE'];?>"
>
	<div class="product-card__wrapper">
        <div class="product-card__tools">
            <div class="product-card__tool product-card__tool_compare">
                #COMPARE<?=$arProduct['ID']?>#
            </div>
            <div class="product-card__tool product-card__tool_favorite">
                #FAVORITE_<?=$arProduct['ID']?>#
            </div>
        </div>
		<div class="product-card__top-wrapper">
            <div class="product-card__photo-slider" data-product-card-slider-active="true">
                <a
                    title="<?=$name?>"
                    href="<?=$arProduct['DETAIL_PAGE_URL']?>"
                    class="product-card__photo-wrapper product-card__photo-wrapper_active"
                >
                    <img
                        alt="<?=$name?>"
                        src="<?=CResizer2Resize::ResizeGD2($arProduct['DETAIL_PICTURE']['SRC'], 38)?>"
                        class="product-card__photo"
                    >
                </a>
            </div>
		</div>
		<div class="product-card__text-wrapper">
			<div class="product-card__sale-markers">
                <div class="product-card__sale-markers-wrap">#STICKERS_<?=$arProduct['ID']?>#</div>
            </div>
            <div class="product-card__price">#PRICE_<?=$arProduct['ID']?>#</div>
			<div class="product-card__title p">
				<a href="<?= $arProduct['DETAIL_PAGE_URL'];?>" class="bem-link bem-link_wu" alt="<?=$name?>"><?=$name?></a>
			</div>
            <div class="product-card__tool product-card__tool_cart">
                <?php if($arProduct['QUANTITY'] > 0):?>
                    #BASKET<?=$arProduct['ID']?>#
                <?php endif;?>
            </div>
            #STATUS<?=$arProduct['ID']?>#
		</div>	
	</div>
</div>