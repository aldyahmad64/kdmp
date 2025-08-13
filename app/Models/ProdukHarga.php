<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProdukHarga extends Model
{
    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function tipeHarga(): BelongsTo
    {
        return $this->belongsTo(TipeHarga::class);
    }
}
