<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

use Dev05\Classes\Constant;

/**
 * @var CMain $APPLICATION
 * @var array $arResult
 * @var array $arParams
 */

global $USER, $idPrice;

$productIDs = \Dev05\Classes\GlobalFactory::getInstance()->getBasket()->getProductIds();
$favorites = FavoriteProducts::getUserFavorites($USER->GetID());

// Вывод некэшируемого содержимого
$content = $arResult['CACHED_TPL'];


// Данные текущего местоположения
$location = \Dev05\Classes\GlobalFactory::getInstance()->getCurrentLocation()->getData();


if($arParams['FROM'] == 'ACTIONS' && $arParams['IBLOCK_ID'] == PC_BUILD_IBLOCK_ID) {
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

$replaces = [];
$searches = [];

ob_start();

\Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("extra");
if ($arParams['SHOW_EXTRA'] == 'Y'){

    include 'inc/banner.php';
    include 'inc/additionalFiltres.php';

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

    } else {
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
            <div class="product-card__sale-markers-wrap">
                <a href="<?=$arAction['URL']?>" class="product-card__sale-marker" style="background-color:<?=$arAction['BG_TEXT']?>;"><?=$arAction['TEXT_STICKER']?></a>
            </div>
		<?endforeach;
    }

	\Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("item-sticker-{$id}");
	$dynamic = @ob_get_contents();ob_end_clean();

    $searches[] = '#STICKERS_'.$id.'#';
    $replaces[] = $dynamic;

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


    // button basket
    if (in_array($id, $productIDs)) {
        $bClass = "bem-icon-link_cart-added";
        $text = "В корзине";
        $bOnClick = "href=\"/personal/cart/\"";
    } else {
        $bClass = "";
        $text = "В корзину";
        $bOnClick = "onclick=\"universal.product.addToBasketModal(this,".$id.", null, 'catalog', '{$addParam}'); yaCounter858663.reachGoal('addbasket'); return true;\"";
    }


    $dynamicForDesktop = "
        <a class='bem-icon-link bem-icon-link_without-underline bem-icon-link_pseudo bem-icon-link_to-cart {$bClass}' data-type='desktop' {$bOnClick}>
            <span class='bem-icon-link__span b'>{$text}</span>
        </a>";
    $dynamicForMobile = "
        <a class='bem-icon-link bem-icon-link_without-underline bem-icon-link_pseudo bem-icon-link_to-cart {$bClass}' {$bOnClick}>
            <span class='bem-icon-link__span b'></span>
        </a>";

    $searches[] = '#BASKET'.$id.'#';// что ищем
    $replaces[] = $dynamicForDesktop;// на что заменяем

    $searches[] = '#MOBILEBASKET'.$id.'#';// что ищем
    $replaces[] = $dynamicForMobile;// на что заменяем

    ob_start();
    \Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("item-status-{$id}");

    $storeStatus = $arProductStoreInfo[$id]['PARENT_QUANTITY_STATUS'] ?: $arProductStoreInfo[$id]['QUANTITY_STATUS'];

    if (!empty($storeStatus['TEXT'])):
    ?>
        <div class="product-card__tool product-card__tool_availability">
            <?php
            if ($storeStatus['CODE'] == 'UNDER_ORDER'):
            ?>
                <span data-product-id="<?=$id?>" title="<?=$storeStatus['TEXT']?>" class="product-card__status product-card__status_on-request p p_fz12 bem-modal-show show-store-info__js" data-modal-name="product-availability">
                    <span  class="bem-icon-link__span"><?=$storeStatus['TEXT']?></span>
                </span>
            <?php
            elseif ($storeStatus['CODE'] == 'NOT_AVAILABLE'):
                ?>
                <span data-product-id="<?=$id?>" class="product-card__status product-card__status_unavailable p p_fz12 <?=$storeStatus['CLASS']?>"><?=$storeStatus['TEXT']?></span>
            <?php
            else:
            ?>
                <span data-product-id="<?= $id ?>"
                      class="product-card__status p p_fz12 <?= $storeStatus['CLASS'] ?> bem-modal-show show-store-info__js"
                      data-modal-name="product-availability"><?= $storeStatus['TEXT'] ?></span>
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

    $placeholders = $arResult['PLACEHOLDERS'][$id];

    $APPLICATION->IncludeComponent(
        '05:show.price',
        '.default',
        [
            'WHERE'       => 'SECTION',
            'PRODUCT_ID'  => $id,
            'SHOW_DELAY'  => 'Y',
            'PLACEHOLDER' => $placeholders['PRICE'],

            'AMOUNT_IN_PACK' => (int)$placeholders['AMOUNT_IN_PACK'],
        ],
        $this,
        ['HIDE_ICONS' => 'Y']
    );
    $APPLICATION->IncludeComponent(
        '05:execute.delay',
        'compare_icon',
        [
            'PRODUCT_ID' => $id,
            'PLACEHOLDER' => $placeholders['COMPARE'],
        ],
        $this,
        ['HIDE_ICONS' => 'Y']
    );
    $APPLICATION->IncludeComponent(
        '05:execute.delay',
        'favorite_icon',
        [
            'PRODUCT_ID'  => $id,
            'PLACEHOLDER' => $placeholders['FAVORITE'],
        ],
        $this,
        ['HIDE_ICONS' => 'Y']
    );
}

unset($dynamic);
echo str_replace($searches, $replaces, $content);
