<?php

if (constant('B_PROLOG_INCLUDED') !== true) {
    die();
}

// бренды для слайдера
$sliderBrands = [];

foreach ($arResult['BRANDS'] as $id => $brand) {
    if (!empty($brand['UF_FILE']) && $brand['UF_POPULAR']) {
        $sliderBrands [] = $brand;
    }
} ?>
<div class="brands-select brands-select_js-search p universal-page-mixin">
    <div class="row-slider row-slider_custom-el brands-select__slider">
        <div class="row-slider__arrow row-slider__arrow_right"></div>
        <div class="row-slider__arrow row-slider__arrow_left"></div>
        <div class="row-slider__slider-wrapper">
            <div class="row-slider__slider">
                <? foreach ($sliderBrands as $brand):?>
                    <div class="row-slider-custom-el row-slider-custom-el_brand-block category-page__brand-slider">
                        <a href="/brands/<?= $brand['UF_NAME']?>" class="row-slider-custom-el__wrapper">
                            <img src="<?=CFile::GetPath($brand['UF_FILE']);?>" class="row-slider-custom-el__brand-img" />
                        </a>
                    </div>
                <? endForeach; ?>
            </div>
        </div>
    </div>
    <div class="brands-select__search-field">
        <div class="brands-select__field-wrapper">
            <input type="text"
                   class="brands-select__field"
                   placeholder="Поиск по брендам" />
        </div>
        <div class="brands-select__tip">
            <a href="javascript:;" class="bem-link bem-link_wu brands-select__tip-el <?= $arParams['SECTION_ID']  == '' || $arParams['SECTION_ID']  === 'all' ? 'bem-link_red' : '' ?>" data-brand="section" data-brand-section-id="all">Все</a>
            <? foreach ($arResult['SECTIONS'] as $name => $section): ?>
                <a href="javascript:;" class="bem-link bem-link_wu brands-select__tip-el <?= $arParams['SECTION_ID']  == $section['ID'] ? 'bem-link_red' : '' ?>" data-brand="section" data-brand-section-id="<?= $section['ID'] ?>">
                    <?= $name ?>
                </a>
            <? endForeach; ?>
        </div>
    </div>
    <div class="select-filter brands-select__filter">
        <div class="select-filter__label">
            <div class="select-filter__marker select-filter__marker_show"></div>
            <p class="select-filter__label_desktop">Отфильтровать по категории</p>
            <p class="select-filter__label_mobile">Отфильтровать по категории</p>
        </div>
        <select class="select-filter__list" name="product-filters" size="1">
            <? foreach ($arResult['SECTIONS'] as $name => $section): ?>
                <option value="<?= $name ?>">
                    <?= $name ?>
                </option>
            <? endForeach; ?>
        </select>
    </div>
    <div class="brands-select__grid">
        <div class="brands-select__search-empty p p_tac">
            <div class="h2 brands-select__search-empty-title">
                Бренд не найден
            </div>Проверьте, правильно ли вы ввели название.
        </div>
        <div data-brands="brands">
            <? foreach ($arResult['SORTED_BRANDS'] as $char => $arBrands): ?>
                <div class="brands-select__col">
                    <div class="brands-select__col-char"><?= $char?></div>
                    <div class="brands-select__list">
                        <? foreach ($arBrands as $id => $brand): ?>
                            <div class="brands-select__list-el">
                                <a href="/brands/<?= $brand['UF_NAME']?>" class="bem-link bem-link_wu brands-select__list-link"><?= $brand['UF_NAME']?></a>
                            </div>
                        <? endforeach; ?>
                    </div>
                </div>
            <? endforeach; ?>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function (event) {
        let brandsFilter = new JSBrandSectionFilterSwitcher(
            <?= json_encode($component->GetName()); ?>,
            <?= json_encode($arResult['AJAX_REQUEST_HANDLER_NAME']); ?>
        );
        brandsFilter.init();
    });
</script>