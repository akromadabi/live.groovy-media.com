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
            'marketplace_rate_mode' => Setting::getValue('marketplace_rate_mode', 'same'),
            'mpwa_api_url' => Setting::getValue('mpwa_api_url', ''),
            'mpwa_api_key' => Setting::getValue('mpwa_api_key', ''),
            'company_name' => Setting::getValue('company_name', 'TikTok Live Manager'),
            'app_name' => Setting::getValue('app_name', 'TikTok Live Manager'),
            'default_hourly_rate' => Setting::getValue('default_hourly_rate', 20000),
            'content_edit_rate' => Setting::getValue('content_edit_rate', 10000),
            'content_live_rate' => Setting::getValue('content_live_rate', 5000),
            'monthly_target_hours' => Setting::getValue('monthly_target_hours', 80),
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

            // Form & Policy Settings
            'attendance_proof_enabled' => Setting::getValue('attendance_proof_enabled', true),
            'attendance_proof_required' => Setting::getValue('attendance_proof_required', false),
            'attendance_vt_link_enabled' => Setting::getValue('attendance_vt_link_enabled', true),
            'attendance_vt_link_required' => Setting::getValue('attendance_vt_link_required', false),
            'attendance_deadline_enabled' => Setting::getValue('attendance_deadline_enabled', false),
            'attendance_deadline_days' => Setting::getValue('attendance_deadline_days', 1),
            'attendance_deadline_time' => Setting::getValue('attendance_deadline_time', '23:59'),
        ];

        $marketplaces = Setting::getMarketplaces();

        return view('admin.settings.index', compact('user', 'settings', 'marketplaces'));
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
        $group = $request->input('setting_group');

        if ($group === 'marketplaces') {
            $rateMode = $request->input('marketplace_rate_mode', 'same');
            Setting::setValue('marketplace_rate_mode', $rateMode);

            $inputMarketplaces = $request->input('marketplaces', []);
            $currentMarketplaces = Setting::getMarketplaces();

            // If uniform rate mode, sync global default_hourly_rate
            if ($rateMode === 'same' && $request->filled('default_hourly_rate')) {
                $globalRateRaw = $request->input('default_hourly_rate');
                $globalClean = is_string($globalRateRaw) ? (int) preg_replace('/[^\d]/', '', $globalRateRaw) : (int) $globalRateRaw;
                Setting::setValue('default_hourly_rate', max(0, $globalClean));

                foreach ($currentMarketplaces as $key => $item) {
                    $currentMarketplaces[$key]['default_hourly_rate'] = max(0, $globalClean);
                }
            }

            foreach ($currentMarketplaces as $key => $item) {
                $enabled = isset($inputMarketplaces[$key]['enabled']) && ($inputMarketplaces[$key]['enabled'] == '1' || $inputMarketplaces[$key]['enabled'] == 'on');
                
                if ($rateMode === 'different' && isset($inputMarketplaces[$key]['default_hourly_rate'])) {
                    $rateRaw = $inputMarketplaces[$key]['default_hourly_rate'];
                    $cleanRate = is_string($rateRaw) ? (int) preg_replace('/[^\d]/', '', $rateRaw) : (int) $rateRaw;
                    $currentMarketplaces[$key]['default_hourly_rate'] = max(0, $cleanRate);
                }

                $currentMarketplaces[$key]['enabled'] = $enabled;
            }

            // Ensure at least one marketplace remains active
            $hasActive = array_filter($currentMarketplaces, fn($m) => !empty($m['enabled']));
            if (empty($hasActive)) {
                $currentMarketplaces['tiktok']['enabled'] = true;
            }

            Setting::setValue('marketplaces', $currentMarketplaces, 'json');

            // Save global defaults for content rates and monthly target hours
            if ($request->has('content_edit_rate')) {
                $editRaw = $request->input('content_edit_rate');
                $cleanEdit = is_string($editRaw) ? (int) preg_replace('/[^\d]/', '', $editRaw) : (int) $editRaw;
                Setting::setValue('content_edit_rate', max(0, $cleanEdit));
            }
            if ($request->has('content_live_rate')) {
                $liveRaw = $request->input('content_live_rate');
                $cleanLive = is_string($liveRaw) ? (int) preg_replace('/[^\d]/', '', $liveRaw) : (int) $liveRaw;
                Setting::setValue('content_live_rate', max(0, $cleanLive));
            }
            if ($request->has('monthly_target_hours')) {
                $targetHours = (float) $request->input('monthly_target_hours');
                Setting::setValue('monthly_target_hours', max(0, $targetHours));
            }

            return back()->with('success', 'Pengaturan Platform & Kebijakan Upah berhasil disimpan.');
        }

        if ($group === 'attendance') {
            $validated = $request->validate([
                'attendance_proof_enabled' => 'nullable|boolean',
                'attendance_proof_required' => 'nullable|boolean',
                'attendance_vt_link_enabled' => 'nullable|boolean',
                'attendance_vt_link_required' => 'nullable|boolean',
                'attendance_deadline_enabled' => 'nullable|boolean',
                'attendance_deadline_days' => 'nullable|integer|min:0|max:30',
                'attendance_deadline_time' => 'nullable|string|max:10',
            ]);

            $validated['attendance_proof_enabled'] = $request->has('attendance_proof_enabled') ? 1 : 0;
            $validated['attendance_proof_required'] = $request->has('attendance_proof_required') ? 1 : 0;
            $validated['attendance_vt_link_enabled'] = $request->has('attendance_vt_link_enabled') ? 1 : 0;
            $validated['attendance_vt_link_required'] = $request->has('attendance_vt_link_required') ? 1 : 0;
            $validated['attendance_deadline_enabled'] = $request->has('attendance_deadline_enabled') ? 1 : 0;

            foreach ($validated as $key => $value) {
                Setting::setValue($key, $value ?? '');
            }

            return back()->with('success', 'Kebijakan formulir absensi berhasil disimpan.');
        }

        if ($group === 'whatsapp') {
            $validated = $request->validate([
                'whatsapp_api_url' => 'nullable|string|max:255',
                'whatsapp_api_key' => 'nullable|string|max:255',
                'whatsapp_sender_number' => 'nullable|string|max:30',
                'whatsapp_admin_phone' => 'nullable|string|max:30',
                'whatsapp_group_id' => 'nullable|string|max:50',
                'wa_reminder_time' => 'nullable|string|max:10',
                'wa_report_time' => 'nullable|string|max:10',
                'whatsapp_enabled' => 'nullable|boolean',
            ]);

            $validated['whatsapp_enabled'] = $request->has('whatsapp_enabled') ? 1 : 0;

            foreach ($validated as $key => $value) {
                Setting::setValue($key, $value ?? '');
            }

            return back()->with('success', 'Pengaturan WhatsApp berhasil disimpan.');
        }

        if ($group === 'app') {
            $validated = $request->validate([
                'app_name' => 'nullable|string|max:255',
            ]);

            foreach ($validated as $key => $value) {
                Setting::setValue($key, $value ?? '');
            }

            return back()->with('success', 'Pengaturan aplikasi berhasil disimpan.');
        }

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

            // Attendance settings
            'attendance_proof_enabled' => 'nullable|boolean',
            'attendance_proof_required' => 'nullable|boolean',
            'attendance_vt_link_enabled' => 'nullable|boolean',
            'attendance_vt_link_required' => 'nullable|boolean',
            'attendance_deadline_enabled' => 'nullable|boolean',
            'attendance_deadline_days' => 'nullable|integer|min:0|max:30',
            'attendance_deadline_time' => 'nullable|string|max:10',
        ]);

        // Handle checkboxes only if present or relevant
        if ($request->hasAny(['whatsapp_enabled', 'whatsapp_api_url'])) {
            $validated['whatsapp_enabled'] = $request->has('whatsapp_enabled') ? 1 : 0;
        }
        if ($request->hasAny(['attendance_proof_enabled', 'attendance_proof_required', 'attendance_vt_link_enabled', 'attendance_deadline_enabled', 'attendance_deadline_days'])) {
            $validated['attendance_proof_enabled'] = $request->has('attendance_proof_enabled') ? 1 : 0;
            $validated['attendance_proof_required'] = $request->has('attendance_proof_required') ? 1 : 0;
            $validated['attendance_vt_link_enabled'] = $request->has('attendance_vt_link_enabled') ? 1 : 0;
            $validated['attendance_vt_link_required'] = $request->has('attendance_vt_link_required') ? 1 : 0;
            $validated['attendance_deadline_enabled'] = $request->has('attendance_deadline_enabled') ? 1 : 0;
        }

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
