<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of users (admin only).
     */
    public function index(): JsonResponse
    {
        $user = auth()->user();

        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.',
            ], 403);
        }

        $users = User::select('id', 'name', 'email', 'phone', 'address', 'role', 'created_at')
            ->get();

        Log::info('Users list retrieved', [
            'count' => $users->count(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $users,
        ], 200);
    }

    /**
     * Display the specified user.
     */
    public function show(string $id): JsonResponse
    {
        $currentUser = auth()->user();

        // Allow users to view their own profile, or admin to view any profile
        if ($currentUser->id != $id && $currentUser->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $user = User::find($id);

        if (!$user) {
            Log::warning('User not found', [
                'user_id' => $id,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        Log::info('User retrieved', [
            'user_id' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'role' => $user->role,
                'created_at' => $user->created_at,
            ],
        ], 200);
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        $currentUser = auth()->user();

        // Allow users to update their own profile, or admin to update any profile
        if ($currentUser->id != $id && $currentUser->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        // Prevent non-admin from changing role
        if ($currentUser->role !== 'admin' && $request->has('role')) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot change your role',
            ], 403);
        }

        $updateData = $request->only(['name', 'email', 'phone', 'address', 'role']);

        if ($request->has('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        Log::info('User updated successfully', [
            'user_id' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'role' => $user->role,
            ],
        ], 200);
    }

    /**
     * Remove the specified user.
     */
    public function destroy(string $id): JsonResponse
    {
        $currentUser = auth()->user();

        if ($currentUser->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.',
            ], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        // Prevent admin from deleting themselves
        if ($currentUser->id == $id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account',
            ], 403);
        }

        $user->delete();

        Log::info('User deleted successfully', [
            'user_id' => $id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ], 200);
    }
}

