<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function metrics(Request $request)
    {
        // Fetch the current user
        $user = $request->user();

        if($user->role === UserRole::EMPLOYEE)
        {
            $metrics = Ticket::where('requester_id', $user->id)
                ->selectRaw('
                    count(*) as total,
                    count(case when status = ? then 1 end) as open,
                    count(case when status = ? then 1 end) as in_progress,
                    count(case when status = ? then 1 end) as resolved
                ', [TicketStatus::OPEN->value, TicketStatus::IN_PROGRESS->value, TicketStatus::RESOLVED->value]
                )->first();

            return response()->json([
                'data' => [
                    'metrics' => [
                        'my_submitted' => (int) $metrics->total,
                        'open'         => (int) $metrics->open,
                        'in_progress'  => (int) $metrics->in_progress,
                        'resolved'     => (int) $metrics->resolved,
                    ],
                ],
            ]);
        }

        if($user->role === UserRole::SUPPORT_AGENT)
        {
            $metrics = Ticket::selectRaw(
            '
                count(case when assignee_id = ? then 1 end) as assigned_to_me,
                count(case when assignee_id is null then 1 end) as unassigned,
                count(case when status = ? then 1 end) as open,
                count(case when status = ? then 1 end) as in_progress,
                count(case when priority = ? and status != ? then 1 end) as urgent,
                count(case when status = ? then 1 end) as closed
            ', [
                $user->id,
                TicketStatus::OPEN->value,
                TicketStatus::IN_PROGRESS->value,
                TicketPriority::URGENT->value,
                TicketStatus::CLOSED->value,
                TicketStatus::CLOSED->value
            ]
            )->first();

            return response()->json([
                'data' => [
                    'metrics' => [
                        'assigned_to_me' => (int) $metrics->assigned_to_me,
                        'unassigned'     => (int) $metrics->unassigned,
                        'open'           => (int) $metrics->open,
                        'in_progress'    => (int) $metrics->in_progress,
                        'urgent'         => (int) $metrics->urgent,
                        'closed' => (int) $metrics->closed,
                    ]
                ]
            ]);
        }

        // 3. Admin: System-wide metrics + total users + 5 recent activities
        $totalUsers = User::where('is_active', true)->count();
        $metrics = Ticket::selectRaw('
            count(*) as total,
            count(case when status = ? then 1 end) as open,
            count(case when status = ? then 1 end) as in_progress,
            count(case when status = ? then 1 end) as resolved,
            count(case when priority = ? and status != ? then 1 end) as urgent
        ', [
            TicketStatus::OPEN->value,
            TicketStatus::IN_PROGRESS->value,
            TicketStatus::RESOLVED->value,
            TicketPriority::URGENT->value,
            TicketStatus::CLOSED->value
        ])->first();
        $recentActivities = TicketActivity::with(['user', 'ticket'])
            ->latest()
            ->take(5)
            ->get();
        return response()->json([
            'data' => [
                'metrics' => [
                    'total_users'   => (int) $totalUsers,
                    'total_tickets' => (int) $metrics->total,
                    'open'          => (int) $metrics->open,
                    'in_progress'   => (int) $metrics->in_progress,
                    'resolved'      => (int) $metrics->resolved,
                    'urgent'        => (int) $metrics->urgent,
                ],
                'recent_activities' => $recentActivities,
            ]
        ]);
    }
}
