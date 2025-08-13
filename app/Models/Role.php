<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Role extends SpatieRole
{

    public static function getValueRole($value = null)
    {
        $data = Role::where('name', $value)->first();
        return $data;
    }

    /** @return BelongsTo<\App\Models\Team, self> */
    public function team(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Team::class);
    }

    /** @return BelongsTo<\App\Models\Team, self> */
    public function teams(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Team::class);
    }

}
