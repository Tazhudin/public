<?php

declare(strict_types=1);

namespace Dev05\Classes;

use Bitrix\Main\ORM\Data\DataManager;

/**
 * Bitrix Framework
 * @package Dev05
 * @subpackage Classes
 * @copyright 2003-2020 05.RU
 */

class Brand
{
    /** @var DataManager */
    private static $brandsEntity = null;

    private $id;
    private $data = null;

    /**
     * Brand constructor.
     * Set brands hl block id and get brands entity
     */
    public function __construct($brand)
    {
        self::init();
        $this->data = self::getInfo($brand);
        $this->id = $brand;
    }

    /**
     * Get info
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Brand constructor.
     * Set brands hl block id and get brands entity
     */
    private static function init()
    {
        if (self::$brandsEntity !== null) return;
        self::$brandsEntity = HighLoadBlock::toInitHighLoadBlock(Constant::HIGHLOADS()['BRANDS']);
    }

    /**
     * Brand getList.
     * @param $param
     * @return array
     */
    public static function getList(array $param = [])
    {
        self::init();
        return self::$brandsEntity::getList($param);
    }

    /**
     * Return brands info
     * @param integer $idBrand
     * @return array $arBrandInfo
     */
    public static function getInfo($idBrand): ?array
    {
        $rsData = self::getList([
            'select' => ['*'],
            'filter' => ['ID' => $idBrand],
            'order' =>  ['UF_SORT' => 'ASC'],
        ]);

        if ($arRes = $rsData->fetch()) {
            $arRes['UF_FILE'] = \CFile::GetPath($arRes['UF_FILE']);
            return $arRes;
        }

        return null;
    }

    /**
     * Return brand sections
     * @param integer $idBrand
     * @return array $arBrandSections
     */
    public function getSections(int $idBrand, string $xmlIdBrand): ?array
    {
        // собираем разделы  
        $dbBrandSections = \CIBlockSection::GetTreeList(
            ['IBLOCK_ID' => Constant::INFOBLOCKS()['CATALOG_1C'], 'ACTIVE' => 'Y', 'GLOBAL_ACTIVE' => 'Y', 'UF_BRANDS' => $idBrand],
            ['ID', 'NAME', 'IBLOCK_SECTION_ID', 'GLOBAL_ACTIVE', 'SECTION_PAGE_URL']
        );

        $sectionLinc = [];
        $arResBrands['ROOT'] = [];
        $sectionLinc[0] = &$arResBrands['ROOT'];
        $arSectionIds = []; // id разделов для картинок

        while ($arSection = $dbBrandSections->GetNext()) {
            $sectionLinc[intval($arSection['IBLOCK_SECTION_ID'])]['CHILD'][$arSection['NAME']] = $arSection;
            $sectionLinc[$arSection['ID']] = &$sectionLinc[intval($arSection['IBLOCK_SECTION_ID'])]['CHILD'][$arSection['NAME']];
            if (!empty($sectionLinc[$arSection['ID']])) $arSectionIds[] = $arSection['ID'];
        }

        $arSection = $arResBrands['ROOT']['CHILD'];
        unset($arResBrands['ROOT']['CHILD']);

        // фото для раздела
        $class = \Bitrix\Iblock\Iblock::wakeUp(Constant::INFOBLOCKS()['CATALOG_1C'])->getEntityDataClass();

        $iter = $class::getList([
            'order' => ['ORDER_NUM' => 'asc'],
            'select' => [
                'NAME',
                'ORDER_NUM' => 'CML2_ORDER_NUM.VALUE',
                'IBLOCK_SECTION_ID',
                new \Bitrix\Main\ORM\Fields\ExpressionField('IMAGE', 'MAX(%s)', 'DETAIL_PICTURE')
            ],
            'filter' => [
                '!CML2_ORDER_NUM.VALUE' => false,
                '=CML2_BRANDS_REF.VALUE' => $xmlIdBrand,
                '@IBLOCK_SECTION_ID' => $arSectionIds,
            ],
            'group' => ['IBLOCK_SECTION_ID']
        ]);

        while ($arRes = $iter->fetch()) {
            $arSectionPicture[$arRes['IBLOCK_SECTION_ID']] = \CFile::GetPath($arRes['IMAGE']);
        }

        foreach ($arSection as $sectionName => $section) {
            foreach ($section['CHILD'] as $subSectionsName => $subSections) {
                foreach ($arSectionPicture as $sectionId => $picture) {
                    if ($subSections['ID'] == $sectionId) {
                        $arSection[$sectionName]['CHILD'][$subSectionsName]['PICTURE'] = $picture;
                    }
                }
            }
        }
        return  $arSection;
    }

    /**
     * Return brand actions ids
     * @return array
     */
    public function getActionsIds(): ?array
    {
        $dbBrandActions = \CIBlockElement::GetList(
            [],
            ['ACTIVE' => 'Y', 'IBLOCK_ID' => Constant::INFOBLOCKS()['CATALOG_1C'], 'PROPERTY_CML2_BRANDS_REF' => $this->data['UF_XML_ID'], '!PROPERTY_CML2_PROMOLINK' => false],
            ['PROPERTY_CML2_PROMOLINK'],
            false,
            []
        );

        while ($arRes = $dbBrandActions->GetNext()) {
            $arActionsIds[] = $arRes['PROPERTY_CML2_PROMOLINK_VALUE'];
        }

        return $arActionsIds;
    }

    /**
     * Return brands list
     * @param array|int @brandsXmlIds
     * @return array
     */
    public static function getListByXmlIds($brandsXmlIds): ?array
    {
        if (empty($brandsXmlIds)) {
            return null;
        }
        $tempBrands = self::getList([
            'filter' => [
                'LOGIC' => 'OR',
                'UF_XML_ID' => $brandsXmlIds,
                'UF_NAME' => $brandsXmlIds,
            ]
        ]);
        $brands = [];
        while ($brand = $tempBrands->fetch()) {
            $brand['UF_PROPERTY_CODE'] = 'PROPERTY_CML2_BRANDS_REF';
            $brand['UF_PROPERTY_VALUE'] = $brand['UF_XML_ID'];
            $brand['UF_PROPERTY_NAME'] = $brand['UF_NAME'];
            $brands[$brand['UF_XML_ID']] = $brand;
        }
        return $brands;
    }

    /**
     * Return brand promo
     * @param array|string @brandName
     * @return array
     */
    public function getPromoBrands(): ?array
    {
        // получение элементов инфоблока
        $dbItems = \CIBlockElement::GetList(
            [],
            ['IBLOCK_ID' => Constant::INFOBLOCKS()['PROMO_BRANDS'], 'PROPERTY_BRAND' => $this->data['UF_XML_ID']],
            false,
            false,
            ['IBLOCK_ID', 'ID', 'NAME', 'IBLOCK_SECTION_ID', 'PROPERTY_BRAND']
        );

        $elementIds = [];
        $arRes = [];
        while ($arItems = $dbItems->Fetch()) {
            $arRes[$arItems['IBLOCK_SECTION_ID']] = $arItems;
            $elementIds[] = (int) $arItems['ID'];
        }

        // получение свойств 
        $products = [];
        \CIBlockElement::GetPropertyValuesArray($products, Constant::INFOBLOCKS()['PROMO_BRANDS'], ['ID' => $elementIds]);

        $pictures = [];
        $mapPictureToProduct = [];
        foreach ($products as $id => $props) {
            if (!empty($props['PICTURE']['VALUE']) || !empty($props['PICTURE_DESCRIPTION']['VALUE'])) {
                foreach ($props['PICTURE']['VALUE'] as $index => $file) {
                    $pictures[$file] = $file;
                    $mapPictureToProduct[$file] = [$id, $index];
                }
                foreach ($props['PICTURE_DESCRIPTION']['VALUE'] as $index => $file) {
                    $pictures[$file] = $file;
                    $mapPictureDescriptionToProduct[$file] = [$id, $index];
                }
            }
        }

        $dbIter = \Bitrix\Main\FileTable::getList([
            'filter' => [
                '=ID' => $pictures,
            ],
        ]);

        while ($file = $dbIter->fetch()) {
            list($productId, $index) = $mapPictureToProduct[$file['ID']];
            $products[$productId]['PICTURE']['VALUE'][$index] = \CFile::GetFileSRC($file);
            if (!empty($products[$productId]['PICTURE_DESCRIPTION']['VALUE'])) {
                list($productId, $index) = $mapPictureDescriptionToProduct[$file['ID']];
                $products[$productId]['PICTURE_DESCRIPTION']['VALUE'][$index] = \CFile::GetFileSRC($file);
            }
        }
        unset($pictures, $mapPictureToProduct, $file, $dbIter);

        // формирование и заполнение разделов
        $arFilter = ['IBLOCK_ID' => Constant::INFOBLOCKS()['PROMO_BRANDS']];
        $arSelect = ['IBLOCK_ID', 'ID', 'NAME', 'DEPTH_LEVEL', 'IBLOCK_SECTION_ID', 'CODE'];
        $rsSections = \CIBlockSection::GetTreeList($arFilter, $arSelect);
        $sectionLinc = [];
        $arResBrands['ROOT'] = [];
        $sectionLinc[0] = &$arResBrands['ROOT'];

        while ($arSection = $rsSections->GetNext()) {
            $sectionLinc[intval($arSection['IBLOCK_SECTION_ID'])]['CHILD'][$arSection['CODE']] = $arSection;
            $sectionLinc[$arSection['ID']] = &$sectionLinc[intval($arSection['IBLOCK_SECTION_ID'])]['CHILD'][$arSection['CODE']];
            foreach ($arRes as $sectionId => $items) {
                foreach ($products[$items['ID']] as $propertyName => $property) {
                    if (!empty($property['VALUE']) && $propertyName !== 'BRAND') {
                        $items[$propertyName] = $property['VALUE'];
                    }
                }
                if ($sectionId == $arSection['ID']) {
                    $items['SECTION_NAME'] = $arSection['NAME'];
                    $sectionLinc[intval($arSection['IBLOCK_SECTION_ID'])]['CHILD'][$arSection['CODE']] = $items;
                }
            }
        }
        unset($sectionLinc, $product, $items);

        return $arResBrands['ROOT']['CHILD'];
    }
}
