<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_email',
        'momo_settlement_number',
        'data_protection_reg_no',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function admins(): HasMany
    {
        return $this->hasMany(Admin::class);
    }
}
