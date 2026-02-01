<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

class WhatsAppService
{
    protected ?string $apiUrl;
    protected ?string $apiKey;
    protected ?string $senderNumber;
    protected bool $enabled;

    public function __construct()
    {
        $this->apiUrl = Setting::getValue('whatsapp_api_url', '');
        $this->apiKey = Setting::getValue('whatsapp_api_key', '');
        $this->senderNumber = Setting::getValue('whatsapp_sender_number', '');
        $this->enabled = (bool) Setting::getValue('whatsapp_enabled', false);
    }

    /**
     * Check if WhatsApp is configured and enabled
     */
    public function isEnabled(): bool
    {
        return $this->enabled && !empty($this->apiUrl) && !empty($this->apiKey) && !empty($this->senderNumber);
    }

    /**
     * Send WhatsApp message
     */
    public function sendMessage(string $phone, string $message): bool
    {
        if (!$this->isEnabled()) {
            Log::warning('WhatsApp notification disabled or not configured');
            return false;
        }

        try {
            // Format phone number
            $phone = $this->formatPhone($phone);

            // Send via API (format sesuai serverwa.hello-inv.com)
            $response = Http::post($this->apiUrl, [
                'api_key' => $this->apiKey,
                'sender' => $this->senderNumber,
                'number' => $phone,
                'message' => $message,
            ]);

            $result = $response->json();

            if ($response->successful() && ($result['status'] ?? false)) {
                Log::info('WhatsApp message sent', ['to' => $phone]);
                return true;
            }

            Log::warning('WhatsApp API error', [
                'to' => $phone,
                'response' => $result,
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('WhatsApp send error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send attendance reminder to user
     */
    public function sendAttendanceReminder($user, string $loginUrl): bool
    {
        $message = "Halo {$user->name} laporan live hari ini belum kami terima, bisa isi di link ini ya\n{$loginUrl}\nTerimakasih... ☺️";

        return $this->sendMessage($user->phone, $message);
    }

    /**
     * Send daily report to admin
     */
    public function sendDailyReport(string $adminPhone, string $reportMessage): bool
    {
        return $this->sendMessage($adminPhone, $reportMessage);
    }

    /**
     * Test API connection
     */
    public function testConnection(string $testPhone): array
    {
        if (!$this->enabled) {
            return ['success' => false, 'message' => 'WhatsApp tidak aktif'];
        }

        if (empty($this->apiUrl) || empty($this->apiKey) || empty($this->senderNumber)) {
            return ['success' => false, 'message' => 'Konfigurasi belum lengkap'];
        }

        try {
            $response = Http::post($this->apiUrl, [
                'api_key' => $this->apiKey,
                'sender' => $this->senderNumber,
                'number' => $this->formatPhone($testPhone),
                'message' => '✅ Test koneksi WhatsApp berhasil dari TikTok Live Manager',
            ]);

            $result = $response->json();

            return [
                'success' => $response->successful() && ($result['status'] ?? false),
                'message' => $result['message'] ?? ($response->successful() ? 'Berhasil' : 'Gagal'),
                'data' => $result,
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Format phone number to Indonesian format
     */
    protected function formatPhone(string $phone): string
    {
        // Remove spaces and dashes
        $phone = preg_replace('/[\s\-\(\)]/', '', $phone);

        // Convert 08xx to 628xx
        if (str_starts_with($phone, '08')) {
            $phone = '62' . substr($phone, 1);
        }

        // Handle +62
        if (str_starts_with($phone, '+62')) {
            $phone = substr($phone, 1);
        }

        // Add 62 if needed
        if (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}
