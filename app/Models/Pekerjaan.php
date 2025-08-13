<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pekerjaan extends Model
{
    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class);
    }
}
