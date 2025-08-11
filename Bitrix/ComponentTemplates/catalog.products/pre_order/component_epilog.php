<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

use Dev05\Classes\Constant;
use Dev05\Classes\GlobalFactory;

global $USER, $idPrice;

// Вывод некэшируемого содержимого
$content = $arResult['CACHED_TPL'];

$favorites = FavoriteProducts::getUserFavorites($USER->GetID());

// Данные текущего местоположения
$location = \Dev05\Classes\GlobalFactory::getInstance()->getCurrentLocation()->getData();

$auth = $USER->IsAuthorized();

$arPrices = toGetPrice(array_keys($arResult['ITEMS_ID']));




/*compares*/
$compares = GlobalFactory::getInstance()->getCompare()->getList();
/*compares*/

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
            <a href="<?=$arAction['URL']?>" class="product-card__sale-marker" style="background-color:<?=$arAction['BG_TEXT']?>;"><?=$arAction['TEXT_STICKER']?></a>
		<?endforeach;
    }
    
	\Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("item-sticker-{$id}");
	$dynamic = @ob_get_contents();ob_end_clean();
	
    $searches[] = '#STICKERS_'.$id.'#';
    $replaces[] = $dynamic;

    $dynamic = '<div class="price-row__cols">
        <div class="product-card__price-col">
          <div class="product-card__black">'.$arPrice['PRICE_DEFAULT_PRINT'].'</div>
        </div>
    </div>';


    ob_start();
    \Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("item-price-{$id}");
    echo $dynamic;
    \Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("item-price-{$id}", "", "item-price-{$id}", false, true, true);
    $dynamic = ob_get_contents(); ob_end_clean();
    
    $searches[] = '#PRICE_'.$id.'#';
    $replaces[] = $dynamic;


    // BONUSES
    ob_start();
    \Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("item-bonuses-{$id}");

    $APPLICATION->IncludeFile(
        Constant::TMP_PATH . 'bonuses.php',
        [

            'price' => $arPrice['DEFAULT_PRICE'],
            'bonuses' => $arPrice['BONUS']
        ],
        []
    );

    \Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("item-bonuses-{$id}");
    $dynamic = @ob_get_contents();ob_end_clean();
    $searches[] = '#BONUSES_'.$id.'#';
    $replaces[] = $dynamic;




    // compare
    $cHref = "/compare/";
    $cClick = "compare.toggle({$id}, 'catalog');  yaCounter858663.reachGoal('compare'); return false;";
    if(in_array($id, $compares)) {
        $cAddClass = "user-tool_added";
        $cModalText = "Удалить из сравнения";
    } else {
        $cAddClass = "";
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
    $userFavorites = FavoriteProducts::create($userId)->getProducts();
    $modalAuth = (!$auth) ? "data-modal-name = 'sign-in'" : "";
    $fHref = "/personal/favorite/";
    $fModalText = 'Перейти в избранные';
    $fClick = "userFavorite.toggle({$id}, 'catalog'); return true;";
    if(in_array($id, $userFavorites)){
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
}

unset($dynamic);
echo str_replace($searches, $replaces, $content);
