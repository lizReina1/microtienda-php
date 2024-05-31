<?php

namespace App\Services\Implementation;

use App\Services\Interface\IRefundsService;
use App\Models\refunds;

class RefundsService implements IRefundsService
{
    public function registerRefund(array $data)
    {
        $refund = refunds::create($data);
        return $refund->id;
    }

    public function getRefund($id)
    {
        return refunds::findOrFail($id);
    }

    public function listRefunds(array $filters = [])
    {
        $query = refunds::query();
    
        return $query->get();
    }

    public function updateRefund($id, array $data)
    {
        $refund = refunds::findOrFail($id);
        $refund->update($data);

        return true;
    }

    public function deleteRefund($id)
    {
        $refund = refunds::findOrFail($id);
        $refund->delete();

        return true;
    }
}
