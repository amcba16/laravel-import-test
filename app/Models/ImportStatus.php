<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportStatus extends Model
{
    protected $table = 'import_status'; // explicit table name

    protected $fillable = [
        'product_id',
        'status',
        'error_message',
    ];

    // An import status belongs to a product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
