<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalaryScheme;
use App\Models\User;
use Illuminate\Http\Request;

class SalarySchemeController extends Controller
{
    /**
     * Display a listing of salary schemes
     */
    public function index()
    {
        $users = User::where('role', 'user')
            ->with('salaryScheme')
            ->orderBy('name')
            ->get();

        return view('admin.salary-schemes.index', compact('users'));
    }

    /**
     * Show the form for editing the salary scheme
     */
    public function edit(User $user)
    {
        $scheme = $user->salaryScheme ?? new SalaryScheme();
        return view('admin.salary-schemes.edit', compact('user', 'scheme'));
    }

    /**
     * Update the specified salary scheme
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'hourly_rate' => 'required|numeric|min:0',
            'content_edit_rate' => 'required|numeric|min:0',
            'content_live_rate' => 'required|numeric|min:0',
            'monthly_target_hours' => 'required|numeric|min:0|max:744',
        ]);

        SalaryScheme::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return redirect()->route('admin.salary-schemes.index')
            ->with('success', 'Skema gaji berhasil diperbarui.');
    }

    /**
     * Apply salary scheme to multiple users
     */
    public function applyToAll(Request $request)
    {
        $validated = $request->validate([
            'hourly_rate' => 'required|numeric|min:0',
            'content_edit_rate' => 'required|numeric|min:0',
            'content_live_rate' => 'required|numeric|min:0',
            'sales_bonus_percentage' => 'required|numeric|min:0|max:100',
            'sales_bonus_nominal' => 'required|numeric|min:0',
        ]);

        $users = User::where('role', 'user')->get();

        foreach ($users as $user) {
            SalaryScheme::updateOrCreate(
                ['user_id' => $user->id],
                $validated
            );
        }

        return redirect()->route('admin.salary-schemes.index')
            ->with('success', 'Skema gaji berhasil diterapkan ke semua user.');
    }
}
