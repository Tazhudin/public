<div class="row-slider row-slider_product row-slider_with-tabs" data-slider="slider-with-tabs">
    <div class="row-slider__tabs" data-tabs="tabs">
        <div class="underline-tabs">
            <?php foreach ($collectionTypes as $type):
                $active = $arParams['ACTIVE_COLLECTION_TYPE'] == $type ? 'underline-tabs__tab_active' : '' ?>
                <div class="underline-tabs__tab <?= $active ?>"
                     data-bx-js="collection-type"
                     data-tab="<?= $arProductCollectionTypes[$type]['DATA_ATTR'] ?>"
                ><?= $arProductCollectionTypes[$type]['NAME'] ?></div>
            <?php endforeach; ?>
        </div>
        <div class="underline-tabs_mobile" data-bx-js="collection-mobileTabs">
            <select class="underline-tabs__select">
                <?php foreach ($collectionTypes as $type):
                    $active = $arParams['ACTIVE_COLLECTION_TYPE'] == $type ? 'selected' : '' ?>
                    <option value="<?= $arProductCollectionTypes[$type]['NAME'] ?>"
                            data-bx-js="collection-type"
                        <?= $active?>
                            data-tab="<?= $arProductCollectionTypes[$type]['DATA_ATTR'] ?>"
                    ><?= $arProductCollectionTypes[$type]['NAME'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php if ($arParams['USE_FILTER_BY_SECTIONS'] === 'Y' && !empty($arResult['SECTIONS'])): ?>
            <div class="select-filter" data-slider-filter="select">
                <div class="select-filter__label">
                    <div class="select-filter__marker select-filter__marker_show"></div>
                    <? if (\Dev05\Classes\SiteHelper::isMobile()): ?>
                        <p class="select-filter__label_mobile">Фильтр</p>
                    <? else: ?>
                        <p class="select-filter__label_desktop">Отфильтровать</p>
                    <? endif; ?>
                </div>
                <select class="select-filter__list" name="product-filters" size="1">
                    <option value="all">Все разделы</option>
                    <? foreach ($arResult['SECTIONS'] as $id => $section): ?>
                        <option value="<?= $id ?>">
                            <?= $section['NAME'] ?>
                        </option>
                    <? endforeach; ?>
                </select>
            </div>
        <? endif; ?>
    </div>
    <?php foreach ($collectionTypes as $type):
        $active = $arParams['ACTIVE_COLLECTION_TYPE'] == $type ? 'row-slider__content_active' : '' ?>
        <div class="row-slider__content <?= $active ?>"
             data-tab="<?= $arProductCollectionTypes[$type]['DATA_ATTR'] ?>"
        >
            <?php if ($active): ?>
                <div class="row-slider__arrow row-slider__arrow_right"></div>
                <div class="row-slider__arrow row-slider__arrow_left"></div>
                <div class="row-slider__slider-wrapper">
                    <div class="row-slider__slider">
                        <?foreach ($arResult['ITEMS'] as $id => $item):
                            $tmp = uniqid("slider_{$id}");
                            $itemsIds[$id] = [
                                'PRICE' => "<!--price_{$tmp}-->",
                                'COMPARE' => "<!--cmp_{$tmp}-->",
                                'FAVORITE' => "<!--favorite_{$tmp}-->",
                                'AMOUNT_IN_PACK' => $item['PROPERTIES']['CML2_AMOUNT_IN_PACK']['VALUE'],
                            ];
                            $img = CResizer2Resize::ResizeGD2($item['DETAIL_PICTURE']['SRC'], 45); ?>
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
                                            <a href="<?= $item['DETAIL_PAGE_URL'] ?>"
                                               class="product-card__photo-wrapper product-card__photo-wrapper_active">
                                                <img data-src="<?= $img; ?>"
                                                     class="product-card__photo bem-lazy loaded"
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
            <? endif; ?>
        </div>
    <?php endforeach; ?>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function (event) {
        let slider = new JSCollectionSwitcher(
            <?= json_encode($component->GetName()); ?>,
            'ajaxRequestHandler',
            <?= json_encode($arProductCollectionTypes); ?>,
            <?= json_encode($arParams); ?>
        );
        slider.init(<?= json_encode($arParams); ?> );
    });
</script>