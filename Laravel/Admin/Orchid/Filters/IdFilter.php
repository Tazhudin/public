<?php

namespace Admin\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Input;

class IdFilter extends Filter
{
    /**
     * The displayable name of the filter.
     *
     * @return string
     */
    public function name(): string
    {
        return __('Id');
    }

    /**
     * The array of matched parameters.
     *
     * @return array
     */
    public function parameters(): array
    {
        return ['id'];
    }

    /**
     * Apply to a given Eloquent query builder.
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function run(Builder $builder): Builder
    {
        $id = $this->request->get('id');
        if (!empty($id)) {
            $builder->where('id', $id);
        }

        return $builder;
    }

    /**
     * Get the display fields.
     */
    public function display(): array
    {
        return [
            Input::make('id')
                ->type('text')
                ->value($this->request->get('id'))
                ->title(__('ID'))
                ->placeholder(__('Введите ID для фильтрации')),
        ];
    }

    /**
     * Value to be displayed
     */
    public function value(): string
    {
        $id = $this->request->get('id');
        return $id ? __('ID: :id', ['id' => $id]) : '';
    }
}
