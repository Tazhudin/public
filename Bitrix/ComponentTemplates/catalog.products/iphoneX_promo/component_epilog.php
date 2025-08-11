<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

use Dev05\Classes\Constant;
use Dev05\Classes\GlobalFactory;

global $USER, $idPrice;
$productIDs = \Dev05\Classes\GlobalFactory::getInstance()->getBasket()->getProductIds();

// Вывод некэшируемого содержимого
$content = $arResult['CACHED_TPL'];

$favorites = FavoriteProducts::getUserFavorites($USER->GetID());

// Данные текущего местоположения
$location = \Dev05\Classes\GlobalFactory::getInstance()->getCurrentLocation()->getData();


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

$arProductStoreInfo = (new \Dev05\Classes\Store(array_keys($arResult['ITEMS_ID']), false, ['SHOW_FEW_STATUS' => false]))->get();

/*likes-begin*/
if ($auth){
    $likesHL = hl(13);
    
    $likesDb = $likesHL::getList(array(
        'select' => array('UF_PRODUCT_ID', 'UF_LIKE'),
        'filter' => array('UF_PRODUCT_ID' => $arResult['ITEMS_ID'], 'UF_USER_ID' => $USER->GetID())
    ));
    
    $setLikes = [];
    while ($el = $likesDb->fetch()){
        $setLikes[ $el['UF_PRODUCT_ID'] ] = $el['UF_LIKE'];
    }
    
    $likeStr = ' onclick="five.product.rate(this, \'#LIKE#\', #ID#, \'catalog\');"';
}else {
    $likeStr = " data-modal='enter'";
}
/*likes-end*/

/*compares*/
$compares = GlobalFactory::getInstance()->getCompare()->getList();
/*compares*/

$replaces = [];
$searches = [];

ob_start();

\Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("extra");
if ($arParams['SHOW_EXTRA'] == 'Y'){
    
    include 'inc/banner.php';
    include 'inc/additionalFiltres.php';
    
}else {
    echo " ";
}
\Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("extra", "", "extra", false, true, true);

$searches[] = '#EXTRA#';
$replaces[] = ob_get_contents();
ob_end_clean();


ob_start();
if($location['REGION_ID'] != 37 && $arResult['WEIGHT'] >= 20):?>
<a href="javascript:void(0);" style="background-color:#c9e4af;" class="catalog_banner">
    <img src="/images/noDel.png">
</a> 
<?endif;
$searches[] = '#NODEL#';
$replaces[] = ob_get_contents();
ob_end_clean();



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
    

    
    // likes
    
    if ($auth){
        if (isset($setLikes[ $id ])){
            if ($setLikes[ $id ] == 0){
                $classLike = '';
                $classDislike = " catalog-item__rating-down_is-active";
            }elseif ($setLikes[ $id ] == 1){
                $classDislike = '';
                $classLike = " catalog-item__rating-up_is-active";
            }
        }else {
            $classLike = '';
            $classDislike = "";
        }
        
        $dynamic = "<a id='dislike_".$id."' href='javascript:void(0)' class='catalog-item__rating-down{$classDislike}'".str_replace(['#ID#', '#LIKE#'], [$id, 'dislike'], $likeStr).">".$props['CML2_DISLIKES']."</a>";
        $dynamic .= "<a id='like_".$id."' href='javascript:void(0)' class='catalog-item__rating-up{$classLike}'".str_replace(['#ID#', '#LIKE#'], [$id, 'like'], $likeStr).">".$props['CML2_LIKES']."</a>";
        
    }else {
        $dynamic = "<a id='dislike_".$id."' href='javascript:void(0)' class='catalog-item__rating-down'".$likeStr.">".$props['CML2_DISLIKES']."</a>";
        $dynamic .= "<a id='like_".$id."' href='javascript:void(0)' class='catalog-item__rating-up'".$likeStr.">".$props['CML2_LIKES']."</a>";
    }
    
    $searches[] = '#LIKE_'.$id.'#';
    $replaces[] = $dynamic;
    

    
    // stickers
	
	ob_start();
	\Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("item-sticker-{$id}");
    // stickers of sales

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
        /**/
        
        foreach ($arActions as $arAction):?>
			<a href="<?=$arAction['URL']?>" class="bem-marker sale-markers__el" style="background-color:<?=$arAction['BG_TEXT']?>;"><?=$arAction['TEXT_STICKER']?></a>
		<?endforeach;
    }
    
	\Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("item-sticker-{$id}");
	$dynamic = @ob_get_contents();ob_end_clean();
	
    $searches[] = '#STICKERS_'.$id.'#';
    $replaces[] = $dynamic;
	//banners
	

	
	if(!empty($arActions) && !empty($arActions[0]['IMG'])) {
		ob_start();
		\Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("item-banner-{$id}");?>
			<div class="catalog-item__banner-block">
				<a href="<?=$arActions[0]['URL']?>"  class="catalog-item__banner" title = "<?=$arActions[0]['NAME']?>">
					<img src="<?=$arActions[0]['IMG']?>">
				</a>
				<div class="catalog-item__banner-desc"><?=$arActions[0]['NAME']?></div>
			</div>
		<?
		\Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("item-banner-{$id}");
		$dynamic = @ob_get_contents();ob_end_clean();
		$searches[] = '#BANNERS_'.$id.'#';
		$replaces[] = $dynamic;
		
	} else {
		$searches[] = '#BANNERS_'.$id.'#';
		$replaces[] = '';
	}
	

    
	
    // price

	
	
	if($arPrice['SALE_PRICE'] > 0 && $arPrice['DEFAULT_PRICE'] > 0 && $arPrice['SALE_PRICE'] !== $arPrice['DEFAULT_PRICE']) {
		if($arPrice['SALE_PRICE'] < $arPrice['DEFAULT_PRICE']) {
			$dynamic = '<div class="price-fields price-fields_large product-card__price-fields " id="item-price-'.$id.'">
				<div class="price-fields__col">
					<div class="price-fields__prop">Цена онлайн</div>
					<div class="price-fields__value">'.$arPrice['SALE_PRICE_PRINT'].'</div>
				</div>
				<div class="price-fields__col price-fields__col_small">
					<div class="price-fields__prop">Цена в магазине</div>
					<div class="price-fields__value">'.$arPrice['PRICE_DEFAULT_PRINT'].'</div>
				</div>
			</div>';
		} else {
			$dynamic = '<div class="price-fields price-fields_small-crossed price-fields_large product-card__price-fields" id="item-price-'.$id.'">
				<div class="price-fields__col">
					<div class="price-fields__value">'.$arPrice['PRICE_DEFAULT_PRINT'].'</div>
				</div>
				<div class="price-fields__col price-fields__col_small">
					<div class="price-fields__prop">Cтарая цена</div>
					<div class="price-fields__value">'.$arPrice['SALE_PRICE_PRINT'].'</div>
				</div>
			</div>';
		} 
	} else if($arPrice['PRICE_RRC'] > 0 && $arPrice['DEFAULT_PRICE'] > 0 && $arPrice['PRICE_RRC'] > $arPrice['DEFAULT_PRICE'] && 
			 ($location['CITY_ID'] != 19 || in_array($props['CML2_BRANDS_REF'], Constant::DOUBLE_PRICE_BRANDS))) {
		$dynamic = '<div class="price-fields price-fields_large product-card__price-fields" id="item-price-'.$id.'">
			<div class="price-fields__col">
				<div class="price-fields__prop">С учетом бонусов</div>
				<div class="price-fields__value">'.$arPrice['PRICE_DEFAULT_PRINT'].'</div>
			</div>
			<div class="price-fields__col price-fields__col_small">
				<div class="price-fields__prop">Обычная цена</div>
				<div class="price-fields__value">'.$arPrice['PRICE_RRC_PRINT'].'</div>
			</div>
		</div>';
	} else if($arPrice['DEFAULT_PRICE'] > 1) {
		$dynamic = '<div class="price-fields price-fields_large product-card__price-fields" id="item-price-'.$id.'">
			<div class="price-fields__col">
				<div class="price-fields__value">'.$arPrice['PRICE_DEFAULT_PRINT'].'</div>
			</div>
		</div>';
	} else {
		$dynamic = '<div class="price-fields price-fields_large product-card__price-fields" id="item-price-'.$id.'">
			<div class="price-fields__col">
				<div class="price-fields__value">Цена не указана</div>
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

    $searches[] = '#BASKET'.$id.'#';// что ищем
    $replaces[] = $dynamic;// на что заменяем


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
    if(in_array($id, $prodsInFavorite)){
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
    ?>
        <div class="product-card__tool product-card__tool_availability">
            <?php
            if ($storeStatus['CODE'] == 'UNDER_ORDER'):
            ?>
                <span title="<?=$storeStatus['TEXT']?>" data-product-id="<?=$id?>" class="bem-icon-link bem-icon-link_pseudo bem-icon-link_delivery bem-icon-link_orange bem-modal-show p p_fz12 show-store-info__js" data-modal-name="product-availability">
                    <span  class="bem-icon-link__span"><?=$storeStatus['TEXT']?></span>
                </span>
            <?php
            else:
            ?>
                <span data-product-id="<?=$id?>" class="p p_fz12 bem-pseudo <?=$storeStatus['CLASS']?> bem-modal-show show-store-info__js" data-modal-name="product-availability"><?=$storeStatus['TEXT']?></span>
            <?php
            endif;
            ?>
        </div>
    <?php
    endif;

    \Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("item-status-{$id}");
    $dynamic = @ob_get_contents();ob_end_clean();
    $searches[] = '#STATUS'.$id.'#';
    $replaces[] = $dynamic;
}

unset($dynamic);
echo str_replace($searches, $replaces, $content);
?>