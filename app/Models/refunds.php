<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Http; 

class refunds extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date', 
        'reason', 
        'quantity', 
        'customer_id',
    ];

    // Fetch customer details from Customer microservice
    public function getCustomerAttribute()
    {
        $customerServiceUrl = config('services.customer_service.url');
        $response = Http::get("$customerServiceUrl/customers/{$this->customer_id}");
        
        return $response->json();
    }

    public function detailSales()
    {
        return $this->hasMany(detailSale::class, 'sale_id');
    }
}
