<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CaseFile;

class CaseFileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cases = [
            [
                'number' => 'C-2024-001',
                'name' => 'Smith vs. Johnson Contract Dispute',
                'client' => 'John Smith',
                'client_org' => 'Smith Enterprises',
                'client_initials' => 'JS',
                'type_label' => 'Civil',
                'type_badge' => 'Civil',
                'status' => 'Active',
                'hearing_date' => '2024-02-15',
                'hearing_time' => '10:00',
            ],
            [
                'number' => 'C-2024-002',
                'name' => 'State vs. Robert Davis',
                'client' => 'Robert Davis',
                'client_org' => '',
                'client_initials' => 'RD',
                'type_label' => 'Criminal Defense',
                'type_badge' => 'Criminal',
                'status' => 'Pending',
                'hearing_date' => '2024-02-20',
                'hearing_time' => '14:30',
            ],
            [
                'number' => 'C-2024-003',
                'name' => 'Williams Divorce Proceedings',
                'client' => 'Sarah Williams',
                'client_org' => '',
                'client_initials' => 'SW',
                'type_label' => 'Family Law',
                'type_badge' => 'Family',
                'status' => 'Active',
                'hearing_date' => '2024-02-25',
                'hearing_time' => '09:00',
            ],
            [
                'number' => 'C-2024-004',
                'name' => 'TechCorp Merger Agreement',
                'client' => 'TechCorp Inc.',
                'client_org' => 'TechCorp Inc.',
                'client_initials' => 'TC',
                'type_label' => 'Corporate',
                'type_badge' => 'Corporate',
                'status' => 'Closed',
                'hearing_date' => null,
                'hearing_time' => null,
            ],
            [
                'number' => 'C-2024-005',
                'name' => 'Innovation Patent Dispute',
                'client' => 'Innovation Labs',
                'client_org' => 'Innovation Labs LLC',
                'client_initials' => 'IL',
                'type_label' => 'Intellectual Property',
                'type_badge' => 'IP',
                'status' => 'Active',
                'hearing_date' => '2024-03-01',
                'hearing_time' => '11:00',
            ],
        ];

        foreach ($cases as $case) {
            CaseFile::create($case);
        }
    }
}
