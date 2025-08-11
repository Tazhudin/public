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
	<h1 class="h2 h2_mb15">
		<?=$arParams['TITLE']?>
		<span class="p p_vam p_fz13 p_fw400"><?= declensionWordByCount($countItems, 'модель', 'модели', 'моделей', true ); ?></span>
	</h1>
	<?php
	echo $arParams['~SECTIONS_FILTER'];
	
	if (count($arResult['SORT_TYPES']) > 0):
	?>
		<div class="sort-row sort-row_title-above-on-mobile">
			<div class="sort-row__title">Сортировать, сначала:</div>
			<?php
			foreach ($arResult['SORT_TYPES'] as $sortName => $sortParams):
				$active = $sortParams['ACTIVE']=='Y' ?' bem-icon-link_red' : '';
			?>
            <div class="sort-row__el">
                <a href="<?=$sortParams['URI'];?>" class="<?=$active?> bem-icon-link bem-icon-link_pseudo sort-row__link">
                    <span class="bem-icon-link__span"><?=$sortParams['NAME']?></span>
                </a>
            </div>
			<?php
			endforeach;
			?>
		</div>
	<?php
	endif;
	?>
    <div class="product-card-grid sale-page__product-card-grid" id="catalog_grid">
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
    }?>
	</div>
    <script type="text/javascript">
        (function() {
            let items = <?= json_encode($itemsIds);?>;

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

        })();
    </script>
<?php
else:?>
	<div class="bem-container bem-container_yellow catalog-page__not-found">
		<h3 class="h3">Товары не найдены</h3>
		<div class="p p_mb">Попробуйте изменить значения фильтров или убрать некоторые из них.</div>
		<a href="/search/?q=<?= $_REQUEST['q'];?>" class="bem-button bem-button_border bem-button_h41">Сбросить фильтры</a>
    </div>
<?php
endif;



$this->__component->arResult['CACHED_TPL'] = @ob_get_contents();ob_end_clean();
