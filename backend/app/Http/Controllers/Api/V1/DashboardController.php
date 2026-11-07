<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function metrics(Request $request)
    {
        if(!in_array($request->user()->role, [UserRole::ADMIN, UserRole::SUPPORT_AGENT]))
        {
            abort(403, 'Unauthorized');
        }

        $userMetrics = User::where('is_active', true)->selectRaw('role, count(*) as count')->groupBy('role')->pluck('count', 'role');

        $ticketMetrics = Ticket::selectRaw('
            count(*) as total,
            count(case when status = ? then 1 end) as open,
            count(case when status = ? then 1 end) as in_progress,
            count(case when status = ? then 1 end) as resolved,
            count(case when status = ? then 1 end) as closed,
            count(case when assignee_id is null then 1 end) as unassigned
        ', [
            TicketStatus::OPEN->value,
            TicketStatus::IN_PROGRESS->value,
            TicketStatus::RESOLVED->value,
            TicketStatus::CLOSED->value,
        ])->first();

        return response()->json([
            'data' => [
                'tickets' => [
                    'total' => (int) $ticketMetrics->total,
                    'open' => (int) $ticketMetrics->open,
                    'in_progress' => (int) $ticketMetrics->in_progress,
                    'resolved' => (int) $ticketMetrics->resolved,
                    'closed' => (int) $ticketMetrics->closed,
                    'unassigned' => (int) $ticketMetrics->unassigned,
                ],
                'users' => [
                    'admin' => (int) ($userMetrics[UserRole::ADMIN->value] ?? 0),
                    'support_agent' => (int) ($userMetrics[UserRole::SUPPORT_AGENT->value] ?? 0),
                    'employee' => (int) ($userMetrics[UserRole::EMPLOYEE->value] ?? 0)
                ]
            ]
        ]);
    }
}
