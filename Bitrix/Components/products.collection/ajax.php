<?php

use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\ActionFilter;
use Dev05\Classes\Constant;
use Dev05\Classes\ProductsCollection\SelectionProducts;


class ProductsCollectionAjaxController extends Controller
{
    /**
     * @behavior ajax request handler
     * @param array $ajaxParams
     */
    public function ajaxRequestHandlerAction( array $ajaxParams )
    {
        global $APPLICATION;
        ob_start();
            $APPLICATION->IncludeComponent(
                '05:products.collection',
                $ajaxParams['COMPONENT_TEMPLATE'],
                array(
                    'COLLECTION_TYPE' => $ajaxParams['GET_COLLECTION_TYPE'],
                    'IBLOCK_ID' => $ajaxParams['IBLOCK_ID'],
                    'SECTION_ID' => $ajaxParams['SECTION_ID'],
                    'ELEMENTS_COUNT' => $ajaxParams['ELEMENTS_COUNT'],
                    'AJAX_MODE' => 'N',
                    'IBLOCK_TYPE' => 'catalog',
                    'DISPLAY_TYPE' => 'SINGLE',
                    'LIMIT' => '50',
                    'BUYING_NOW_LIMIT_IN_ORDERS' => $ajaxParams['BUYING_NOW_LIMIT_IN_ORDERS]'],
                    'USE_FILTER_BY_SECTIONS' => 'Y',
                ),
                false
            );
        $tmp = ob_get_contents(); ob_end_clean();

        // необходимо вызвать execute() напрямую для замены placeholder-ов цен и иконок сравнения\избранное
        // поскольку оно на событии 'OnEndBufferContent' и при ajax запросе оно срабатывает после отправки ответа
        $bufferObj = \Dev05\Classes\GlobalFactory::getInstance()->getFrameBuffer();
        $bufferObj->execute($tmp);

        return ['HTML' => $tmp];
    }

    /**
     * @behavior configure request filters
     * @return array
     */
    public function configureActions(): array
    {
        return [
            'ajaxRequestHandler' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([ActionFilter\HttpMethod::METHOD_POST])
                ],
                'postfilters' => []
            ]
        ];
    }
}
