<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http; 
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleDetail extends Model
{   
    use HasFactory;
    protected $table = 'detail_sales';
    protected $fillable = [ 
        'quantity',
        'price', 
        'total',
        'sale_id', 
        'product_id'
    ];

    public function getCustomerAttribute()
    {
        $productServiceUrl = config('services.product_service.url');
        $response = Http::get("$productServiceUrl/products/{$this->product_id}");
        
        return $response->json();
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }
}
