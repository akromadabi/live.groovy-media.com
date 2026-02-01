<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    /**
     * Show settings page
     */
    public function index()
    {
        $user = Auth::user();
        $settings = [
            'mpwa_api_url' => Setting::getValue('mpwa_api_url', ''),
            'mpwa_api_key' => Setting::getValue('mpwa_api_key', ''),
            'company_name' => Setting::getValue('company_name', 'TikTok Live Manager'),
            'app_name' => Setting::getValue('app_name', 'TikTok Live Manager'),
            'default_hourly_rate' => Setting::getValue('default_hourly_rate', 15000),
            'bonus_pcs_threshold' => Setting::getValue('bonus_pcs_threshold', 20),
            'bonus_amount' => Setting::getValue('bonus_amount', 10000),
            'whatsapp_api_url' => Setting::getValue('whatsapp_api_url', ''),
            'whatsapp_api_key' => Setting::getValue('whatsapp_api_key', ''),
            'whatsapp_sender_number' => Setting::getValue('whatsapp_sender_number', ''),
            'whatsapp_admin_phone' => Setting::getValue('whatsapp_admin_phone', ''),
            'whatsapp_group_id' => Setting::getValue('whatsapp_group_id', ''),
            'wa_reminder_time' => Setting::getValue('wa_reminder_time', '18:00'),
            'wa_report_time' => Setting::getValue('wa_report_time', '21:00'),
            'whatsapp_enabled' => Setting::getValue('whatsapp_enabled', false),
        ];

        return view('admin.settings.index', compact('user', 'settings'));
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        $user->update(['password' => Hash::make($validated['password'])]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }

    /**
     * Update system settings
     */
    public function updateSystem(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'nullable|string|max:255',
            'default_hourly_rate' => 'nullable|numeric|min:0',
            'bonus_pcs_threshold' => 'nullable|integer|min:1',
            'bonus_amount' => 'nullable|numeric|min:0',
            'whatsapp_api_url' => 'nullable|string|max:255',
            'whatsapp_api_key' => 'nullable|string|max:255',
            'whatsapp_sender_number' => 'nullable|string|max:30',
            'whatsapp_admin_phone' => 'nullable|string|max:30',
            'whatsapp_group_id' => 'nullable|string|max:50',
            'wa_reminder_time' => 'nullable|string|max:10',
            'wa_report_time' => 'nullable|string|max:10',
            'whatsapp_enabled' => 'nullable|boolean',
        ]);

        // Handle checkbox
        $validated['whatsapp_enabled'] = $request->has('whatsapp_enabled') ? 1 : 0;

        foreach ($validated as $key => $value) {
            Setting::setValue($key, $value ?? '');
        }

        return back()->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }

    /**
     * Test WhatsApp connection
     */
    public function testWhatsApp(Request $request)
    {
        $waService = new \App\Services\WhatsAppService();
        $testPhone = Setting::getValue('whatsapp_admin_phone', '');

        if (empty($testPhone)) {
            return back()->with('error', 'Nomor admin belum diatur');
        }

        $result = $waService->testConnection($testPhone);

        if ($result['success']) {
            return back()->with('success', 'Test WA berhasil! Cek HP admin.');
        }

        return back()->with('error', 'Test WA gagal: ' . $result['message']);
    }
}
