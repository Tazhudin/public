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
if( !empty($arResult['ITEMS'])):

    $itemsIds = [];
    foreach ($arResult['ITEMS'] as $key => $arProduct){
        $itemsIds[] = $arProduct['ID'];
        require("card.php");
    }
    if ($arParams['HIDE_NAV_STRING']!='Y') {
        //echo $arResult["NAV_STRING"];
    }
    ?>
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
                        item.find('.bem-icon-link_to-cart').children().text("В корзину");
                    } else {
                        item.find('.bem-icon-link_to-cart').addClass("bem-icon-link_cart-added");
                        item.find('.bem-icon-link_to-cart').children().text("В корзине");
                        item.find('.bem-icon-link_to-cart').attr('href', '/personal/cart/');
                        item.find('.bem-icon-link_to-cart').attr('onclick', '');
                    }
                })
            });

        })();
    </script>
<?php
else:
?>
<div class="bem-container bem-container_yellow not_found">
	<div class="not_found_title">Товары не найдены</div>
	<div class="not_found_desc"></div>
</div>
<?php
endif;
$this->__component->arResult['CACHED_TPL'] = @ob_get_contents();ob_end_clean();