<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ward extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'province_id', 'name', 'division_type', 'codename'];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }
}
