<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

use Dev05\Classes\Constant;
use Dev05\Classes\GlobalFactory;
use Dev05\Classes\Store;

global $USER, $idPrice;
$productsIDs = \Dev05\Classes\GlobalFactory::getInstance()->getBasket()->getProductIds();

$content = $arResult['CACHED_TPL']; // Вывод некэшируемого содержимого
$favorites = FavoriteProducts::getUserFavorites($USER->GetID());
$location = \Dev05\Classes\GlobalFactory::getInstance()->getCurrentLocation()->getData(); // Данные текущего местоположения
if ($arParams['FROM'] == 'ACTIONS' && $arParams['IBLOCK_ID'] == PC_BUILD_IBLOCK_ID) {
	$addParam = 'build';	
}
$dbProp = CIBlockPropertyEnum::GetList( 
	[], 
	["IBLOCK_ID" => ACTION_IBLOCK_ID,  "CODE" => "GEO_TARGETING", "EXTERNAL_ID" => ["all", $idPrice, $location['CITY_ID']]]
);
while($ar = $dbProp -> GetNext()) {
	$arGeoTargetingFilter[] = $ar['VALUE'];
}
$auth = $USER->IsAuthorized();
$arPrices = toGetPrice(array_keys($arResult['ITEMS_ID']));
$arProductStoreInfo = (new Store(
    array_keys($arResult['ITEMS_ID']),
    false, ['SHOW_FEW_STATUS' => false]
))->get();
$compares = GlobalFactory::getInstance()->getCompare()->getList();
$replaces = [];
$searches = [];
foreach ($arResult['ITEMS_ID'] as $id => $iblockId){
	$arPrice = $arPrices[$id];
	
    // get props
    $dbProps = CIBlockElement::GetProperty(
        $iblockId, 
        $id,
        array(),
        array('CODE' => 'CML2_%')
    );
    $props = [];
    while($res = $dbProps->GetNext()){
        if ($res['PROPERTY_TYPE'] == 'F'){
            continue;
        }elseif ($res['PROPERTY_TYPE'] == 'L'){
            $val = $res['VALUE_ENUM'];
        }else {
            $val = $res['VALUE'];
        }
        
        if (empty($val)) continue;
        
        if ($res['MULTIPLE'] == 'Y'){
            $props[ $res['CODE'] ][] = $val;
        }else {
            $props[ $res['CODE'] ] = $val;
        }
    }
    // stickers
	ob_start();
	\Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("item-sticker-{$id}");
    if (!empty($props['CML2_PROMOLINK'])){
        /**/
        $arActions = [];
        $rs_action = CIBlockElement::GetList(
            array("ID" => "DESC"),
            array('IBLOCK_ID' => ACTION_IBLOCK_ID, 'ID' => $props['CML2_PROMOLINK'], 'ACTIVE' => 'Y', 'ACTIVE_DATE' => 'Y', "PROPERTY_GEO_TARGETING_VALUE" => $arGeoTargetingFilter),
            false,
            false,
			array ('IBLOCK_ID', 'ID', 'NAME', 'DATE_ACTIVE_TO', 'DETAIL_PAGE_URL', 'PROPERTY_TEXT_STICKER', 'PROPERTY_BG_TEXT', 'PROPERTY_ACTION_IMG', 'PROPERTY_GEO_TARGETING')
        );
        while($action = $rs_action->GetNext())
        {
            $arAction = [];
            $geo = [];
			$db_property = \CIBlockElement::GetProperty(
				$action['IBLOCK_ID'],
				$action['ID'],
				[],
				['CODE' => 'GEO_TARGETING']
			);
			
			while($arProp = $db_property -> GetNext()) {
				$geo[] = $arProp['VALUE_XML_ID'];
			}
				
            $arAction['IMG'] = CFile::GetPath($action['PROPERTY_ACTION_IMG_VALUE']);
            $arAction['NAME'] = $action['NAME'];
            $arAction['URL'] = $action['DETAIL_PAGE_URL']; ;
            $arAction['ID'] = $action['ID'];
            $arAction['TEXT_STICKER'] = $action['PROPERTY_TEXT_STICKER_VALUE'];
            $arAction['BG_TEXT'] = $action['PROPERTY_BG_TEXT_VALUE']??"#9C27B0";
            $arAction['GEO_TARGETING'] = $geo;
            
            $arActions[] = $arAction;
        }
        foreach ($arActions as $arAction):?>
            <a
                href="<?=$arAction['URL']?>"
                class="product-card__sale-marker"
                style="background-color:<?=$arAction['BG_TEXT']?>;"
            >
                <?=$arAction['TEXT_STICKER']?>
            </a>
		<?endforeach;
    }
	\Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("item-sticker-{$id}");
	$dynamic = @ob_get_contents();ob_end_clean();
    $searches[] = '#STICKERS_'.$id.'#';
    $replaces[] = $dynamic;

    // price
	if($arPrice['SALE_PRICE'] > 0 && $arPrice['DEFAULT_PRICE'] > 0 && $arPrice['SALE_PRICE'] !== $arPrice['DEFAULT_PRICE']) {
		if($arPrice['SALE_PRICE'] < $arPrice['DEFAULT_PRICE']) {
			$dynamic = '<div class="price-row__cols" id="item-price-'.$id.'">
				<div class="product-card__price-col">
					<div class="product-card__grey">Цена онлайн</div>
					<div class="product-card__black">'.$arPrice['SALE_PRICE_PRINT'].'</div>
				</div>
				<div class="product-card__price-col">
					<div class="product-card__grey">Цена в магазине</div>
					<div class="product-card__common">'.$arPrice['PRICE_DEFAULT_PRINT'].'</div>
				</div>
			</div>';
		} else {
			$dynamic = '<div class="price-row__cols" id="item-price-'.$id.'">
                <div class="price-row__col">'.$arPrice['PRICE_DEFAULT_PRINT'].'</div>
				<div class="price-row__col product-card__price_red">'.$arPrice['SALE_PRICE_PRINT'].'</div>
			</div>';
		} 
	} else if($arPrice['PRICE_RRC'] > 0 && $arPrice['DEFAULT_PRICE'] > 0 && $arPrice['PRICE_RRC'] > $arPrice['DEFAULT_PRICE'] && 
			 ($location['CITY_ID'] != 19 || in_array($props['CML2_BRANDS_REF'], Constant::DOUBLE_PRICE_BRANDS))) {
		$dynamic = '<div class="price-row__cols" id="item-price-'.$id.'">
			<div class="price-fields__col">
				<div class="product-card__grey">С учетом бонусов</div>
				<div class="product-card__black">'.$arPrice['PRICE_DEFAULT_PRINT'].'</div>
			</div>
			<div class="product-card__price-col">
				<div class="product-card__grey">Обычная цена</div>
				<div class="product-card__common">'.$arPrice['PRICE_RRC_PRINT'].'</div>
			</div>
		</div>';
	} else if($arPrice['DEFAULT_PRICE'] > 1) {
		$dynamic = '<div class="price-row__cols" id="item-price-'.$id.'">
			<div class="price-fields__col">
				<div class="price-row__col">'.$arPrice['PRICE_DEFAULT_PRINT'].'</div>
			</div>
		</div>';
	} else {
		$dynamic = '<div class="price-fields__col" id="item-price-'.$id.'">
			<div class="price-fields__col">
				<div class="price-row__col">Цена не указана</div>
			</div>
		</div>';
	}
	
    ob_start();
    \Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("item-price-{$id}");
    echo $dynamic;
    \Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("item-price-{$id}", "", "item-price-{$id}", false, true, true);
    $dynamic = ob_get_contents(); ob_end_clean();
    $searches[] = '#PRICE_'.$id.'#';
    $replaces[] = $dynamic;
	
    // button basket
    if(in_array($id, $productsIDs)){
        $bClass = "bem-icon-link_cart-added";
        $text = "В корзине";
        $bOnClick = 'href="/personal/cart/"';
    }else {
        $bClass = "";
        $text = "В корзину";
        $bOnClick = "onclick=\"universal.product.addToBasketModal(this,".$id.", null, 'catalog', '{$addParam}'); yaCounter858663.reachGoal('addbasket'); return true;\"";
    }
    $dynamic = "
        <a class='bem-icon-link bem-icon-link_without-underline bem-icon-link_pseudo bem-icon-link_to-cart {$bClass}' {$bOnClick}>
            <span class='bem-icon-link__span b'>{$text}</span>
        </a>";
    $searches[] = '#BASKET'.$id.'#';
    $replaces[] = $dynamic;

    // compare
    $cHref = "/compare/";
    $cClick = "compare.toggle({$id}, 'catalog');  yaCounter858663.reachGoal('compare'); return false;";
    if(in_array($id, $compares)) {
        $cAddClass = "user-tool_added";
        $cModalText = "Удалить из сравнения";
    } else {
        $cClass = "";
        $cModalText = "Добавить в сравнение";
    }
    ob_start();
    $APPLICATION->IncludeFile('/local/templates/.default/inc/compareFavorite.php',
        Array(
            'type'         => 'compare',
            'class'        => 'compare',
            'idProduct'    => $id,
            'buttonAddDel' => $cModalText,
            'click'        => $cClick,
            'addedClass'   => $cAddClass,
            'link'         => $cHref,
            'buttonText'   => 'Перейти в сравнение',
            'isAuth'       => 'true'
        ),
        Array()
    );
    $compareHtmlTmp = @ob_get_contents();ob_end_clean();
    $searches[] = '#COMPARE'.$id.'#';
    $replaces[] = $compareHtmlTmp;

    // favorite
    $userId = $USER->GetID() > 0 ? $USER->GetID() : 0;
    $modalAuth = (!$auth) ? "data-modal-name = 'sign-in'" : "";
    $fHref = "/personal/favorite/";
    $fModalText = 'Перейти в избранные';
    $fClick = "userFavorite.toggle({$id}, 'catalog'); return true;";
    if(in_array($id, $favorites)){
        $fAddClass = "user-tool_added";
        $fButtAddDel = "Удалить из избранного";
    } elseif ($userId) {
        $fAddClass = "";
        $fClass = "";
        $fButtAddDel = "Добавить в избранное";
    } else {
        $fClass = "";
        $fClick = "";
        $fButtAddDel = "Требуется авторизация";
        $fModalText = 'Требуется авторизация';
        $fHref = "#";
    }
    ob_start();
    $APPLICATION->IncludeFile('/local/templates/.default/inc/compareFavorite.php',
        Array(
            'type'          => 'favorite',
            'class'         => 'favorite',
            'idProduct'     => $id,
            'buttonAddDel'  => $fButtAddDel,
            'DataModalAuth' => $modalAuth,
            'click'         => $fClick,
            'addedClass'    => $fAddClass,
            'link'         	=> $fHref,
            'buttonText'    => $fModalText,
            'isAuth'        => $auth,
        ),
        Array()
    );
    $favoriteHtmlTmp = @ob_get_contents();ob_end_clean();
    $searches[] = '#FAVORITE_'.$id.'#';
    $replaces[] = $favoriteHtmlTmp;

    ob_start();
    \Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("item-status-{$id}");
    $storeStatus = $arProductStoreInfo[$id]['PARENT_QUANTITY_STATUS'] ?: $arProductStoreInfo[$id]['QUANTITY_STATUS'];
    if (!empty($storeStatus['TEXT'])):
        if ($storeStatus['CODE'] == 'UNDER_ORDER'):
        ?>
            <span
                title="<?=$storeStatus['TEXT']?>"
                data-product-id="<?=$id?>"
                class="
                    product-card__status_delivery p p_fz12
                    bem-modal-show
                    show-store-info__js"
                data-modal-name="product-availability"
            >
                <?=$storeStatus['TEXT']?>
            </span>
        <?php
        else:
        ?>
            <span
                data-product-id="<?=$id?>"
                class="product-card__status_available p p_fz12 <?=$storeStatus['CLASS']?> bem-modal-show show-store-info__js"
                data-modal-name="product-availability"
            >
                <?=$storeStatus['TEXT']?>
            </span>
        <?php
        endif;
    endif;
    \Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("item-status-{$id}");
    $dynamic = @ob_get_contents();ob_end_clean();
    $searches[] = '#STATUS'.$id.'#';
    $replaces[] = $dynamic;
}
unset($dynamic);
echo str_replace($searches, $replaces, $content);
