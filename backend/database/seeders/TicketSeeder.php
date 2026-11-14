<?php

namespace Database\Seeders;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketComment;
use App\Models\TicketActivity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds following the canonical TCK-YYYYMM-XXXX specification.
     */
    public function run(): void
    {
        // 1. Fetch Users
        $admin = User::where('email', 'admin@example.com')->first();
        $jamie = User::where('email', 'agent@example.com')->first();
        $sam = User::where('email', 'sam@example.com')->first();
        $maya = User::where('email', 'employee@example.com')->first();
        $noah = User::where('email', 'noah@example.com')->first();
        $olivia = User::where('email', 'olivia@example.com')->first();
        $sofia = User::where('email', 'sofia@example.com')->first();

        // 2. Fetch Categories
        $network = TicketCategory::where('name', 'Network')->first();
        $hardware = TicketCategory::where('name', 'Hardware')->first();
        $software = TicketCategory::where('name', 'Software')->first();
        $access = TicketCategory::where('name', 'Access')->first();

        // -------------------------------------------------------------
        // Ticket 1: TCK-202611-0001 (Active In-Progress Ticket)
        // -------------------------------------------------------------
        $t1 = Ticket::updateOrCreate(
            ['ticket_number' => 'TCK-202611-0001'],
            [
                'requester_id' => $maya->id,
                'assignee_id' => $jamie->id,
                'category_id' => $network->id,
                'subject' => 'VPN disconnects during client calls',
                'description' => 'My VPN disconnects every 10–15 minutes during client video calls. This started this morning and happens on both home Wi-Fi and a mobile hotspot. I have restarted my laptop and reconnected, but the issue continues. Please help me restore a stable connection.',
                'priority' => TicketPriority::HIGH,
                'status' => TicketStatus::IN_PROGRESS,
                'created_at' => Carbon::parse('2026-11-01 09:12:00'),
                'updated_at' => Carbon::parse('2026-11-01 10:42:00'),
            ]
        );

        // Comments for TCK-202611-0001
        TicketComment::firstOrCreate(
            ['ticket_id' => $t1->id, 'user_id' => $maya->id, 'body' => 'The connection drops are interrupting client calls. My device is a MacBook Pro running macOS 15.'],
            ['created_at' => Carbon::parse('2026-11-01 09:30:00')]
        );
        TicketComment::firstOrCreate(
            ['ticket_id' => $t1->id, 'user_id' => $jamie->id, 'body' => 'Thanks, Maya. I am checking the VPN gateway logs. Could you confirm the version of your VPN client?'],
            ['created_at' => Carbon::parse('2026-11-01 10:15:00')]
        );
        TicketComment::firstOrCreate(
            ['ticket_id' => $t1->id, 'user_id' => $jamie->id, 'body' => 'I found a connection timeout on the gateway. I have applied an update; please reconnect and try another call.'],
            ['created_at' => Carbon::parse('2026-11-01 10:42:00')]
        );

        // Activities for TCK-202611-0001
        TicketActivity::firstOrCreate(
            ['ticket_id' => $t1->id, 'event_type' => 'TICKET_CREATED'],
            ['user_id' => $maya->id, 'description' => 'Maya Patel submitted the request.', 'created_at' => Carbon::parse('2026-11-01 09:12:00')]
        );
        TicketActivity::firstOrCreate(
            ['ticket_id' => $t1->id, 'event_type' => 'TICKET_ASSIGNED', 'new_value' => 'Sam Rivera'],
            ['user_id' => $admin->id, 'description' => 'Alex Morgan assigned the ticket.', 'created_at' => Carbon::parse('2026-11-01 09:15:00')]
        );
        TicketActivity::firstOrCreate(
            ['ticket_id' => $t1->id, 'event_type' => 'TICKET_ASSIGNED', 'new_value' => 'Jamie Chen'],
            ['user_id' => $admin->id, 'description' => 'Alex Morgan changed the assignee.', 'created_at' => Carbon::parse('2026-11-01 09:22:00')]
        );
        TicketActivity::firstOrCreate(
            ['ticket_id' => $t1->id, 'event_type' => 'STATUS_CHANGED', 'new_value' => 'IN_PROGRESS'],
            ['user_id' => $jamie->id, 'description' => 'OPEN → IN PROGRESS', 'created_at' => Carbon::parse('2026-11-01 09:24:00')]
        );
        TicketActivity::firstOrCreate(
            ['ticket_id' => $t1->id, 'event_type' => 'PRIORITY_CHANGED', 'new_value' => 'HIGH'],
            ['user_id' => $jamie->id, 'description' => 'MEDIUM → HIGH by Jamie Chen', 'created_at' => Carbon::parse('2026-11-01 09:25:00')]
        );
        TicketActivity::firstOrCreate(
            ['ticket_id' => $t1->id, 'event_type' => 'TICKET_UPDATED'],
            ['user_id' => $maya->id, 'description' => 'Maya Patel added connection details.', 'created_at' => Carbon::parse('2026-11-01 09:28:00')]
        );

        // -------------------------------------------------------------
        // Ticket 2: TCK-202610-0042 (Closed Ticket)
        // -------------------------------------------------------------
        $t2 = Ticket::updateOrCreate(
            ['ticket_number' => 'TCK-202610-0042'],
            [
                'requester_id' => $maya->id,
                'assignee_id' => $sam->id,
                'category_id' => $access->id,
                'subject' => 'Add access to shared project drive',
                'description' => 'Please grant me access to the Atlas project drive. I need the shared research folder for the upcoming client presentation. My manager has approved this request.',
                'priority' => TicketPriority::MEDIUM,
                'status' => TicketStatus::CLOSED,
                'resolution_notes' => 'Granted read/write permissions to Atlas research folder on storage node 2.',
                'resolved_at' => Carbon::parse('2026-10-30 14:15:00'),
                'closed_at' => Carbon::parse('2026-10-31 09:00:00'),
                'created_at' => Carbon::parse('2026-10-29 09:05:00'),
                'updated_at' => Carbon::parse('2026-10-31 09:00:00'),
            ]
        );

        TicketComment::firstOrCreate(
            ['ticket_id' => $t2->id, 'user_id' => $maya->id, 'body' => 'I have added the project folder name: Atlas / Research. Thank you.'],
            ['created_at' => Carbon::parse('2026-10-29 10:30:00')]
        );
        TicketComment::firstOrCreate(
            ['ticket_id' => $t2->id, 'user_id' => $sam->id, 'body' => 'Access is now enabled. Please sign out and back in to see the project drive.'],
            ['created_at' => Carbon::parse('2026-10-30 14:15:00')]
        );

        TicketActivity::firstOrCreate(
            ['ticket_id' => $t2->id, 'event_type' => 'TICKET_CREATED'],
            ['user_id' => $maya->id, 'description' => 'Maya Patel submitted the request.', 'created_at' => Carbon::parse('2026-10-29 09:05:00')]
        );
        TicketActivity::firstOrCreate(
            ['ticket_id' => $t2->id, 'event_type' => 'TICKET_ASSIGNED', 'new_value' => 'Jamie Chen'],
            ['user_id' => $admin->id, 'description' => 'Alex Morgan assigned the ticket.', 'created_at' => Carbon::parse('2026-10-29 09:15:00')]
        );
        TicketActivity::firstOrCreate(
            ['ticket_id' => $t2->id, 'event_type' => 'TICKET_ASSIGNED', 'new_value' => 'Sam Rivera'],
            ['user_id' => $admin->id, 'description' => 'Alex Morgan changed the assignee.', 'created_at' => Carbon::parse('2026-10-29 10:10:00')]
        );
        TicketActivity::firstOrCreate(
            ['ticket_id' => $t2->id, 'event_type' => 'STATUS_CHANGED', 'new_value' => 'IN_PROGRESS'],
            ['user_id' => $sam->id, 'description' => 'OPEN → IN PROGRESS', 'created_at' => Carbon::parse('2026-10-29 10:12:00')]
        );
        TicketActivity::firstOrCreate(
            ['ticket_id' => $t2->id, 'event_type' => 'PRIORITY_CHANGED', 'new_value' => 'MEDIUM'],
            ['user_id' => $sam->id, 'description' => 'HIGH → MEDIUM by Sam Rivera', 'created_at' => Carbon::parse('2026-10-29 10:20:00')]
        );
        TicketActivity::firstOrCreate(
            ['ticket_id' => $t2->id, 'event_type' => 'TICKET_UPDATED'],
            ['user_id' => $maya->id, 'description' => 'Description updated by Maya Patel.', 'created_at' => Carbon::parse('2026-10-29 10:30:00')]
        );
        TicketActivity::firstOrCreate(
            ['ticket_id' => $t2->id, 'event_type' => 'TICKET_RESOLVED'],
            ['user_id' => $sam->id, 'description' => 'Sam Rivera granted drive access.', 'created_at' => Carbon::parse('2026-10-30 14:15:00')]
        );
        TicketActivity::firstOrCreate(
            ['ticket_id' => $t2->id, 'event_type' => 'TICKET_CLOSED'],
            ['user_id' => $sam->id, 'description' => 'Sam Rivera confirmed completion.', 'created_at' => Carbon::parse('2026-10-31 09:00:00')]
        );

        // -------------------------------------------------------------
        // Ticket 3: TCK-202611-0002 (Urgent, Unassigned Queue Ticket)
        // -------------------------------------------------------------
        $t3 = Ticket::updateOrCreate(
            ['ticket_number' => 'TCK-202611-0002'],
            [
                'requester_id' => $noah->id,
                'assignee_id' => null,
                'category_id' => $access->id,
                'subject' => 'Unable to access payroll portal',
                'description' => 'I am receiving an invalid credentials error when trying to log into the ADP payroll portal to review direct deposit information.',
                'priority' => TicketPriority::URGENT,
                'status' => TicketStatus::OPEN,
                'created_at' => Carbon::parse('2026-11-01 08:30:00'),
                'updated_at' => Carbon::parse('2026-11-01 08:30:00'),
            ]
        );
        TicketActivity::firstOrCreate(
            ['ticket_id' => $t3->id, 'event_type' => 'TICKET_CREATED'],
            ['user_id' => $noah->id, 'description' => 'Noah Williams submitted the request.', 'created_at' => Carbon::parse('2026-11-01 08:30:00')]
        );

        // -------------------------------------------------------------
        // Ticket 4: TCK-202611-0003 (High Priority, Sam Rivera)
        // -------------------------------------------------------------
        $t4 = Ticket::updateOrCreate(
            ['ticket_number' => 'TCK-202611-0003'],
            [
                'requester_id' => $olivia->id,
                'assignee_id' => $sam->id,
                'category_id' => $hardware->id,
                'subject' => 'Laptop will not power on',
                'description' => 'My Dell Precision laptop shut down abruptly during lunch and the power button LED is blinking amber.',
                'priority' => TicketPriority::HIGH,
                'status' => TicketStatus::OPEN,
                'created_at' => Carbon::parse('2026-11-01 11:20:00'),
                'updated_at' => Carbon::parse('2026-11-01 11:20:00'),
            ]
        );
        TicketActivity::firstOrCreate(
            ['ticket_id' => $t4->id, 'event_type' => 'TICKET_CREATED'],
            ['user_id' => $olivia->id, 'description' => 'Olivia Brown submitted the request.', 'created_at' => Carbon::parse('2026-11-01 11:20:00')]
        );

        // -------------------------------------------------------------
        // Ticket 5: TCK-202610-0039 (Urgent, Unassigned)
        // -------------------------------------------------------------
        $t5 = Ticket::updateOrCreate(
            ['ticket_number' => 'TCK-202610-0039'],
            [
                'requester_id' => $sofia->id,
                'assignee_id' => null,
                'category_id' => $software->id,
                'subject' => 'Client database unavailable',
                'description' => 'Staging PostgreSQL database is timing out on port 5432 after the weekend maintenance upgrade.',
                'priority' => TicketPriority::URGENT,
                'status' => TicketStatus::OPEN,
                'created_at' => Carbon::parse('2026-10-28 14:00:00'),
                'updated_at' => Carbon::parse('2026-10-28 14:00:00'),
            ]
        );
        TicketActivity::firstOrCreate(
            ['ticket_id' => $t5->id, 'event_type' => 'TICKET_CREATED'],
            ['user_id' => $sofia->id, 'description' => 'Sofia Garcia submitted the request.', 'created_at' => Carbon::parse('2026-10-28 14:00:00')]
        );

        // -------------------------------------------------------------
        // Ticket 6: TCK-202610-0045 (Unassigned, Hardware)
        // -------------------------------------------------------------
        $t6 = Ticket::updateOrCreate(
            ['ticket_number' => 'TCK-202610-0045'],
            [
                'requester_id' => $noah->id,
                'assignee_id' => null,
                'category_id' => $hardware->id,
                'subject' => 'Meeting room display offline',
                'description' => 'Main conference room TV display is not detecting HDMI or AirPlay inputs.',
                'priority' => TicketPriority::MEDIUM,
                'status' => TicketStatus::OPEN,
                'created_at' => Carbon::parse('2026-10-31 16:45:00'),
                'updated_at' => Carbon::parse('2026-10-31 16:45:00'),
            ]
        );
        TicketActivity::firstOrCreate(
            ['ticket_id' => $t6->id, 'event_type' => 'TICKET_CREATED'],
            ['user_id' => $noah->id, 'description' => 'Noah Williams submitted the request.', 'created_at' => Carbon::parse('2026-10-31 16:45:00')]
        );

        // -------------------------------------------------------------
        // Ticket 7: TCK-202610-0044 (Resolved, Jamie Chen)
        // -------------------------------------------------------------
        $t7 = Ticket::updateOrCreate(
            ['ticket_number' => 'TCK-202610-0044'],
            [
                'requester_id' => $maya->id,
                'assignee_id' => $jamie->id,
                'category_id' => $software->id,
                'subject' => 'Figma license request',
                'description' => 'Requesting an Enterprise Editor seat for the upcoming Q4 product design sprint.',
                'priority' => TicketPriority::LOW,
                'status' => TicketStatus::RESOLVED,
                'resolution_notes' => 'Added Maya Patel to the enterprise design organization workspace.',
                'resolved_at' => Carbon::parse('2026-10-30 15:00:00'),
                'created_at' => Carbon::parse('2026-10-30 08:15:00'),
                'updated_at' => Carbon::parse('2026-10-30 15:00:00'),
            ]
        );
        TicketActivity::firstOrCreate(
            ['ticket_id' => $t7->id, 'event_type' => 'TICKET_CREATED'],
            ['user_id' => $maya->id, 'description' => 'Maya Patel submitted the request.', 'created_at' => Carbon::parse('2026-10-30 08:15:00')]
        );
        TicketActivity::firstOrCreate(
            ['ticket_id' => $t7->id, 'event_type' => 'TICKET_RESOLVED'],
            ['user_id' => $jamie->id, 'description' => 'Jamie Chen resolved the ticket.', 'created_at' => Carbon::parse('2026-10-30 15:00:00')]
        );

        // -------------------------------------------------------------
        // Ticket 8: TCK-202610-0043 (Urgent, In Progress, Jamie Chen)
        // -------------------------------------------------------------
        $t8 = Ticket::updateOrCreate(
            ['ticket_number' => 'TCK-202610-0043'],
            [
                'requester_id' => $olivia->id,
                'assignee_id' => $jamie->id,
                'category_id' => $network->id,
                'subject' => 'Wi-Fi unavailable on floor 3',
                'description' => 'All access points on the third floor east wing have been dropping packets intermittently.',
                'priority' => TicketPriority::URGENT,
                'status' => TicketStatus::IN_PROGRESS,
                'created_at' => Carbon::parse('2026-10-30 11:00:00'),
                'updated_at' => Carbon::parse('2026-10-30 13:20:00'),
            ]
        );
        TicketActivity::firstOrCreate(
            ['ticket_id' => $t8->id, 'event_type' => 'TICKET_CREATED'],
            ['user_id' => $olivia->id, 'description' => 'Olivia Brown submitted the request.', 'created_at' => Carbon::parse('2026-10-30 11:00:00')]
        );
        TicketActivity::firstOrCreate(
            ['ticket_id' => $t8->id, 'event_type' => 'STATUS_CHANGED', 'new_value' => 'IN_PROGRESS'],
            ['user_id' => $jamie->id, 'description' => 'Status changed to IN PROGRESS.', 'created_at' => Carbon::parse('2026-10-30 13:20:00')]
        );
    }
}
