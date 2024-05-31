<?php

namespace App\Services\Implementation;

use App\Services\Interface\ISalesService;
use App\Models\saledetail;
use App\Models\sales;

class SalesService implements ISalesService
{ 
    public function registerSale(array $data)
    {
        $sale = sales::create($data);
        return $sale->id;
    }

    public function getSale($id)
    {
        return sales::findOrFail($id);
    }


    public function listSales(array $filters = [])
    {
        $query = sales::query();
        $sales = $query->get();
        return $sales;
    }

    public function updateSale($id, array $data)
    {
        $sale = sales::findOrFail($id);
        $sale->update($data);
        return true;
    }

    public function deleteSale($id)
    {
        $sale = sales::findOrFail($id);
        $sale->delete();
        return true;
    }

}
