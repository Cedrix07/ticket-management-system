<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreCommentRequest;
use App\Http\Resources\Api\V1\CommentResource;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TicketCommentController extends Controller
{
    protected $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    public function index(Ticket $ticket)
    {
        Gate::authorize('view', $ticket);
        $comments = $ticket->comments()->with('user')->oldest('id')->get();
        return CommentResource::collection($comments);
    }

    public function store(StoreCommentRequest $request, Ticket $ticket)
    {
        Gate::authorize('addComment', $ticket);
        $comment = $this->ticketService->addComment($ticket, $request->validated('body'), $request->user());
        return (new CommentResource($comment->load('user')))->response()->setStatusCode(201);
    }
}
