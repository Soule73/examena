<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserManagementService
{
    public function getUserWithPagination(array $filters, int $perPage, User $currentUser)
    {
        $query = User::with('roles')->whereNot('id', $currentUser->id);

        if (!empty($filters['role'])) {
            $query->role($filters['role']);
        }
        $per_page = $filters['per_page'] ?? 10;

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate($per_page)->withQueryString();

        return $users;
    }
    public function store(array $data)
    {
        DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $user->assignRole($data['role']);
        });
    }

    public function update(User $user, array $data)
    {
        try {
            DB::transaction(function () use ($user, $data) {
                $updatedData = [
                    'name' => $data['name'],
                    'email' => $data['email'],
                ];

                if (isset($data['password']) && $data['password']) {
                    $updatedData['password'] = Hash::make($data['password']);
                }

                $user->update($updatedData);

                if (!isset($data['role']) || !Role::where('name', $data['role'])->exists()) {
                    throw new \InvalidArgumentException("Le rôle est requis pour la mise à jour.");
                }

                $user->syncRoles([$data['role']]);
            });
        } catch (\Exception $e) {

            Log::error(
                "Erreur lors de la mise à jour de l'utilisateur : " . $e->getMessage()
            );
            throw $e;
        }
    }

    public function delete(User $user)
    {
        DB::transaction(function () use ($user) {

            $user->examAssignments()->delete();
            $user->exams()->delete();
            $user->answers()->delete();
            $user->roles()->detach();

            $user->delete();
        });
    }

    public function toggleStatus(User $user)
    {
        $user->is_active = !$user->is_active;
        $user->save();
    }
}
