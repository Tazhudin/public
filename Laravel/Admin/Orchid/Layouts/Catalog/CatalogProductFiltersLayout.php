<?php

namespace Admin\Orchid\Layouts\Catalog;

use Admin\Orchid\Filters\IdFilter;
use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;

class CatalogProductFiltersLayout extends Selection
{
    /**
     * @return string[]|Filter[]
     */
    public function filters(): array
    {
        return [
            IdFilter::class,
        ];
    }
}
