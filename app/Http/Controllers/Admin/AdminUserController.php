<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Centre;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    /**
     * Display a listing of system user accounts.
     */
    public function index(Request $request)
    {
        $query = User::with(['roles', 'assignedCentre']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('mobile', 'like', "%{$s}%");
            });
        }

        if ($request->filled('role')) {
            $query->where(function ($q) use ($request) {
                $q->where('role', $request->role)
                    ->orWhereHas('roles', function ($rq) use ($request) {
                        $rq->where('name', $request->role);
                    });
            });
        }

        if ($request->filled('centre_id')) {
            $query->where('centre_id', $request->centre_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $users = $query->latest('id')->paginate(20)->withQueryString();

        $stats = [
            'total_users'     => User::count(),
            'total_admins'    => User::whereIn('role', ['super_admin', 'admin'])->count(),
            'total_centres'   => User::where('role', 'centre')->count(),
            'total_staff'     => User::where('role', 'staff')->count(),
            'total_students'  => User::where('role', 'student')->count(),
            'active_users'    => User::where('is_active', true)->count(),
            'inactive_users'  => User::where('is_active', false)->count(),
        ];

        $roles = Role::orderBy('display_name')->get();
        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();

        return view('admin.users.index', compact('users', 'stats', 'roles', 'centres'));
    }

    /**
     * Show form to create a new user.
     */
    public function create()
    {
        $roles = Role::orderBy('display_name')->get();
        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();

        return view('admin.users.create', compact('roles', 'centres'));
    }

    /**
     * Store a freshly created user in database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:191'],
            'email'      => ['required', 'string', 'email', 'max:191', 'unique:users,email'],
            'mobile'     => ['nullable', 'string', 'max:20'],
            'password'   => ['required', 'string', 'min:8', 'confirmed'],
            'role'       => ['required', 'string', Rule::in(['super_admin', 'admin', 'centre', 'staff', 'student'])],
            'centre_id'  => ['nullable', 'exists:centres,id'],
            'role_ids'   => ['nullable', 'array'],
            'role_ids.*' => ['exists:roles,id'],
            'is_active'  => ['nullable', 'boolean'],
        ]);

        return DB::transaction(function () use ($request, $validated) {
            $user = User::create([
                'name'      => $validated['name'],
                'email'     => strtolower(trim($validated['email'])),
                'mobile'    => $validated['mobile'] ?? null,
                'password'  => Hash::make($validated['password']),
                'role'      => $validated['role'],
                'centre_id' => in_array($validated['role'], ['centre', 'staff']) ? ($validated['centre_id'] ?? null) : null,
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Sync additional roles or ensure primary role exists in pivot
            $roleIds = $validated['role_ids'] ?? [];
            $primaryRoleModel = Role::where('name', $validated['role'])->first();
            if ($primaryRoleModel && !in_array($primaryRoleModel->id, $roleIds)) {
                $roleIds[] = $primaryRoleModel->id;
            }

            if (!empty($roleIds)) {
                $user->roles()->sync($roleIds);
            }

            AuditLog::record('user_created', $user, null, [
                'name'      => $user->name,
                'email'     => $user->email,
                'role'      => $user->role,
                'centre_id' => $user->centre_id,
                'is_active' => $user->is_active,
            ]);

            return redirect()->route('admin.users.index')
                ->with('success', "User account for {$user->name} ({$user->email}) successfully provisioned.");
        });
    }

    /**
     * Display the specified user dossier.
     */
    public function show(User $user)
    {
        $user->load(['roles.permissions', 'assignedCentre', 'student']);

        // Collect user-specific audit trail
        $auditLogs = AuditLog::with('user')
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere(function ($oq) use ($user) {
                        $oq->where('auditable_type', User::class)
                            ->where('auditable_id', $user->id);
                    });
            })
            ->latest()
            ->take(15)
            ->get();

        return view('admin.users.show', compact('user', 'auditLogs'));
    }

    /**
     * Show form to edit user account.
     */
    public function edit(User $user)
    {
        $user->load(['roles', 'assignedCentre']);
        $roles = Role::orderBy('display_name')->get();
        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();

        return view('admin.users.edit', compact('user', 'roles', 'centres'));
    }

    /**
     * Update user details in database.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:191'],
            'email'      => ['required', 'string', 'email', 'max:191', Rule::unique('users', 'email')->ignore($user->id)],
            'mobile'     => ['nullable', 'string', 'max:20'],
            'password'   => ['nullable', 'string', 'min:8', 'confirmed'],
            'role'       => ['required', 'string', Rule::in(['super_admin', 'admin', 'centre', 'staff', 'student'])],
            'centre_id'  => ['nullable', 'exists:centres,id'],
            'role_ids'   => ['nullable', 'array'],
            'role_ids.*' => ['exists:roles,id'],
            'is_active'  => ['nullable', 'boolean'],
        ]);

        // Guard self-lockout
        if ($user->id === auth()->id() && !$request->boolean('is_active', true)) {
            return back()->with('error', 'Security rule: You cannot deactivate your own administrative account.');
        }

        if ($user->id === auth()->id() && $user->role === 'super_admin' && $validated['role'] !== 'super_admin') {
            return back()->with('error', 'Security rule: You cannot revoke your own Super Administrator privilege.');
        }

        return DB::transaction(function () use ($request, $user, $validated) {
            $oldValues = [
                'name'      => $user->name,
                'email'     => $user->email,
                'role'      => $user->role,
                'centre_id' => $user->centre_id,
                'is_active' => $user->is_active,
            ];

            $updateData = [
                'name'      => $validated['name'],
                'email'     => strtolower(trim($validated['email'])),
                'mobile'    => $validated['mobile'] ?? null,
                'role'      => $validated['role'],
                'centre_id' => in_array($validated['role'], ['centre', 'staff']) ? ($validated['centre_id'] ?? null) : null,
                'is_active' => $request->boolean('is_active', true),
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            // Sync roles
            $roleIds = $validated['role_ids'] ?? [];
            $primaryRoleModel = Role::where('name', $validated['role'])->first();
            if ($primaryRoleModel && !in_array($primaryRoleModel->id, $roleIds)) {
                $roleIds[] = $primaryRoleModel->id;
            }
            $user->roles()->sync($roleIds);

            AuditLog::record('user_updated', $user, $oldValues, [
                'name'      => $user->name,
                'email'     => $user->email,
                'role'      => $user->role,
                'centre_id' => $user->centre_id,
                'is_active' => $user->is_active,
            ]);

            return redirect()->route('admin.users.show', $user)
                ->with('success', "User account for {$user->name} updated successfully.");
        });
    }

    /**
     * Toggle active/inactive status of account.
     */
    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Security rule: You cannot deactivate your own administrative account.');
        }

        $oldStatus = $user->is_active;
        $user->is_active = !$user->is_active;
        $user->save();

        AuditLog::record('user_status_toggled', $user, ['is_active' => $oldStatus], ['is_active' => $user->is_active]);

        $statusLabel = $user->is_active ? 'Activated' : 'Deactivated';
        return back()->with('success', "Account for {$user->name} has been {$statusLabel}.");
    }

    /**
     * Manually reset user password by administrator.
     */
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        AuditLog::record('user_password_reset', $user, null, ['reset_by' => auth()->id()]);

        return back()->with('success', "Password for {$user->name} ({$user->email}) reset successfully.");
    }

    /**
     * Remove the specified user from database.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Security rule: You cannot delete your own administrative account.');
        }

        if ($user->isSuperAdmin() && User::where('role', 'super_admin')->count() <= 1) {
            return back()->with('error', 'Security rule: Cannot delete the only remaining Super Administrator.');
        }

        return DB::transaction(function () use ($user) {
            $name = $user->name;
            $email = $user->email;

            AuditLog::record('user_deleted', $user, [
                'name'  => $name,
                'email' => $email,
                'role'  => $user->role,
            ], null);

            $user->roles()->detach();
            $user->delete();

            return redirect()->route('admin.users.index')
                ->with('success', "User account {$name} ({$email}) permanently deleted.");
        });
    }
}
