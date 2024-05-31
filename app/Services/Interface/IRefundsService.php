<?php

namespace App\Services\Interface;

interface IRefundsService
{
    public function registerRefund(array $data);
    public function getRefund($id);
    public function listRefunds(array $filters = []);
    public function updateRefund($id, array $data);
    public function deleteRefund($id);
}
