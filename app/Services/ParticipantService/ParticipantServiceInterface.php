<?php

namespace App\Services\ParticipantService;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface ParticipantServiceInterface
{
    public function getParticipants(Request $request): LengthAwarePaginator;
    public function getParticipantStats(): array;
    public function getParticipantById($id);
    public function getParticipantWithDetails($id);
    public function createParticipant(array $data);
    public function updateParticipant($id, array $data);
    public function deleteParticipant($id);
    public function regenerateHashId($id);
    public function toggleStatus($id);
    public function exportParticipantsToCsv();
    public function exportParticipantsToPdf(Request $request);
    public function exportSingleParticipantToPdf($id);
    public function searchParticipants(Request $request): LengthAwarePaginator;
}