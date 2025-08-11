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

if (!empty($arResult['ITEMS'])): ?>
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
        } ?>
    </div>
    <?php
    if ($arParams['HIDE_NAV_STRING'] != 'Y') {
        echo $arResult["NAV_STRING"];
    } ?>

<?php else: ?>
    <div class="bem-container bem-container_yellow catalog-page__not-found">
        <h3 class="h3">Товары не найдены</h3>
    </div>
<?php endif;

$this->__component->arResult['CACHED_TPL'] = @ob_get_contents();ob_end_clean();
