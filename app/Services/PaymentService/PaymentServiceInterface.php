<?php

namespace App\Services\PaymentService;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface PaymentServiceInterface
{
    public function getPayments(Request $request): LengthAwarePaginator;
    public function getPaymentStats(Request $request): array;
    public function getFilterData(): array;
    public function getPaymentById($id);
    public function createPayment(array $data);
    public function updatePayment($id, array $data);
    public function verifyPayment($id, array $data);
    public function deletePayment($id);
    public function authorizePayment($id): bool;
}