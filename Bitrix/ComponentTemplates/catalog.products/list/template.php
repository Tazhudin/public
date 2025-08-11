<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
	die();
}

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

global $idPrice;
$this->setFrameMode(true);

ob_start();
$countItems = $this->__component->arResult["NAV_RESULT"]->nSelectedCount;

if (!empty($arResult['ITEMS'])):
?>
    <div class="catalog-page__header <?= $arParams['EXT_CONTAINER_CLASS'];?>" xmlns="">
		<div class="flex-grow-1 d-flex align-items-center flex-wrap">
			<h1 class="h1 h1_m0 d-inline-block pr-7"><?=$arParams['TITLE']?></h1>
            <?php if ($countItems > 0): ?>
			    <div class="d-inline-block p p_vam p_fz13 grey-text">
                    <?=declensionWordByCount($countItems, 'модель', 'модели', 'моделей', true )?>
                </div>
            <?php endif; ?>
		</div>
		<?php	
		echo $arParams['~SECTIONS_FILTER'];

		if (count($arResult['SORT_TYPES']) > 0):
            $mobileSortSelectId = uniqid('search_sort_');
		?>	
			<div class="catalog-page__header-sort">
				<div class="sort-row sort-row_title-above-on-mobile">
					<div class="sort-row__title">Сортировать, сначала:</div>
					<?php
                    $mobileSortOptions = '';
                    $fSelected = false;
					foreach ($arResult['SORT_TYPES'] as $sortName => $sortParams):
                        $selected = $sortParams['ACTIVE'] === 'Y';
                        $fSelected = $selected || $fSelected;
                        $active = $selected ? 'bem-icon-link_red' : '';
                        $selected = $selected ? 'selected="selected"' : '';
                        $mobileSortOptions .= "<option class=\"mobile-smart-filters__option\" value=\"{$sortParams['URI']}\" {$selected}>{$sortParams['NAME']}</option>";
					?>
                    <div class="sort-row__el">
                        <a href="<?=$sortParams['URI'];?>" class="<?= $active; ?> bem-icon-link bem-icon-link_pseudo sort-row__link"><?=$sortParams['NAME']?></a>
                    </div>
					<?php
					endforeach;
                    if (!$fSelected) {
                        $mobileSortOptions = '<option class="mobile-smart-filters__option">Сортировать по</option>' . $mobileSortOptions;
                    }
					?>
				</div>
			</div>

            <div class="catalog-page__mobile-smart-filters">
                <div class="mobile-smart-filters">
                    <div class="mobile-smart-filters__row">
                        <div class="mobile-smart-filters__select-wrap mobile-smart-filters__select-wrap_sort">
                            <select id="<?= $mobileSortSelectId; ?>" class="mobile-smart-filters__select mobile-smart-filters__select_sort" size="1"><?= $mobileSortOptions; ?></select>
                        </div>

                        <?php
                        if (!empty($arParams['QUICK_FILTER_ITEMS']) && is_array($arParams['QUICK_FILTER_ITEMS']['ITEMS']) && !empty($arParams['QUICK_FILTER_ITEMS']['ITEMS'])):
                        ?>
                        <div class="mobile-smart-filters__select-wrap mobile-smart-filters__select-wrap_filter">
                            <div class="mobile-smart-filters__select-text mobile-smart-filters__select-text_filter">
                                <?= $arParams['QUICK_FILTER_ITEMS']['TITLE'];?>
                            </div>
                            <select id="<?= $mobileSortSelectId; ?>_quick" class="mobile-smart-filters__select mobile-smart-filters__select_filter"
                                    name="catalog-filters" size="1">
                                <?php foreach ($arParams['QUICK_FILTER_ITEMS']['ITEMS'] as $qItem) : ?>
                                <option value="<?= $qItem['URL'];?>" <?= $qItem['ACTIVE'] ? 'selected="selected"' : '';?>>
                                    <?= $qItem['NAME'];?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <?php
                        endif;
                        ?>

                    </div>
                </div>
            </div>
            <script type="text/javascript">
            (function () {
                let timer = null;
                document.querySelector('#<?= $mobileSortSelectId; ?>').addEventListener('change', function (e) {
                    clearTimeout(timer);
                    timer = setTimeout(() => {
                        if (e.target.value) {
                            window.location.href = e.target.value;
                        }
                    }, 500);
                });
                let qFilter = document.querySelector('#<?= $mobileSortSelectId; ?>_quick');
                if (qFilter){
                    qFilter.addEventListener('change', (e) => window.location.href = e.target.value);
                }
            })();
            </script>
		<?php
		endif;
		?>
	</div>
	<div class="catalog-page__grid">
		<div class="product-card-grid catalog-page__product-card-grid" id="catalog_grid">
			<?php
            $itemsIds = [];

            $tmpUniq = uniqid('prod_list_');

			foreach ($arResult['ITEMS'] as $key => $arProduct){

                $itemsIds[] = $arProduct['ID'];

                $arResult['PLACEHOLDERS'][$arProduct['ID']] = [
                    'PRICE'    => "<!--price_{$tmpUniq}_{$arProduct['ID']}-->",
                    'COMPARE'  => "<!--cmp_{$tmpUniq}_{$arProduct['ID']}-->",
                    'FAVORITE' => "<!--favorite_{$tmpUniq}_{$arProduct['ID']}-->",

                    'AMOUNT_IN_PACK' => $arProduct['PROPERTIES']['CML2_AMOUNT_IN_PACK']['VALUE'],
                ];

                $placeholders = $arResult['PLACEHOLDERS'][$arProduct['ID']];

                require(__DIR__ . '/card.php');
			}
			?>
		</div>
        <?php
        if ($arParams['HIDE_NAV_STRING'] != 'Y') {
            echo $arResult["NAV_STRING"];
        }
		?>
	</div>
<?php
else:
?>
<div class="bem-container bem-container_yellow catalog-page__not-found">
	<h3 class="h3">Товары не найдены</h3>
	<div class="p p_mb">Попробуйте изменить значения фильтров или убрать некоторые из них.</div>
	<a href="/search/?q=<?= $_REQUEST['q'];?>" class="bem-button bem-button_border bem-button_h41">Сбросить фильтры</a>
</div>
<?php
endif;
?>
<script type="text/javascript">
(function() {
    var items = <?= json_encode($itemsIds ?: []);?>;

    document.addEventListener('changeBasket', function (e) {
        let basket = e.detail;
        let item = null;

        items.forEach((i) => {
            let product = basket.getProduct(i);
            item = $('#each_elem_' + i);
            if (!item) return;
            if (!product.success) {
                item.find('.bem-icon-link_to-cart').removeClass('bem-icon-link_cart-added');
                item.find('.bem-icon-link_to-cart[data-type="desktop"] .bem-icon-link__span').text("В корзину");
                item.find('.bem-icon-link_to-cart').attr('href', 'javascript:;');
                item.find('.bem-icon-link_to-cart').attr('onclick', 'universal.product.addToBasketModal(this,' + i + ', null, \'catalog\'); yaCounter858663.reachGoal(\'addbasket\'); return true;');
            } else {
                item.find('.bem-icon-link_to-cart').addClass("bem-icon-link_cart-added");
                item.find('.bem-icon-link_to-cart[data-type="desktop"] .bem-icon-link__span').text("В корзине");
                item.find('.bem-icon-link_to-cart').attr('href', '/personal/cart/').attr('onclick', '');
            }
        })
    });
    <?php if ($arParams['IS_FAVORITE_PAGE'] === 'Y'): ?>
    document.addEventListener('changeFavorite', function (e) {
        let favorite = e.detail;
        let item = null;

        if (!items.length && favorite.getData().result.count > 0) {
            window.location.reload();
        }

        items.forEach((el, i, tmpItems) => {
            let product = favorite.getProduct(el);
            item = $('#each_elem_' + el);
            if (item && !product.success) {
                tmpItems.splice(i, 1);
                item.remove();
            }
        });
        if (!items.length) {
            window.location.reload();
        }
    });
    <?php endif; ?>
})();
</script>
<?php

$this->__component->arResult['CACHED_TPL'] = @ob_get_contents();ob_end_clean();
