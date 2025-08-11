<?php
$name = !empty($arProduct['PROPERTIES']['CML2_ELEMENT_PAGE_TITLE']['VALUE'])
			? $arProduct['PROPERTIES']['CML2_ELEMENT_PAGE_TITLE']['VALUE']
			: $arProduct['NAME'];
?>
<div class="product-card product-card_large product-adaptive-grid__el" id="each_elem_<?=$arProduct['ID'];?>" data-article="<?=$arProduct['PROPERTIES']['CML2_ARTICLE']['VALUE'];?>">
	<div class="product-card__wrapper">
		<div class="product-card__top-wrapper">
			<div class="sale-markers product-card__sale-markers product-card__sale-markers_bottom-left catalog-page__sale-markers_desktop">
				#STICKERS_<?=$arProduct['ID']?>#
			</div>
			<a title="<?=$name?>" href="<?=$arProduct['DETAIL_PAGE_URL']?>" class="product-card__photo-wrapper">
				<img 
					alt="<?=$name?>"
					src="<?=CResizer2Resize::ResizeGD2($arProduct['DETAIL_PICTURE']['SRC'], 38)?>"
					class="product-card__photo loaded"
				>
			</a>
		</div>
		<div class="product-card__text-wrapper">
			<!-- дублирование маркеров акций, в этом месте они нужны для мобильной версии -->
			<div class="sale-markers product-card__sale-markers product-card__sale-markers_inside-text catalog-page__sale-markers_mobile">
				#STICKERS_<?=$arProduct['ID']?>#
			</div>
			<div class="product-card__title p">
				<a href="<?= $arProduct['DETAIL_PAGE_URL'];?>" class="bem-link bem-link_wu" alt="<?=$name?>"><?=$name?></a>
			</div>
			#PRICE_<?=$arProduct['ID']?>#
			<div class="product-card__tools p">
				<div class="product-card__tool product-card__tool_cart">
					<?if($arProduct['QUANTITY'] > 0):?>
						#BASKET<?=$arProduct['ID']?>#
					<?endif;?>
				</div>
				<div class="product-card__tool product-card__tool_favorite">
					#FAVORITE_<?=$arProduct['ID']?>#
				</div>
				<div class="product-card__tool product-card__tool_compare">
					#COMPARE<?=$arProduct['ID']?>#
				</div>
				#STATUS<?=$arProduct['ID']?>#
			</div>
		</div>	
	</div>
</div>