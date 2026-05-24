<?php

namespace App\Services\OrderService;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface OrderServiceInterface
{
    public function getOrders(Request $request): LengthAwarePaginator;
    public function getOrderById($id);
    public function getOrderStats(Request $request): array;
    public function getFilterData(): array;
    public function verifyPayment($orderId, array $data);
    public function updatePaymentProof($orderId, array $data);
    public function cancelOrder($orderId);
    public function deleteOrder($orderId);
    public function checkTicket(string $ticketCode): ?array;
    public function exportOrdersToCsv(Request $request);
    public function exportOrdersToPdf(Request $request);
    public function exportSingleOrderToPdf($orderId);
}