<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Approval;
use App\Models\User;

class ApprovalSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create a user for requester
        $user = User::first() ?: User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password')
        ]);

        $requests = [
            [
                'request_id' => 'REQ-001',
                'title' => 'Meeting Room Booking',
                'type' => 'room',
                'requested_by' => 'John Smith',
                'date' => '2025-01-25',
                'status' => 'pending',
                'lead_time' => '3',
                'description' => 'Quarterly team meeting for Q1 planning',
                'requester_id' => $user->id,
            ],
            [
                'request_id' => 'REQ-002',
                'title' => 'Projector Request',
                'type' => 'equipment',
                'requested_by' => 'Sarah Johnson',
                'date' => '2025-01-26',
                'status' => 'pending',
                'lead_time' => '2',
                'description' => 'Need projector for client presentation',
                'requester_id' => $user->id,
            ],
            [
                'request_id' => 'REQ-003',
                'title' => 'Training Room Setup',
                'type' => 'room',
                'requested_by' => 'Mike Wilson',
                'date' => '2025-01-28',
                'status' => 'approved',
                'lead_time' => '7',
                'description' => 'New employee training session',
                'requester_id' => $user->id,
                'approved_by' => 'Admin User',
                'approved_at' => now(),
            ],
            [
                'request_id' => 'REQ-004',
                'title' => 'Audio System',
                'type' => 'equipment',
                'requested_by' => 'Emily Davis',
                'date' => '2025-01-30',
                'status' => 'pending',
                'lead_time' => '1',
                'description' => 'Audio system for company event',
                'requester_id' => $user->id,
            ],
            [
                'request_id' => 'REQ-005',
                'title' => 'Conference Room',
                'type' => 'room',
                'requested_by' => 'David Brown',
                'date' => '2025-02-02',
                'status' => 'rejected',
                'lead_time' => '5',
                'description' => 'Board meeting with investors',
                'requester_id' => $user->id,
                'rejected_by' => 'Admin User',
                'rejected_at' => now(),
            ]
        ];

        foreach ($requests as $request) {
            Approval::create($request);
        }
    }
}
