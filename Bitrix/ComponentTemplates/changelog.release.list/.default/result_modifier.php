<?php
$cdbResult = new CDBResult;
$cdbResult->NavPageCount = ceil($arResult['RELEASES']['RELEASES_CNT'] / $arParams['PAGE_ITEMS_COUNT']);
$cdbResult->NavPageNomer = $arParams['PAGE'];
$cdbResult->NavPageSize = $arParams['PAGE_ITEMS_COUNT'];
$cdbResult->NavRecordCount = count($arResult['RELEASES']);

$arResult['CDB_RESULT'] = $cdbResult;