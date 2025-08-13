<?php

use Illuminate\Support\Facades\Route;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use App\Models\User;


// CONTOH DIRECT

//         Notification::make()
//             ->title('TEST NOTIFIKASI')
//             ->body('Ini adalah notifikasi test yang dikirim dari headerActions.')
//             ->success()
//             ->sendToDatabase(auth()->user()); // bisa juga ke semua user

// CONTOH ACTION

// Action::make('testNotification')
//     ->label('Kirim Notifikasi Test')
//     ->color('success')
//     ->icon('heroicon-o-bell')
//     ->action(function () {
//         Notification::make()
//             ->title('TEST NOTIFIKASI')
//             ->body('Ini adalah notifikasi test yang dikirim dari headerActions.')
//             ->success()
//             ->sendToDatabase(auth()->user()); // bisa juga ke semua user
//     }),
