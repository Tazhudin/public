<?php
namespace Dev05\Classes\ProductsCollection;

class SelectionProducts
{
    private $selectionProductsProvider;
    public function __construct(ISelectionProductsProvider $selectionProductsProvider)
    {
        $this->selectionProductsProvider = $selectionProductsProvider;
    }
    public function getProducts(array $arParams) {
        return $this->selectionProductsProvider->getProducts($arParams);
    }
}