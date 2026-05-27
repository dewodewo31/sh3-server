<?php

namespace App\Services\GalleryService;

use Google\Client;
use Google\Service\Drive;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GoogleDriveService
{
    protected $client;
    protected $drive;
    
    public function __construct()
    {
        // Optional: Setup client jika menggunakan API
        // $this->client = new Client();
        // $this->client->setDeveloperKey(config('services.google.api_key'));
        // $this->drive = new Drive($this->client);
    }
    
    /**
     * Extract file ID from Google Drive URL
     * Support berbagai format URL:
     * - https://drive.google.com/file/d/FILE_ID/view
     * - https://drive.google.com/open?id=FILE_ID
     * - https://drive.google.com/uc?id=FILE_ID
     * - https://drive.google.com/d/FILE_ID
     */
    public function extractFileId($url): ?string
    {
        $patterns = [
            '/\/file\/d\/([a-zA-Z0-9_-]+)/',
            '/id=([a-zA-Z0-9_-]+)/',
            '/\/uc\?id=([a-zA-Z0-9_-]+)/',
            '/\/d\/([a-zA-Z0-9_-]+)/',
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }
        
        return null;
    }
    
    /**
     * Download file from Google Drive using direct download URL
     * (Untuk file publik tanpa perlu API key)
     */
    public function downloadFile($fileId, $fileName = null)
    {
        try {
            // URL download langsung untuk file publik
            $directDownloadUrl = "https://drive.google.com/uc?export=download&id={$fileId}";
            
            // Gunakan Guzzle atau file_get_contents
            $client = new \GuzzleHttp\Client([
                'verify' => false,
                'timeout' => 30
            ]);
            
            $response = $client->get($directDownloadUrl);
            
            if ($response->getStatusCode() !== 200) {
                return [
                    'success' => false,
                    'error' => 'Gagal mengunduh file. HTTP: ' . $response->getStatusCode()
                ];
            }
            
            // Detect mime type
            $contentType = $response->getHeaderLine('Content-Type');
            $extension = 'jpg';
            if (str_contains($contentType, 'png')) $extension = 'png';
            if (str_contains($contentType, 'jpeg')) $extension = 'jpg';
            if (str_contains($contentType, 'gif')) $extension = 'gif';
            if (str_contains($contentType, 'webp')) $extension = 'webp';
            
            // Generate file name
            if (!$fileName) {
                $fileName = 'drive_' . $fileId . '_' . time() . '.' . $extension;
            }
            
            // Save to storage
            $path = 'galleries/' . $fileName;
            Storage::disk('public')->put($path, $response->getBody());
            
            return [
                'success' => true,
                'path' => $path,
                'name' => $fileName,
                'mime_type' => $contentType
            ];
            
        } catch (\Exception $e) {
            \Log::error('Google Drive download error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Download multiple files from Google Drive links
     */
    public function downloadMultipleFiles(array $urls)
    {
        $results = [];
        
        foreach ($urls as $url) {
            $fileId = $this->extractFileId($url);
            if ($fileId) {
                $result = $this->downloadFile($fileId);
                if ($result['success']) {
                    $results[] = $result['path'];
                }
            }
        }
        
        return $results;
    }
}