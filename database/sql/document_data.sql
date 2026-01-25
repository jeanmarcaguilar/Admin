-- SQL to insert document data into the documents table
-- This includes all the document types: internal, payment, vendor, release of funds, purchase, and disbursement

-- First, make sure the table exists and is empty
TRUNCATE TABLE `documents`;

-- Insert sample documents
INSERT INTO `documents` (`code`, `name`, `type`, `category`, `size`, `date`, `status`, `is_shared`, `description`, `file_path`, `created_at`, `updated_at`) VALUES
-- Internal Documents
('DOC-2025-1001', 'Q1 Financial Report', 'internal', 'financial', '2.5 MB', '2025-10-15', 'Indexed', 0, 'First quarter financial report for 2025', 'documents/financial/q1_report_2025.pdf', NOW(), NOW()),
('DOC-2025-1002', 'Employee Handbook 2025', 'internal', 'hr', '1.8 MB', '2025-10-10', 'Indexed', 0, 'Updated employee handbook for 2025', 'documents/hr/employee_handbook_2025.pdf', NOW(), NOW()),

-- Payment Documents
('PAY-2025-1001', 'Vendor Payment - ABC Corp', 'payment', 'financial', '1.2 MB', '2025-10-12', 'Indexed', 1, 'Payment to ABC Corp for office supplies', 'documents/payments/abc_corp_payment_oct.pdf', NOW(), NOW()),
('PAY-2025-1002', 'Utility Bill - October 2025', 'payment', 'utilities', '0.8 MB', '2025-10-05', 'Indexed', 0, 'Monthly utility bill payment', 'documents/payments/utility_oct_2025.pdf', NOW(), NOW()),

-- Vendor Documents
('VEND-2025-1001', 'Vendor Contract - XYZ Supplies', 'vendor', 'contracts', '1.5 MB', '2025-09-28', 'Indexed', 0, 'Annual supply contract with XYZ', 'documents/vendors/xyz_supply_contract.pdf', NOW(), NOW()),
('VEND-2025-1002', 'Vendor NDA - Tech Solutions Inc', 'vendor', 'legal', '1.1 MB', '2025-10-01', 'Indexed', 1, 'Non-disclosure agreement with vendor', 'documents/vendors/tech_solutions_nda.pdf', NOW(), NOW()),

-- Release of Funds Documents
('ROF-2025-1001', 'Project Alpha - Phase 1 Release', 'release_of_funds', 'projects', '0.9 MB', '2025-10-18', 'Indexed', 1, 'Approval for release of funds for Phase 1', 'documents/funds/project_alpha_phase1.pdf', NOW(), NOW()),
('ROF-2025-1002', 'Emergency Fund Release', 'release_of_funds', 'financial', '1.0 MB', '2025-10-08', 'Indexed', 0, 'Approval for emergency fund release', 'documents/funds/emergency_release_oct.pdf', NOW(), NOW()),

-- Purchase Documents
('PO-2025-1001', 'Office Equipment Purchase', 'purchase', 'procurement', '2.1 MB', '2025-10-14', 'Indexed', 0, 'Purchase order for new office computers', 'documents/purchases/office_equipment_po.pdf', NOW(), NOW()),
('PO-2025-1002', 'Software License Renewal', 'purchase', 'it', '0.7 MB', '2025-10-02', 'Indexed', 1, 'Annual software license renewal', 'documents/purchases/software_licenses_2025.pdf', NOW(), NOW()),

-- Disbursement Documents
('DISB-2025-1001', 'October 2025 Payroll', 'disbursement', 'payroll', '2.8 MB', '2025-10-01', 'Indexed', 0, 'Monthly payroll disbursement', 'documents/disbursements/payroll_oct_2025.pdf', NOW(), NOW()),
('DISB-2025-1002', 'Vendor Payments - October 2025', 'disbursement', 'financial', '1.9 MB', '2025-10-03', 'Indexed', 1, 'Monthly vendor payments report', 'documents/disbursements/vendor_payments_oct.pdf', NOW(), NOW());
