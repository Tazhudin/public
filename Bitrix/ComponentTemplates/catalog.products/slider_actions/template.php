<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if(!empty($arResult['ITEMS'])) {?>
    <div class="row-slider__arrow row-slider__arrow_right"></div>
    <div class="row-slider__arrow row-slider__arrow_left"></div>
    <div class="row-slider__slider-wrapper">
        <div class="row-slider__slider"><?
            foreach ($arResult['ITEMS'] as $key => $arItems) {
                if(!empty($arItems['PROPERTIES']['BANNER_ON_MAIN']['VALUE']))
                    continue;?>
                <div class="news-card row-slider__product">
                <a title="<?=$arItems['NAME']?>" href="<?=$arItems['DETAIL_PAGE_URL']?>" class="news-card__photo" style="background-image:url(<?=CFile::GetPath($arItems['PROPERTIES']['PROMOIMG']['VALUE']);?>)"></a>
                <div class="news-card__title">
                    <a href="<?=$arItems['DETAIL_PAGE_URL']?>" class="bem-link"><?=$arItems['NAME']?></a>
                </div>
                </div><?
            }?>
        </div>
    </div>
    <?
}