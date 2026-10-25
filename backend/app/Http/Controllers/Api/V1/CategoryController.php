<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CategoryRequest;
use App\Http\Resources\Api\V1\CategoryResource;
use App\Models\TicketCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', TicketCategory::class);
        $query = TicketCategory::query()
            ->when($request->boolean('active_only'), fn($q) => $q->where('is_active', true));

        $categories = $request->has('per_page')
            ? $query->paginate($request->integer('per_page', 15))
            : $query->get();

        return CategoryResource::collection($categories);
    }

    public function store(CategoryRequest $request)
    {
        Gate::authorize('create', TicketCategory::class);
        $validated = $request->validated();
        $category = TicketCategory::create($validated);
        return CategoryResource::make($category)
            ->response()
            ->setStatusCode(201);
    }

    public function update(CategoryRequest $request, TicketCategory $ticketCategory)
    {
        Gate::authorize('update', $ticketCategory);
        $validated = $request->validated();
        $ticketCategory->update($validated);
        return CategoryResource::make($ticketCategory);
    }

    public function destroy(TicketCategory $ticketCategory)
    {
        Gate::authorize('delete', $ticketCategory);
        $ticketCategory->delete();
        return response()->noContent();
    }

    public function show(TicketCategory $ticketCategory)
    {
        Gate::authorize('view', $ticketCategory);
        return CategoryResource::make($ticketCategory);
    }

    public function toggleStatus(TicketCategory $ticketCategory)
    {
        Gate::authorize('update', $ticketCategory);
        $ticketCategory->update(['is_active' => !$ticketCategory->is_active]);
        return CategoryResource::make($ticketCategory);
    }
}
