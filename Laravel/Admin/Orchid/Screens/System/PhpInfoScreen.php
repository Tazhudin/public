<?php

namespace Admin\Orchid\Screens\System;

use Admin\Orchid\Screens\Permission;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class PhpInfoScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return mixed[]
     */
    public function query(): array
    {
        ob_start();
        passthru('php -t /var/www/html/admin -S 127.0.0.1:8000 2> /dev/null & pid=$! && sleep 1 && curl -s 127.0.0.1:8000/php.php && kill -9 $pid');
        $phpinfo = str_replace('\'', '"', ob_get_clean());
        $revealEnv = ['DB_', 'PASSWORD', 'SECRET', '_KEY'];
        $phpinfo = preg_replace(array_map(
            fn($env) => "/(<tr><td class=\"e\">\S*{$env}\S*\s*<\/td><td class=\"v\">)([^<]+)(<\/td><\/tr>)/",
            $revealEnv
        ), '$1***$3', $phpinfo);
        return [
            'content' => "<iframe width='100%' height='700' srcdoc='$phpinfo'></iframe>",
        ];
    }

    /**
     * The name of the screen is displayed in the header.
     */
    public function name(): ?string
    {
        return 'Информация PhpInfo';
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
        return [
            Layout::view('card'),
        ];
    }
}
