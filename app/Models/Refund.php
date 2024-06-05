<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Http; 

class Refund extends Model
{
    use HasFactory;
    protected $table = 'refunds';
    protected $fillable = [
        'date', 
        'reason', 
        'quantity', 
        'customer_id',
        'detail_sale_id',
    ];

    // Fetch customer details from Customer microservice
    public function getCustomerAttribute()
    {
        $customerServiceUrl = config('services.customer_service.url');
        $response = Http::get("$customerServiceUrl/customers/{$this->customer_id}");
        
        return $response->json();
    }

    public function detailSale()
    {
        return $this->belongsTo(SaleDetail::class);
    }
}
