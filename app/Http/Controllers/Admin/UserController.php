<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SalaryScheme;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'user');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Filter by task
        if ($request->filled('task')) {
            $query->where('task', $request->task);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $users = $query->withSum('attendances', 'live_duration_minutes')
            ->withCount('attendances')
            ->orderBy('name')
            ->paginate(20);

        $tasks = User::where('role', 'user')
            ->distinct()
            ->pluck('task')
            ->filter();

        return view('admin.users.index', compact('users', 'tasks'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
            'task' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
            'task' => $validated['task'],
            'phone' => $validated['phone'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Create default salary scheme
        SalaryScheme::create([
            'user_id' => $user->id,
            'hourly_rate' => 25000,
            'content_edit_rate' => 15000,
            'content_live_rate' => 10000,
            'sales_bonus_percentage' => 0,
            'sales_bonus_nominal' => 5000,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $user->load([
            'attendances' => function ($query) {
                $query->orderByDesc('attendance_date')->limit(10);
            },
            'salaries' => function ($query) {
                $query->orderByDesc('period_start')->limit(5);
            },
            'salaryScheme'
        ]);

        $stats = [
            'total_live' => $user->attendances()->count(),
            'total_hours' => round($user->attendances()->sum('live_duration_minutes') / 60, 1),
            'total_salary' => $user->salaries()->sum('total_salary'),
        ];

        return view('admin.users.show', compact('user', 'stats'));
    }

    /**
     * Show the form for editing the user
     */
    public function edit(User $user)
    {
        $scheme = $user->salaryScheme ?? new SalaryScheme();

        $globalDefaults = [
            'daily_live_hours' => Setting::getValue('daily_live_hours', 3),
            'monthly_leave_days' => Setting::getValue('monthly_leave_days', 4),
            'bonus_pcs_threshold' => Setting::getValue('bonus_pcs_threshold', 20),
            'bonus_amount' => Setting::getValue('bonus_amount', 10000),
        ];

        return view('admin.users.edit', compact('user', 'scheme', 'globalDefaults'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'task' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'daily_live_hours' => 'nullable|numeric|min:0.5|max:24',
            'monthly_leave_days' => 'nullable|integer|min:0|max:15',
            'bonus_pcs_threshold' => 'nullable|integer|min:1',
            'bonus_amount' => 'nullable|numeric|min:0',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'task' => $validated['task'],
            'phone' => $validated['phone'] ?? null,
            'is_active' => $validated['is_active'] ?? false,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        // Update bonus scheme fields
        $bonusData = [];
        foreach (['daily_live_hours', 'monthly_leave_days', 'bonus_pcs_threshold', 'bonus_amount'] as $field) {
            $bonusData[$field] = (isset($validated[$field]) && $validated[$field] !== '' && $validated[$field] !== null)
                ? $validated[$field]
                : null;
        }

        SalaryScheme::updateOrCreate(
            ['user_id' => $user->id],
            $bonusData
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "User berhasil {$status}.");
    }
}
