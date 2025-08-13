<?php

namespace App\Models;

use App\LogsActivity;
use Filament\Facades\Filament;
use Filament\Support\Colors\Color;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    //
    use HasRoles, LogsActivity;

    public static function booted(): void
    {
        static::addGlobalScope('team', function (Builder $query) {
            if (auth()->hasUser()) {
                $query->where('team_id', Filament::getTenant()?->id);
            }
        });

        static::deleted(function (Setting $data) {
            // Jika tipe gambar dan file bukan default/logo
            if ($data->type === 'gambar' && !empty($data->value) && $data->value !== 'img/web/logo.png') {
                // Hapus file dari disk public
                Storage::disk('public')->delete($data->value);
            }

            if ($data->type == 'richtext') {
                self::deleteImage($data);
            }
        });

        self::updating(function (Setting $data) {
            if ($data->type == 'gambar' && $data->isDirty('value')) {
                $old = $data->getOriginal('value');

                if ($old) {
                    // Hilangkan prefix storage URL jika ada
                    $relativePath = str_replace('/storage/', '', parse_url($old, PHP_URL_PATH));

                    if ($relativePath !== 'img/web/logo.png' && Storage::disk('public')->exists($relativePath)) {
                        Storage::disk('public')->delete($relativePath);
                    }
                }
            }
            if ($data->type == 'richtext') {
                self::updateImage($data);
            }
        });
    }

    protected static function deleteImage($data)
    {
        $oldContent = $data->getOriginal('value');
        $oldImages = self::extractImagePaths($oldContent);
        foreach ($oldImages as $imagePath) {
            // pastikan path aman
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
        }
    }

    protected static function updateImage($data)
    {
        $oldContent = $data->getOriginal('value');
        $newContent = $data->value;
        $oldImages = self::extractImagePaths($oldContent);
        $newImages = self::extractImagePaths($newContent);
        $deletedImages = array_diff($oldImages, $newImages);
        foreach ($deletedImages as $imagePath) {
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
        }
    }

    protected static function extractImagePaths($html)
    {
        if (!$html)
            return [];
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $images = $dom->getElementsByTagName('img');
        $paths = [];
        foreach ($images as $img) {
            if ($img instanceof \DOMElement) {
                $src = $img->getAttribute('src');
                if (str_contains($src, '/storage/')) {
                    $paths[] = str_replace('/storage/', '', parse_url($src, PHP_URL_PATH));
                }
            }
        }

        return $paths;
    }

    public static function getWebName()
    {
        $data = Setting::where('key', 'web_name')->first();
        return $data?->value;
    }

    public static function getSpa()
    {
        $data = Setting::where('key', 'web_spa')->first();
        return $data?->value;
    }

    public static function getLogo()
    {
        $data = Setting::where('key', 'web_logo')->first();
        return $data?->value;
    }

    public static function getFormSlide()
    {
        $data = Setting::where('key', 'form_slide')->first();
        return $data?->value;
    }

    public static function getColor()
    {
        $keys = [
            'primary' => 'admin_warna_primary',
            'gray' => 'admin_warna_gray',
            'info' => 'admin_warna_info',
            'success' => 'admin_warna_success',
            'warning' => 'admin_warna_warning',
            'danger' => 'admin_warna_danger',
        ];

        $defaults = [
            'primary' => Color::Blue,
            'gray' => Color::Gray,
            'info' => Color::Lime,
            'success' => Color::Green,
            'warning' => Color::Yellow,
            'danger' => Color::Red,
        ];

        $colors = [];

        foreach ($keys as $name => $key) {
            $setting = Setting::where('key', $key)->first();
            $colors[$name] = $setting?->value ?? $defaults[$name];
        }

        return $colors;
    }

    /** @return BelongsTo<\App\Models\Team, self> */
    public function team(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Team::class);
    }

}
