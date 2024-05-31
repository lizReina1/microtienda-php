<?php

namespace App\Services\Interface;

interface IDetailSalesService
{
    public function addDetailSale($saleId, array $data);
    public function getDetailSale($saleId, $detailId);
    public function updateDetailSale($detailId, array $data);
    public function deleteDetailSale($detailId);
}
