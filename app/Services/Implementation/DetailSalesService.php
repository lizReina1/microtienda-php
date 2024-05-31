<?php

namespace App\Services\Implementation;

use App\Services\Interface\IDetailSalesService;
use App\Models\DetailSale;

class DetailSalesService implements IDetailSalesService
{
    public function addDetailSale($saleId, array $data)
    {
        $data['sale_id'] = $saleId;
        $detailSale = DetailSale::create($data);

        return $detailSale->id;
    }

    public function getDetailSale($saleId, $detailId)
    {
        return DetailSale::where('sale_id', $saleId)
            ->findOrFail($detailId);
    }


    public function updateDetailSale($detailId, array $data)
    {
        $detailSale = DetailSale::findOrFail($detailId);
        $detailSale->update($data);

        return true; // Indicador de éxito
    }

    public function deleteDetailSale($detailId)
    {
        $detailSale = DetailSale::findOrFail($detailId);
        $detailSale->delete();

        return true; // Indicador de éxito
    }
}
