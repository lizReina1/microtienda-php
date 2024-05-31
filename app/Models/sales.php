<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Http; 
use App\Models\detailSale;

class sales extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'total',
        'payment_type',
        'quantity_items',
        'customer_id',
        'user_id',
    ];

    // Fetch customer details from Customer microservice
    public function getCustomerAttribute()
    {
        $customerServiceUrl = config('services.customer_service.url');
        $response = Http::get("$customerServiceUrl/customers/{$this->customer_id}");
        
        return $response->json();
    }

    // Fetch user details from User microservice
    public function getUserAttribute()
    {
        $userServiceUrl = config('services.user_service.url');
        $response = Http::get("$userServiceUrl/users/{$this->user_id}");
        
        return $response->json();
    }

    public function detailSales()
    {
        return $this->hasMany(detailSale::class, 'sale_id');
    }
}
