<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Resources\Api\V1\TicketResource;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TicketController extends Controller
{
    protected $ticketService;

    public function __construct(TicketService $ticketService) {
        $this->ticketService = $ticketService;
    }

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Ticket::class);
        $user = $request->user();
        $tickets = Ticket::query();
        $tickets->with(['requester', 'assignee', 'category'])
                ->when($user->role === UserRole::EMPLOYEE, fn($q) => $q->where('requester_id', $user->id))
                ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
                ->when($request->filled('category_id'), fn($q) => $q->where('category_id', $request->category_id))
                ->when($request->filled('search'), fn($q, $search) => $q->where(fn($sub) =>
                    $sub->where('ticket_number', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%")))
                ->latest();
        return TicketResource::collection($tickets->paginate($request->integer('per_page', 15)));
    }

    public function store(StoreTicketRequest $request)
    {
        Gate::authorize('create', Ticket::class);
        $validated = $request->validated();
        $ticket = $this->ticketService->createTicket($validated, $request->user());
        $ticket->load('requester','category');
        return new TicketResource($ticket)
            ->response()
            ->setStatusCode(201);
    }

    public function show(Ticket $ticket)
    {
        Gate::authorize('view', $ticket);
        $ticket->load('requester', 'assignee', 'category');
        return new TicketResource($ticket);
    }
}
