<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UpdateUserRoleRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', User::class);
        $query = User::query();

        // Filter by role
        $query->when($request->filled('role'), fn($q) => $q->where('role', $request->role));

        // Filter by status
        $query->when($request->has('is_active'), fn($q) => $q->where('is_active', $request->boolean('is_active')));

        // filter by search: name or email
        $query->when($request->filled('search'), fn($q, $search) =>
            $q->where(fn($sub) =>
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
            )
        );
        $users = $query->latest('id')->paginate($request->integer('per_page', 15));

        return UserResource::collection($users);
    }

    public function updateRole(UpdateUserRoleRequest $request, User $user)
    {
        Gate::authorize('updateRole', $user);
        $user->update([
            'role' => $request->validated('role')
        ]);

        return new UserResource($user);
    }

    public function toggleStatus(Request $request, User $user)
    {
        Gate::authorize('toggleStatus', $user);

        if($user->id === $request->user()->id)
        {
            abort(422, 'You cannot deactivate your own account.');
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        return new UserResource($user);
    }
}
