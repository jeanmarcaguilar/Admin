<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ComplianceTracking;

class ComplianceTrackingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $complianceItems = [
            [
                'code' => 'CPL-2023-001',
                'title' => 'Annual Business Permit Renewal',
                'type' => 'government',
                'status' => 'pending',
                'due_date' => now()->addDays(45),
                'description' => 'Annual business permit compliance with local government regulations',
                'responsible_person' => 'John Doe',
                'priority' => 'high',
                'created_at' => now()->subDays(30),
                'updated_at' => now()->subDays(15),
            ],
            [
                'code' => 'CPL-2023-002',
                'title' => 'Financial Audit Report',
                'type' => 'financial',
                'status' => 'active',
                'due_date' => now()->addDays(89),
                'description' => 'Quarterly financial audit and reporting to stakeholders',
                'responsible_person' => 'Jane Smith',
                'priority' => 'medium',
                'created_at' => now()->subDays(45),
                'updated_at' => now()->subDays(20),
            ],
            [
                'code' => 'CPL-2023-003',
                'title' => 'Employee Safety Training',
                'type' => 'safety',
                'status' => 'active',
                'due_date' => now()->addDays(15),
                'description' => 'Mandatory workplace safety training for all employees',
                'responsible_person' => 'Mike Johnson',
                'priority' => 'critical',
                'created_at' => now()->subDays(60),
                'updated_at' => now()->subDays(10),
            ],
            [
                'code' => 'CPL-2023-004',
                'title' => 'Environmental Compliance Check',
                'type' => 'environmental',
                'status' => 'pending',
                'due_date' => now()->addDays(30),
                'description' => 'Environmental impact assessment and compliance verification',
                'responsible_person' => 'Sarah Wilson',
                'priority' => 'medium',
                'created_at' => now()->subDays(20),
                'updated_at' => now()->subDays(5),
            ],
            [
                'code' => 'CPL-2023-005',
                'title' => 'HR Policy Update',
                'type' => 'hr',
                'status' => 'completed',
                'due_date' => now()->subDays(10),
                'description' => 'Updated employee handbook and HR policies',
                'responsible_person' => 'Emily Brown',
                'priority' => 'low',
                'created_at' => now()->subDays(90),
                'updated_at' => now()->subDays(10),
            ],
            [
                'code' => 'CPL-2023-006',
                'title' => 'Legal Contract Review',
                'type' => 'legal',
                'status' => 'overdue',
                'due_date' => now()->subDays(5),
                'description' => 'Review and update all client contracts for compliance',
                'responsible_person' => 'Robert Davis',
                'priority' => 'high',
                'created_at' => now()->subDays(75),
                'updated_at' => now()->subDays(25),
            ],
        ];

        foreach ($complianceItems as $item) {
            ComplianceTracking::create($item);
        }
    }
}
