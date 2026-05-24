<?php

namespace App\Services\EventServices;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface EventServiceInterface
{
    public function getEvents(Request $request): LengthAwarePaginator;
    public function getEventStats(): array;
    public function getEventById($id);
    public function getEventBySlug($slug);
    public function createEvent(array $data);
    public function updateEvent($id, array $data);
    public function deleteEvent($id);
    public function getCoordinatesFromAddress(string $address): ?array;
    public function getUpcomingEvents(int $limit = 5);
    public function getOngoingEvents(int $limit = 5);
    public function getHistoryEvents(int $limit = 5);
    public function getTopEvents(int $limit = 5);
}