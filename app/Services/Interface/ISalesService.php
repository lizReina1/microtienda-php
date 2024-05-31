<?php

namespace App\Services\Interface;

interface ISalesService
{
    public function registerSale(array $data);
    public function getSale($id);
    public function listSales(array $filters = []);
    public function updateSale($id, array $data);
    public function deleteSale($id);

}
