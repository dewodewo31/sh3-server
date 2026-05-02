<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class GeocodingHelper
{
    /**
     * Konversi alamat ke koordinat menggunakan Nominatim (OpenStreetMap) - GRATIS
     */
    public static function getCoordinates($address)
    {
        if (empty($address)) {
            return null;
        }
        
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'SH3-Event-App/1.0'
            ])->get('https://nominatim.openstreetmap.org/search', [
                'q' => $address,
                'format' => 'json',
                'limit' => 1,
                'addressdetails' => 1
            ]);
            
            if ($response->successful() && count($response->json()) > 0) {
                $data = $response->json()[0];
                return [
                    'lat' => (float) $data['lat'],
                    'lng' => (float) $data['lon'],
                    'display_name' => $data['display_name']
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Geocoding error: ' . $e->getMessage());
        }
        
        return null;
    }
    
    /**
     * Ambil koordinat default (Indonesia)
     */
    public static function getDefaultCoordinates()
    {
        return [
            'lat' => -6.2088,  // Jakarta
            'lng' => 106.8456
        ];
    }
}