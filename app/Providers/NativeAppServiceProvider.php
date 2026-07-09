<?php

namespace App\Providers;

use Native\Laravel\Facades\Window;
use Native\Laravel\Facades\MenuBar;
use Native\Laravel\Contracts\ProvidesPhpIni;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
       // Window::open();
       MenuBar::create()
            ->icon(public_path('vendor/nativephp/bin/icon.png'))
            ->withContextMenu(
                \Native\Laravel\Menu\Menu::new()
                    ->label('Open App', fn() => Window::open('main'))
                    ->separator()
                    ->quit()
            );
        
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [
        ];
    }
}
