<?php

namespace App\Models;

use App\Models\Store;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mobile extends Model
{
    use HasFactory , SoftDeletes;

    protected $table = 'mobiles';
    protected $guarded = ['id'];

    public function getDatabaseName(): HasOne
    {
        return $this->hasOne(Store::class);
    }
}
