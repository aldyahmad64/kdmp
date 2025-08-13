<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use App\Models\Team;
use Filament\PanelProvider;
use Filament\Navigation\MenuItem;
use App\Filament\Auth\CustomLogin;
use Filament\Support\Colors\Color;
use Filament\Navigation\NavigationGroup;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('/')
            ->login(CustomLogin::class)
            ->colors(function () {
                return [
                    'primary' => Color::Blue,
                    'gray' => Color::Gray,
                    'info' => Color::Lime,
                    'success' => Color::Green,
                    'warning' => Color::Yellow,
                    'danger' => Color::Red,
                ];
            })
            ->spa(true)
            ->brandLogo(function () {
                $data = [
                    'web_name' => env('APP_NAME'),
                    'web_logo' => 'storage/img/web/logo.png'
                ];
                return view('filament.admin.logo', $data);
            })
            ->brandName(env('APP_NAME'))
            ->favicon('storage/img/web/logo.png')
            ->collapsibleNavigationGroups(true)
            ->sidebarFullyCollapsibleOnDesktop()
            ->sidebarWidth('20rem')
            ->maxContentWidth('full')
            ->breadcrumbs(false)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label(fn() => auth()->user()->name)
                    ->url(fn(): string => EditProfilePage::getUrl())
                    ->icon('heroicon-m-user-circle'),
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Master Data')
                    ->extraTopbarAttributes([
                        'x-data' => '{ collapsed: true }',
                        'x-init' => '$el.nextElementSibling.style.display = "none"',
                    ]),
                NavigationGroup::make()
                    ->label('Manajemen Sistem'),
                NavigationGroup::make()
                    ->label('Informasi Sistem')
                    ->collapsible(true)
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('15s')
            ->plugins([
                FilamentEditProfilePlugin::make()
                    ->slug('my-profile')
                    ->setTitle('My Profile')
                    ->setNavigationLabel('My Profile')
                    ->setNavigationGroup('Group Profile')
                    ->setIcon('heroicon-o-user')
                    ->shouldShowEmailForm(false)
                    ->shouldRegisterNavigation(false)
                    ->shouldShowDeleteAccountForm(false)
                    ->shouldShowBrowserSessionsForm()
                    ->shouldShowAvatarForm(
                        value: true,
                        directory: 'avatars', // image will be stored in 'storage/app/public/avatars
                        rules: 'mimes:jpeg,png|max:1024' //only accept jpeg and png files with a maximum size of 1MB
                    ),
                FilamentShieldPlugin::make(),
            ])
            ->tenant(Team::class, 'slug')
            ->tenantMenu(fn() => auth()->user()->canAny(['view_team']))
            ->tenantMiddleware([
                \App\Http\Middleware\SettingMiddleware::class,
                \BezhanSalleh\FilamentShield\Middleware\SyncShieldTenant::class,
            ], isPersistent: true);
    }
}
