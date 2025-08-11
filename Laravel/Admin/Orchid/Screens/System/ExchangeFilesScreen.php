<?php

namespace Admin\Orchid\Screens\System;

use Carbon\Carbon;
use Admin\Orchid\Screens\Permission;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Storage;
use Library\ValueObject\GetListOptions;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Layout;

class ExchangeFilesScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return mixed[]
     */
    public function query(): array
    {
        $storageDisk = Storage::disk(env('FILESYSTEM_DISK_PRIVATE', 's3private'));
        $allFiles = collect($storageDisk->allFiles('exchange'))
            ->sort(fn($a, $b) => preg_replace('/\D/', '', $b) <=> preg_replace('/\D/', '', $a));
        $getListOptions = GetListOptions::createFromRequest(request: request(), defaultSize: 13);

        return [
            'datarows' => new LengthAwarePaginator(
                items: $allFiles->forPage($getListOptions->getPage(), $getListOptions->limit),
                total: $allFiles->count(),
                perPage: $getListOptions->limit,
                currentPage: $getListOptions->getPage(),
                options: [
                    'path' => Paginator::resolveCurrentPath(),
                ]
            ),
        ];
    }

    /**
     * The name of the screen is displayed in the header.
     */
    public function name(): ?string
    {
        return 'Файлы данных обмена';
    }

    /**
     * The permissions required to access this screen.
     *
     * @return string[]|null
     */
    public function permission(): ?iterable
    {
        return [
            Permission::SYSTEM,
        ];
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): array
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): array
    {
        $storageDisk = Storage::disk(env('FILESYSTEM_DISK_PRIVATE', 's3private'));
        return [
            Layout::table('datarows', [
                TD::make('filename', 'Название файла')
                    ->render(fn(string $filename) => basename($filename)),
                TD::make('filesize', 'Размер файла')
                    ->render(fn(string $filename) => round($storageDisk->size($filename) / 1024, 1) . ' kB'),
                TD::make('created_at', 'Дата создания')
                    ->render(fn(string $filename)
                        => Carbon::parse(preg_replace('/\D/', '', $filename))->format('d.m.Y H:i:s')),

                TD::make('Действия')
                    ->cantHide()
                    ->width('100px')
                    ->render(fn($filename) => Link::make('Скачать')
                        ->icon('bs.download')
                        ->href($storageDisk->temporaryUrl(
                            $filename,
                            now()->addHour(),
                            ['ResponseContentDisposition' => 'attachment']
                        ))
                        ->download()),
            ]),
        ];
    }
}
