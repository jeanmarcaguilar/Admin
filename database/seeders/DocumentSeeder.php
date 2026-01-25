<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documents = [
            [
                'code' => 'DOC-' . now()->format('Y') . '-' . str_pad(1, 4, '0', STR_PAD_LEFT),
                'name' => 'Q1 Financial Report',
                'type' => 'internal',
                'category' => 'financial',
                'size' => '2.5 MB',
                'date' => now()->subDays(30)->toDateString(),
                'status' => 'Indexed',
                'is_shared' => false,
                'description' => 'First quarter financial report for 2025',
                'file_path' => 'documents/financial/q1_report.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'DOC-' . now()->format('Y') . '-' . str_pad(2, 4, '0', STR_PAD_LEFT),
                'name' => 'Vendor Payment - ABC Corp',
                'type' => 'payment',
                'category' => 'financial',
                'size' => '1.2 MB',
                'date' => now()->subDays(15)->toDateString(),
                'status' => 'Indexed',
                'is_shared' => true,
                'description' => 'Payment invoice for services rendered by ABC Corp',
                'file_path' => 'documents/payments/abc_corp_invoice.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'DOC-' . now()->format('Y') . '-' . str_pad(3, 4, '0', STR_PAD_LEFT),
                'name' => 'Vendor Contract - XYZ Supplies',
                'type' => 'vendor',
                'category' => 'contracts',
                'size' => '1.8 MB',
                'date' => now()->subDays(10)->toDateString(),
                'status' => 'Indexed',
                'is_shared' => false,
                'description' => 'Service agreement with XYZ Supplies',
                'file_path' => 'documents/contracts/xyz_supplies_agreement.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'DOC-' . now()->format('Y') . '-' . str_pad(4, 4, '0', STR_PAD_LEFT),
                'name' => 'Project Funds Release - Phase 1',
                'type' => 'release_of_funds',
                'category' => 'financial',
                'size' => '0.9 MB',
                'date' => now()->subDays(5)->toDateString(),
                'status' => 'Indexed',
                'is_shared' => true,
                'description' => 'Approval for release of funds for Project Alpha Phase 1',
                'file_path' => 'documents/financial/project_alpha_funds_release.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'DOC-' . now()->format('Y') . '-' . str_pad(5, 4, '0', STR_PAD_LEFT),
                'name' => 'Office Equipment Purchase Order',
                'type' => 'purchase',
                'category' => 'procurement',
                'size' => '1.5 MB',
                'date' => now()->subDays(3)->toDateString(),
                'status' => 'Indexed',
                'is_shared' => false,
                'description' => 'Purchase order for new office equipment',
                'file_path' => 'documents/purchases/office_equipment_po.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'DOC-' . now()->format('Y') . '-' . str_pad(6, 4, '0', STR_PAD_LEFT),
                'name' => 'Monthly Disbursement Report - October 2025',
                'type' => 'disbursement',
                'category' => 'financial',
                'size' => '2.1 MB',
                'date' => now()->toDateString(),
                'status' => 'Indexed',
                'is_shared' => true,
                'description' => 'Monthly financial disbursement report for October 2025',
                'file_path' => 'documents/financial/disbursement_oct_2025.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('documents')->insert($documents);
    }
}
