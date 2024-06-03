<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http; 
use App\Models\sales;
use App\Models\refunds;

class detailSale extends Model
{
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

    public function sales()
    {
        return $this->belongsTo(sales::class);
    }

    public function refunds()
    {
        return $this->belongsTo(refunds::class);
    }
}
