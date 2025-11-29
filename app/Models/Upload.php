<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    protected $fillable = [
        'file_name',
        'file_path',
        'total_products',
        'processed_products',
    ];

    // One upload has many products
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
