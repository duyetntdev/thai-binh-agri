<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Province extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'division_type', 'codename'];

    public function wards(): HasMany
    {
        return $this->hasMany(Ward::class);
    }
}
