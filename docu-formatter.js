/**
 * ═══════════════════════════════════════════════════════════════
 * MICROFINANCIAL MANAGEMENT SYSTEM — DocuFormatter
 * Professional Document PDF Generator
 * ═══════════════════════════════════════════════════════════════
 *
 * Generates A4-ready, professional PDF documents with:
 *   A. Header (system name, title, code, department, source, date)
 *   B. Document Details (metadata box)
 *   C. Summary (auto-generated key highlights)
 *   D. Contextual Sections (based on document type detection)
 *   E. Data Records Table (with totals/counts)
 *   F. Footer (confidentiality + page X of Y + document code)
 *
 * Dependencies:
 *   - jsPDF 2.5+ (CDN)
 *   - jsPDF-AutoTable 3.8+ (CDN)
 *
 * Usage:
 *   DocuFormatter.generate({
 *     title, doc_code, department, source_module, confidentiality,
 *     status, prepared_by, generated_datetime, description, records
 *   });
 *
 *   // Or build JSON schema only:
 *   const json = DocuFormatter.buildSchema({ ... });
 */

const DocuFormatter = (() => {

  // ─── Color Palette ──────────────────────────────────────────
  const C = {
    brand:     [5, 150, 105],
    darkGreen: [4, 120, 87],
    navy:      [15, 23, 42],
    dark:      [31, 41, 55],
    gray600:   [75, 85, 99],
    gray500:   [107, 114, 128],
    gray400:   [156, 163, 175],
    gray300:   [209, 213, 219],
    gray200:   [229, 231, 235],
    gray100:   [243, 244, 246],
    gray50:    [249, 250, 251],
    white:     [255, 255, 255],
    red:       [220, 38, 38],
    amber:     [217, 119, 6],
    blue:      [37, 99, 235],
    purple:    [124, 58, 237],
    emerald:   [5, 150, 105],
    warn:      [146, 64, 14],
    warnBg:    [254, 252, 232],
    warnBdr:   [253, 224, 71],
  };

  // Source module color map
  const SOURCE_COLORS = {
    'HR2':      { color: [5,150,105],   label: 'HR2 — Talent & Workforce' },
    'HR4':      { color: [37,99,235],   label: 'HR4 — Compensation & HCM' },
    'Logs1':    { color: [124,58,237],  label: 'Logs1 — Supply Chain' },
    'Finance':  { color: [217,119,6],   label: 'Finance — Financial Mgmt' },
    'Legal':    { color: [220,38,38],   label: 'Legal — Administrative' },
    'Admin':    { color: [5,150,105],   label: 'Admin — System Admin' },
  };

  const CONF_COLORS = {
    'Public':       [5, 150, 105],
    'Internal':     [37, 99, 235],
    'Confidential': [217, 119, 6],
    'Restricted':   [220, 38, 38],
  };

  // ─── Document Type Detection ────────────────────────────────
  // Returns { type, typeName, sectionLayout }
  function detectDocumentType(title, sourceModule) {
    const t = (title || '').toLowerCase();
    const s = (sourceModule || '').toLowerCase();

    // Legal
    if (s.includes('legal') || t.includes('legal case') || t.includes('case tracking') || t.includes('demand letter') || t.includes('complaint'))
      return { type: 'legal_case', typeName: 'Legal Case Summary Report', layout: 'legal' };
    if (t.includes('contract') || t.includes('agreement') || t.includes('nda'))
      return { type: 'contract', typeName: 'Contract Lifecycle Report', layout: 'contract' };
    if (t.includes('permit') || t.includes('license') || t.includes('renewal'))
      return { type: 'permit', typeName: 'Permits & Licenses Report', layout: 'permit' };

    // Finance
    if (t.includes('budget') || t.includes('allocation') || t.includes('forecast'))
      return { type: 'budget', typeName: 'Budget & Allocation Report', layout: 'budget' };
    if (t.includes('disbursement') || t.includes('payment') || t.includes('payable'))
      return { type: 'disbursement', typeName: 'Disbursement Report', layout: 'finance' };
    if (t.includes('collection') || t.includes('receivable') || t.includes('aging'))
      return { type: 'collection', typeName: 'Collection & Aging Report', layout: 'finance' };
    if (t.includes('balance sheet') || t.includes('financial statement') || t.includes('cash flow'))
      return { type: 'financial_stmt', typeName: 'Financial Statement Report', layout: 'finance' };
    if (t.includes('general ledger') || t.includes('journal') || t.includes('ledger'))
      return { type: 'ledger', typeName: 'General Ledger Report', layout: 'finance' };
    if (t.includes('invoice') || t.includes('receipt'))
      return { type: 'invoice', typeName: 'Invoice / Receipt Document', layout: 'finance' };

    // HR
    if (t.includes('payroll') || t.includes('salary') || t.includes('compensation'))
      return { type: 'payroll', typeName: 'Payroll & Compensation Report', layout: 'hr' };
    if (t.includes('employee') || t.includes('personnel') || t.includes('staff'))
      return { type: 'employee', typeName: 'Employee Record Report', layout: 'hr' };
    if (t.includes('attendance') || t.includes('leave') || t.includes('absence'))
      return { type: 'attendance', typeName: 'Attendance & Leave Report', layout: 'hr' };
    if (t.includes('training') || t.includes('development') || t.includes('learning'))
      return { type: 'training', typeName: 'Training & Development Report', layout: 'hr' };
    if (t.includes('recruitment') || t.includes('hiring') || t.includes('applicant'))
      return { type: 'recruitment', typeName: 'Recruitment Report', layout: 'hr' };

    // Facilities & Visitors
    if (t.includes('visitor') || t.includes('guest') || t.includes('visit log'))
      return { type: 'visitor', typeName: 'Visitor Management Report', layout: 'visitor' };
    if (t.includes('facilit') || t.includes('reservation') || t.includes('booking') || t.includes('room'))
      return { type: 'facilities', typeName: 'Facilities Reservation Report', layout: 'facilities' };

    // Supply Chain
    if (t.includes('inventory') || t.includes('stock') || t.includes('warehouse'))
      return { type: 'inventory', typeName: 'Inventory & Stock Report', layout: 'supply_chain' };
    if (t.includes('procurement') || t.includes('purchase order') || t.includes('supplier'))
      return { type: 'procurement', typeName: 'Procurement Report', layout: 'supply_chain' };

    // Governance / Admin
    if (t.includes('audit') || t.includes('audit trail') || t.includes('log'))
      return { type: 'audit', typeName: 'Audit Trail Report', layout: 'audit' };
    if (t.includes('policy') || t.includes('memo') || t.includes('circular'))
      return { type: 'policy', typeName: 'Policy / Memo Document', layout: 'policy' };
    if (t.includes('report'))
      return { type: 'report', typeName: 'Data Report', layout: 'generic' };

    // Fallback
    return { type: 'document', typeName: 'Administrative Document', layout: 'generic' };
  }

  // ─── Section Generators ─────────────────────────────────────
  // Each returns [] of { heading, content_bullets }

  function buildLegalSections(records) {
    const sections = [];
    const total = records.length;
    const statusMap = countBy(records, r => r.status || r.case_status || r.workflow_step || 'unknown');
    const typeMap = countBy(records, r => r.case_type || r.type || 'unknown');
    const priorities = countBy(records, r => r.priority || r.severity || 'normal');

    sections.push({
      heading: 'Case Overview',
      content_bullets: [
        `Total cases on record: ${total}`,
        `Case types: ${mapEntries(typeMap)}`,
        `Source module: Legal Management`,
      ]
    });

    sections.push({
      heading: 'Status Breakdown',
      content_bullets: Object.entries(statusMap).map(([k, v]) => `${titleCase(k)}: ${v} case(s)`)
    });

    const critical = records.filter(r =>
      ['critical', 'high'].includes((r.priority || r.severity || '').toLowerCase())
    );
    if (critical.length > 0) {
      sections.push({
        heading: 'Priority Watchlist',
        content_bullets: critical.slice(0, 8).map(r =>
          `${r.case_number || r.case_code || r.demand_code || r.title || 'N/A'} — ${titleCase(r.priority || r.severity || 'High')} — ${r.title || r.case_title || r.borrower_name || 'N/A'}`
        )
      });
    }

    const pending = records.filter(r => {
      const s = (r.status || r.workflow_step || '').toLowerCase();
      return s.includes('pending') || s.includes('review') || s.includes('hearing') || s.includes('investigation');
    });
    sections.push({
      heading: 'Next Actions',
      content_bullets: pending.length > 0
        ? pending.slice(0, 6).map(r => `${r.case_number || r.case_code || 'N/A'}: Currently ${titleCase(r.status || r.workflow_step || 'Pending')}`)
        : ['No pending actions identified in the current dataset.']
    });

    return sections;
  }

  function buildContractSections(records) {
    const sections = [];
    const statusMap = countBy(records, r => r.status || 'unknown');
    const typeMap = countBy(records, r => r.contract_type || r.type || 'unknown');

    sections.push({
      heading: 'Contract Overview',
      content_bullets: [
        `Total contracts: ${records.length}`,
        `Contract types: ${mapEntries(typeMap)}`,
      ]
    });

    sections.push({
      heading: 'Status Summary',
      content_bullets: Object.entries(statusMap).map(([k, v]) => `${titleCase(k)}: ${v}`)
    });

    const expiring = records.filter(r => {
      if (!r.end_date && !r.expiry_date) return false;
      const end = new Date(r.end_date || r.expiry_date);
      const now = new Date();
      const diff = (end - now) / (1000 * 60 * 60 * 24);
      return diff >= 0 && diff <= 90;
    });

    if (expiring.length > 0) {
      sections.push({
        heading: 'Expiring Within 90 Days',
        content_bullets: expiring.slice(0, 6).map(r =>
          `${r.contract_number || r.title || 'N/A'} — Expires: ${r.end_date || r.expiry_date || 'N/A'}`
        )
      });
    }

    return sections;
  }

  function buildPermitSections(records) {
    const sections = [];
    const statusMap = countBy(records, r => r.status || 'unknown');

    sections.push({
      heading: 'Permits & Licenses Summary',
      content_bullets: [
        `Total permits/licenses: ${records.length}`,
        `Status distribution: ${mapEntries(statusMap)}`,
      ]
    });

    const expiring = records.filter(r => {
      if (!r.expiry_date && !r.expiration_date) return false;
      const end = new Date(r.expiry_date || r.expiration_date);
      const diff = (end - new Date()) / (1000 * 60 * 60 * 24);
      return diff >= 0 && diff <= 60;
    });

    if (expiring.length > 0) {
      sections.push({
        heading: 'Renewal Alerts (≤60 Days)',
        content_bullets: expiring.slice(0, 6).map(r =>
          `${r.permit_name || r.title || r.permit_code || 'N/A'} — Expires: ${r.expiry_date || r.expiration_date}`
        )
      });
    }

    return sections;
  }

  function buildBudgetSections(records) {
    const sections = [];
    const total = records.length;
    const totalBudget = sumField(records, 'budget_amount', 'amount', 'total_budget', 'allocated');
    const totalActual = sumField(records, 'actual_amount', 'actual', 'spent', 'disbursed');
    const variance = totalBudget > 0 ? totalBudget - totalActual : 0;

    sections.push({
      heading: 'Budget Summary',
      content_bullets: [
        `Total line items: ${total}`,
        totalBudget > 0 ? `Total allocated budget: ${formatCurrency(totalBudget)}` : null,
        totalActual > 0 ? `Total actual expenditure: ${formatCurrency(totalActual)}` : null,
        variance !== 0 ? `Variance: ${formatCurrency(variance)} (${variance > 0 ? 'Under Budget' : 'Over Budget'})` : null,
      ].filter(Boolean)
    });

    const deptMap = countBy(records, r => r.department || r.dept || r.category || 'Unassigned');
    sections.push({
      heading: 'Allocations',
      content_bullets: Object.entries(deptMap).map(([k, v]) => `${k}: ${v} item(s)`)
    });

    const overBudget = records.filter(r => {
      const b = parseMoney(r.budget_amount || r.amount || r.total_budget || r.allocated);
      const a = parseMoney(r.actual_amount || r.actual || r.spent || r.disbursed);
      return b > 0 && a > b;
    });
    if (overBudget.length > 0) {
      sections.push({
        heading: 'Variance Notes',
        content_bullets: overBudget.slice(0, 6).map(r => {
          const name = r.title || r.description || r.department || 'N/A';
          return `${name} — Over budget`;
        })
      });
    }

    sections.push({
      heading: 'Recommendations',
      content_bullets: [
        overBudget.length > 0 ? `Review ${overBudget.length} over-budget item(s) for corrective action.` : 'All items are within budget parameters.',
        'Ensure periodic reconciliation with actual expenditures.',
      ]
    });

    return sections;
  }

  function buildFinanceSections(records) {
    const sections = [];
    const statusMap = countBy(records, r => r.status || r.payment_status || 'unknown');
    const totalAmt = sumField(records, 'amount', 'total', 'balance', 'payment_amount');

    sections.push({
      heading: 'Financial Summary',
      content_bullets: [
        `Total records: ${records.length}`,
        totalAmt > 0 ? `Total amount: ${formatCurrency(totalAmt)}` : null,
        `Status breakdown: ${mapEntries(statusMap)}`,
      ].filter(Boolean)
    });

    const overdue = records.filter(r =>
      (r.status || '').toLowerCase().includes('overdue') ||
      (r.payment_status || '').toLowerCase().includes('overdue')
    );
    if (overdue.length > 0) {
      sections.push({
        heading: 'Overdue Items',
        content_bullets: overdue.slice(0, 6).map(r =>
          `${r.reference || r.invoice_number || r.code || 'N/A'} — ${r.amount || r.total || 'N/A'}`
        )
      });
    }

    return sections;
  }

  function buildHRSections(records) {
    const sections = [];
    const statusMap = countBy(records, r => r.status || r.employment_status || 'Active');
    const deptMap = countBy(records, r => r.department || r.dept || 'Unassigned');

    sections.push({
      heading: 'HR Summary',
      content_bullets: [
        `Total records: ${records.length}`,
        `Status: ${mapEntries(statusMap)}`,
        `Departments: ${mapEntries(deptMap)}`,
      ]
    });

    return sections;
  }

  function buildVisitorSections(records) {
    const sections = [];
    const total = records.length;
    const purposeMap = countBy(records, r => r.purpose || r.visit_purpose || 'General');

    sections.push({
      heading: 'Visitor Summary',
      content_bullets: [
        `Total visits recorded: ${total}`,
        `Visit purposes: ${mapEntries(purposeMap)}`,
      ]
    });

    const flagged = records.filter(r =>
      (r.flagged === true || r.flagged === 1 || (r.risk_level || '').toLowerCase() === 'high')
    );
    if (flagged.length > 0) {
      sections.push({
        heading: 'High-Risk Flags',
        content_bullets: flagged.slice(0, 6).map(r =>
          `${r.visitor_name || r.name || 'N/A'} — ${r.purpose || r.visit_purpose || 'N/A'}`
        )
      });
    }

    sections.push({
      heading: 'Visit Logs',
      content_bullets: records.slice(0, 6).map(r =>
        `${r.visitor_name || r.name || 'N/A'} — ${r.check_in || r.date || 'N/A'} — ${r.purpose || 'N/A'}`
      )
    });

    return sections;
  }

  function buildFacilitiesSections(records) {
    const sections = [];
    const total = records.length;
    const statusMap = countBy(records, r => r.status || 'unknown');

    sections.push({
      heading: 'Reservation Summary',
      content_bullets: [
        `Total reservations: ${total}`,
        `Status: ${mapEntries(statusMap)}`,
      ]
    });

    const facilityMap = countBy(records, r => r.facility || r.room || r.venue || 'Unspecified');
    sections.push({
      heading: 'Schedule',
      content_bullets: Object.entries(facilityMap).map(([k, v]) => `${k}: ${v} reservation(s)`)
    });

    const conflicts = records.filter(r =>
      (r.status || '').toLowerCase().includes('conflict') || (r.overlap === true)
    );
    if (conflicts.length > 0) {
      sections.push({
        heading: 'Conflicts & Rules',
        content_bullets: conflicts.slice(0, 6).map(r =>
          `${r.facility || r.room || 'N/A'} — ${r.date || r.reservation_date || 'N/A'} — Conflict detected`
        )
      });
    }

    return sections;
  }

  function buildSupplyChainSections(records) {
    const sections = [];
    const statusMap = countBy(records, r => r.status || 'unknown');
    const totalVal = sumField(records, 'total_value', 'amount', 'unit_price', 'cost');

    sections.push({
      heading: 'Supply Chain Summary',
      content_bullets: [
        `Total records: ${records.length}`,
        totalVal > 0 ? `Total value: ${formatCurrency(totalVal)}` : null,
        `Status: ${mapEntries(statusMap)}`,
      ].filter(Boolean)
    });

    return sections;
  }

  function buildAuditSections(records) {
    const sections = [];
    const actionMap = countBy(records, r => r.action || r.event_type || r.activity || 'unknown');
    const userMap = countBy(records, r => r.performed_by || r.user || r.username || 'System');

    sections.push({
      heading: 'Audit Summary',
      content_bullets: [
        `Total audit entries: ${records.length}`,
        `Action types: ${mapEntries(actionMap)}`,
      ]
    });

    sections.push({
      heading: 'Activity by User',
      content_bullets: Object.entries(userMap).sort((a, b) => b[1] - a[1]).slice(0, 8)
        .map(([k, v]) => `${k}: ${v} action(s)`)
    });

    return sections;
  }

  function buildPolicySections(records) {
    const sections = [];
    sections.push({
      heading: 'Document Purpose',
      content_bullets: ['This is an official policy/memo document issued by the administration.']
    });
    if (records.length > 0) {
      sections.push({
        heading: 'Referenced Items',
        content_bullets: records.slice(0, 6).map(r =>
          `${r.title || r.subject || r.reference || r.code || JSON.stringify(r).substring(0, 80)}`
        )
      });
    }
    return sections;
  }

  function buildGenericSections(records) {
    const sections = [];
    const total = records.length;
    const statusMap = countBy(records, r => r.status || 'N/A');

    sections.push({
      heading: 'Document Summary',
      content_bullets: [
        `Total records: ${total}`,
        Object.keys(statusMap).length > 1 ? `Status breakdown: ${mapEntries(statusMap)}` : null,
      ].filter(Boolean)
    });

    return sections;
  }

  // Layout → section builder map
  const SECTION_BUILDERS = {
    legal:        buildLegalSections,
    contract:     buildContractSections,
    permit:       buildPermitSections,
    budget:       buildBudgetSections,
    finance:      buildFinanceSections,
    hr:           buildHRSections,
    visitor:      buildVisitorSections,
    facilities:   buildFacilitiesSections,
    supply_chain: buildSupplyChainSections,
    audit:        buildAuditSections,
    policy:       buildPolicySections,
    generic:      buildGenericSections,
  };


  // ─── Summary Bullet Generator ───────────────────────────────
  function generateSummary(records, detectedType, input) {
    const bullets = [];
    const n = records.length;
    if (n === 0) { bullets.push('No data records provided.'); return bullets; }

    bullets.push(`Total records: ${n}`);

    // Status summary
    const statusMap = countBy(records, r => r.status || r.workflow_step || r.case_status || r.payment_status || null);
    const statuses = Object.entries(statusMap).filter(([k]) => k !== 'null');
    if (statuses.length > 0 && statuses.length <= 8) {
      bullets.push('Status: ' + statuses.map(([k, v]) => `${titleCase(k)} (${v})`).join(', '));
    }

    // Department summary
    const deptMap = countBy(records, r => r.department || r.dept || null);
    const depts = Object.entries(deptMap).filter(([k]) => k !== 'null');
    if (depts.length > 1 && depts.length <= 8) {
      bullets.push('Departments: ' + depts.map(([k, v]) => `${k} (${v})`).join(', '));
    }

    // Money totals
    const moneyFields = ['amount', 'total', 'budget_amount', 'actual_amount', 'loan_amount', 'payment_amount', 'balance', 'demand_amount', 'contract_value'];
    for (const f of moneyFields) {
      const vals = records.map(r => parseMoney(r[f])).filter(v => v > 0);
      if (vals.length > 0) {
        const sum = vals.reduce((a, b) => a + b, 0);
        bullets.push(`Total ${titleCase(f.replace(/_/g, ' '))}: ${formatCurrency(sum)}`);
        break; // only first money field
      }
    }

    // Priority / severity counts (for legal, etc.)
    const prioMap = countBy(records, r => r.priority || r.severity || null);
    const prios = Object.entries(prioMap).filter(([k]) => k !== 'null');
    if (prios.length > 0) {
      const critical = prios.filter(([k]) => ['critical', 'high'].includes(k.toLowerCase()));
      if (critical.length > 0) {
        bullets.push('Critical/High priority: ' + critical.map(([k, v]) => `${v} ${titleCase(k)}`).join(', '));
      }
    }

    // Date range
    const dateFields = ['created_at', 'date', 'filed_date', 'start_date', 'check_in', 'issued_date'];
    for (const f of dateFields) {
      const dates = records.map(r => r[f]).filter(Boolean).map(d => new Date(d)).filter(d => !isNaN(d)).sort((a, b) => a - b);
      if (dates.length >= 2) {
        const fmt = d => d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        bullets.push(`Date range: ${fmt(dates[0])} — ${fmt(dates[dates.length - 1])}`);
        break;
      }
    }

    return bullets.slice(0, 6);
  }


  // ─── Build Table from Records ───────────────────────────────
  function buildTable(records, title, detectedType) {
    if (!records || records.length === 0) return null;

    // Collect all keys from records
    const allKeys = [];
    const keySet = new Set();
    records.forEach(r => {
      Object.keys(r).forEach(k => {
        if (!keySet.has(k) && k !== '_rawData' && !k.startsWith('_')) {
          keySet.add(k);
          allKeys.push(k);
        }
      });
    });

    // Prioritize certain columns first
    const priorityOrder = [
      'id', 'code', 'case_number', 'case_code', 'loan_code', 'document_code', 'reference', 'contract_number', 'permit_code',
      'title', 'name', 'borrower_name', 'visitor_name', 'employee_name', 'subject',
      'type', 'case_type', 'contract_type', 'document_type', 'category',
      'department', 'dept',
      'status', 'workflow_step', 'case_status', 'payment_status',
      'priority', 'severity',
      'amount', 'loan_amount', 'budget_amount', 'payment_amount', 'demand_amount', 'contract_value', 'total', 'balance', 'fee',
      'date', 'created_at', 'filed_date', 'start_date', 'end_date', 'expiry_date', 'due_date',
      'assigned_to', 'assigned_attorney', 'prepared_by'
    ];

    const columns = [];
    const seen = new Set();
    // Add priority keys that exist
    priorityOrder.forEach(k => {
      if (keySet.has(k) && !seen.has(k)) {
        columns.push(k);
        seen.add(k);
      }
    });
    // Add remaining keys
    allKeys.forEach(k => {
      if (!seen.has(k)) {
        columns.push(k);
        seen.add(k);
      }
    });

    // Limit to 12 columns for readability
    const finalCols = columns.slice(0, 12);
    const headers = finalCols.map(k => titleCase(k.replace(/_/g, ' ')));

    // Sort rows: priority/severity (critical first), then status
    const sorted = [...records].sort((a, b) => {
      const prioOrder = { critical: 0, high: 1, medium: 2, normal: 3, low: 4 };
      const pa = prioOrder[(a.priority || a.severity || 'normal').toLowerCase()] ?? 3;
      const pb = prioOrder[(b.priority || b.severity || 'normal').toLowerCase()] ?? 3;
      if (pa !== pb) return pa - pb;
      return 0;
    });

    const rows = sorted.map(r =>
      finalCols.map(k => {
        const v = r[k];
        if (v === null || v === undefined) return 'N/A';
        if (typeof v === 'object') return JSON.stringify(v);
        return String(v);
      })
    );

    return {
      title: title || 'Data Records',
      columns: headers,
      rows: rows,
      _rawColumns: finalCols,
    };
  }


  // ─── Build Document JSON Schema ─────────────────────────────
  function buildSchema(input) {
    const {
      title = 'Untitled Document',
      doc_code = '',
      department = '',
      source_module = '',
      confidentiality = 'Internal',
      status = 'Active',
      prepared_by = '',
      generated_datetime = new Date().toLocaleString('en-US', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }),
      description = '',
      records = []
    } = input;

    const detected = detectDocumentType(title, source_module);
    const srcInfo = SOURCE_COLORS[source_module] || SOURCE_COLORS['Admin'];
    const builder = SECTION_BUILDERS[detected.layout] || SECTION_BUILDERS.generic;

    const sections = builder(records);
    const summary = generateSummary(records, detected, input);
    const table = buildTable(records, title, detected);

    return {
      doc_header: {
        system_name: 'Microfinancial Management System',
        title: title,
        subtitle: `${detected.typeName}  |  ${department}  |  ${source_module || 'Admin'}`,
        code: doc_code,
        department: department,
        source: source_module || 'Admin',
        generated_datetime: generated_datetime,
        confidentiality: confidentiality,
        status: status,
      },
      document_details: [
        { label: 'Document Title', value: title },
        { label: 'Document Code', value: doc_code || 'N/A' },
        { label: 'Source System', value: srcInfo.label },
        { label: 'Department', value: department || 'N/A' },
        { label: 'Prepared By', value: prepared_by || 'N/A' },
        { label: 'Generated', value: generated_datetime },
        { label: 'Confidentiality', value: confidentiality },
        { label: 'Status', value: status },
        { label: 'Document Type', value: detected.typeName },
        { label: 'Description', value: description || 'N/A' },
      ],
      summary_bullets: summary,
      sections: sections,
      tables: table ? [table] : [],
      footer: {
        left_text: `${confidentiality} • ${status} • ${doc_code}`,
        right_text: 'Microfinancial Management System',
      },
      _detected: detected,
      _source_color: srcInfo.color,
    };
  }


  // ═══════════════════════════════════════════════════════════════
  // PDF RENDERING ENGINE
  // ═══════════════════════════════════════════════════════════════

  function renderPDF(schema, options = {}) {
    const { jsPDF } = window.jspdf;
    if (!jsPDF) { console.error('DocuFormatter: jsPDF not loaded.'); return null; }

    const orientation = options.landscape ? 'landscape' : 'portrait';
    const pdf = new jsPDF({ orientation, unit: 'mm', format: 'a4' });
    const W = pdf.internal.pageSize.getWidth();
    const H = pdf.internal.pageSize.getHeight();
    const M = 18; // margin
    const IW = W - M * 2; // inner width

    const h = schema.doc_header;
    const srcColor = schema._source_color || C.brand;
    const confColor = CONF_COLORS[h.confidentiality] || C.blue;

    let y = 0;

    // ── Page break helper ──
    function needsPage(need) {
      if (y > H - need - 28) { pdf.addPage(); y = 16; return true; }
      return false;
    }

    // ── Section header helper ──
    function sectionHeader(label, accentColor) {
      needsPage(24);
      const ac = accentColor || srcColor;
      pdf.setFillColor(...ac);
      pdf.rect(M, y, 3, 8, 'F');
      pdf.setFillColor(248, 250, 252);
      pdf.rect(M + 3, y, IW - 3, 8, 'F');
      pdf.setFontSize(9);
      pdf.setFont('helvetica', 'bold');
      pdf.setTextColor(...ac);
      pdf.text(label.toUpperCase(), M + 8, y + 5.5);
      y += 12;
    }

    // ═══════════════════════════════════
    // A. HEADER
    // ═══════════════════════════════════

    // Top accent bar
    pdf.setFillColor(...srcColor);
    pdf.rect(0, 0, W, 4, 'F');

    // Letterhead background
    pdf.setFillColor(248, 250, 252);
    pdf.rect(0, 4, W, 40, 'F');

    // System Name (small)
    pdf.setFontSize(9);
    pdf.setFont('helvetica', 'normal');
    pdf.setTextColor(...C.gray500);
    pdf.text(h.system_name, M, 14);

    // Document Title (big)
    pdf.setFontSize(18);
    pdf.setFont('helvetica', 'bold');
    pdf.setTextColor(...C.navy);
    const titleLines = pdf.splitTextToSize(h.title, IW - 70);
    pdf.text(titleLines, M, 23);

    // Subtitle (type | dept | source)
    const titleEndY = 23 + titleLines.length * 7;
    pdf.setFontSize(9);
    pdf.setFont('helvetica', 'normal');
    pdf.setTextColor(...C.gray600);
    pdf.text(h.subtitle, M, Math.min(titleEndY + 2, 36));

    // Right side: Document reference box
    pdf.setFillColor(...C.white);
    pdf.setDrawColor(...srcColor);
    pdf.setLineWidth(0.6);
    pdf.roundedRect(W - M - 64, 8, 64, 30, 2, 2, 'FD');
    pdf.setFontSize(7);
    pdf.setFont('helvetica', 'bold');
    pdf.setTextColor(...srcColor);
    pdf.text('DOCUMENT REF', W - M - 32, 15, { align: 'center' });
    pdf.setFontSize(8);
    pdf.setFont('helvetica', 'bold');
    pdf.setTextColor(...C.navy);
    const codeStr = h.code || 'N/A';
    pdf.setFontSize(codeStr.length > 20 ? 6 : codeStr.length > 14 ? 7 : 8);
    pdf.text(codeStr, W - M - 32, 22, { align: 'center' });
    pdf.setFontSize(7);
    pdf.setFont('helvetica', 'normal');
    pdf.setTextColor(...C.gray400);
    pdf.text(h.generated_datetime || 'N/A', W - M - 32, 28, { align: 'center' });
    // Confidentiality badge in ref box
    pdf.setFontSize(6);
    pdf.setFont('helvetica', 'bold');
    pdf.setTextColor(...confColor);
    pdf.text(h.confidentiality.toUpperCase(), W - M - 32, 34, { align: 'center' });

    // Double divider line
    pdf.setDrawColor(...srcColor);
    pdf.setLineWidth(0.8);
    pdf.line(M, 44, W - M, 44);
    pdf.setLineWidth(0.2);
    pdf.setDrawColor(...C.gray200);
    pdf.line(M, 45.5, W - M, 45.5);

    y = 52;


    // ═══════════════════════════════════
    // B. DOCUMENT DETAILS (2-column table)
    // ═══════════════════════════════════

    sectionHeader('Document Details', srcColor);

    const details = schema.document_details || [];
    // Split into left/right columns
    const half = Math.ceil(details.length / 2);
    const leftD = details.slice(0, half);
    const rightD = details.slice(half);
    const colW = (IW - 6) / 2;

    const maxR = Math.max(leftD.length, rightD.length);
    for (let i = 0; i < maxR; i++) {
      const ry = y + i * 8;
      if (i % 2 === 0) {
        pdf.setFillColor(...C.gray50);
        pdf.rect(M, ry - 2.5, IW, 8, 'F');
      }
      if (leftD[i]) {
        pdf.setFontSize(7.5);
        pdf.setFont('helvetica', 'bold');
        pdf.setTextColor(...C.gray500);
        pdf.text(leftD[i].label, M + 4, ry + 2);
        pdf.setFont('helvetica', 'normal');
        pdf.setTextColor(...C.navy);
        const lv = String(leftD[i].value || 'N/A').substring(0, 50);
        pdf.text(lv, M + 44, ry + 2);
      }
      if (rightD[i]) {
        const rx = M + colW + 6;
        pdf.setFontSize(7.5);
        pdf.setFont('helvetica', 'bold');
        pdf.setTextColor(...C.gray500);
        pdf.text(rightD[i].label, rx, ry + 2);
        pdf.setFont('helvetica', 'normal');
        pdf.setTextColor(...C.navy);
        const rv = String(rightD[i].value || 'N/A').substring(0, 50);
        pdf.text(rv, rx + 40, ry + 2);
      }
    }
    y += maxR * 8 + 6;

    // Description box (if long description)
    const descDetail = details.find(d => d.label === 'Description');
    if (descDetail && descDetail.value && descDetail.value !== 'N/A' && descDetail.value.length > 60) {
      needsPage(30);
      pdf.setFillColor(...C.white);
      pdf.setDrawColor(...C.gray200);
      const descLines = pdf.splitTextToSize(descDetail.value, IW - 12);
      const descH = Math.min(descLines.length * 4.2 + 8, 60);
      pdf.roundedRect(M, y, IW, descH, 2, 2, 'FD');
      pdf.setFontSize(8);
      pdf.setFont('helvetica', 'normal');
      pdf.setTextColor(...C.dark);
      pdf.text(descLines.slice(0, 12), M + 6, y + 6);
      y += descH + 6;
    }


    // ═══════════════════════════════════
    // C. SUMMARY (auto bullets)
    // ═══════════════════════════════════

    if (schema.summary_bullets && schema.summary_bullets.length > 0) {
      sectionHeader('Summary', C.brand);

      // Summary highlight box
      pdf.setFillColor(240, 253, 244);
      pdf.setDrawColor(167, 243, 208);
      pdf.setLineWidth(0.3);
      const bulletCount = schema.summary_bullets.length;
      const summH = bulletCount * 6 + 8;
      pdf.roundedRect(M, y, IW, summH, 2, 2, 'FD');

      pdf.setFontSize(8.5);
      pdf.setFont('helvetica', 'normal');
      pdf.setTextColor(...C.dark);
      schema.summary_bullets.forEach((b, i) => {
        pdf.setFont('helvetica', 'bold');
        pdf.setTextColor(...C.brand);
        pdf.text('●', M + 5, y + 6 + i * 6);
        pdf.setFont('helvetica', 'normal');
        pdf.setTextColor(...C.dark);
        pdf.text(String(b).substring(0, 100), M + 10, y + 6 + i * 6);
      });
      y += summH + 6;
    }


    // ═══════════════════════════════════
    // D. CONTEXTUAL SECTIONS
    // ═══════════════════════════════════

    if (schema.sections && schema.sections.length > 0) {
      schema.sections.forEach((sec, si) => {
        // Alternate section accent colors
        const accentColors = [srcColor, C.brand, C.blue, C.amber, C.purple];
        const ac = accentColors[si % accentColors.length];

        sectionHeader(sec.heading, ac);

        if (sec.content_bullets && sec.content_bullets.length > 0) {
          sec.content_bullets.forEach((b, bi) => {
            needsPage(8);
            pdf.setFontSize(8.5);
            pdf.setFont('helvetica', 'bold');
            pdf.setTextColor(...ac);
            pdf.text('▸', M + 4, y);
            pdf.setFont('helvetica', 'normal');
            pdf.setTextColor(...C.dark);
            const bLines = pdf.splitTextToSize(String(b), IW - 16);
            pdf.text(bLines, M + 10, y);
            y += bLines.length * 4.5 + 2;
          });
          y += 4;
        }
      });
    }


    // ═══════════════════════════════════
    // E. DATA RECORDS TABLE
    // ═══════════════════════════════════

    if (schema.tables && schema.tables.length > 0) {
      schema.tables.forEach(tbl => {
        needsPage(30);
        sectionHeader(tbl.title || 'Data Records', srcColor);

        // Record count label
        pdf.setFontSize(7);
        pdf.setFont('helvetica', 'normal');
        pdf.setTextColor(...C.gray400);
        pdf.text(`${tbl.rows.length} record(s)`, W - M, y - 8, { align: 'right' });

        const isLandscape = tbl.columns.length > 8;

        pdf.autoTable({
          startY: y,
          head: [tbl.columns],
          body: tbl.rows,
          theme: 'grid',
          styles: {
            fontSize: 7,
            cellPadding: 2.5,
            lineColor: C.gray200,
            lineWidth: 0.15,
            overflow: 'linebreak',
            minCellWidth: 14,
          },
          headStyles: {
            fillColor: srcColor,
            textColor: C.white,
            fontStyle: 'bold',
            fontSize: 7,
            halign: 'left',
          },
          alternateRowStyles: { fillColor: C.gray50 },
          columnStyles: (() => {
            const cs = {};
            // Make first column (code/id) bold
            cs[0] = { fontStyle: 'bold', textColor: C.navy };
            // Make status column colored
            tbl.columns.forEach((col, ci) => {
              if (col.toLowerCase().includes('status') || col.toLowerCase().includes('priority')) {
                cs[ci] = { fontStyle: 'bold' };
              }
            });
            return cs;
          })(),
          margin: { left: M, right: M },
          showHead: 'firstPage',
          didDrawPage: function() { /* footers added later */ },
        });
        y = pdf.lastAutoTable.finalY + 6;

        // Totals row — count money columns
        if (tbl._rawColumns) {
          const moneyKeys = ['amount', 'total', 'balance', 'budget_amount', 'actual_amount', 'payment_amount',
                             'loan_amount', 'demand_amount', 'contract_value', 'fee', 'cost'];
          const totals = [];
          tbl._rawColumns.forEach((key, ci) => {
            if (moneyKeys.some(mk => key.toLowerCase().includes(mk))) {
              const sum = tbl.rows.reduce((acc, row) => acc + parseMoney(row[ci]), 0);
              if (sum > 0) totals.push({ col: tbl.columns[ci], sum });
            }
          });
          if (totals.length > 0) {
            needsPage(12);
            pdf.setFillColor(240, 253, 244);
            pdf.setDrawColor(167, 243, 208);
            pdf.roundedRect(M, y, IW, 8, 1.5, 1.5, 'FD');
            pdf.setFontSize(8);
            pdf.setFont('helvetica', 'bold');
            pdf.setTextColor(...C.brand);
            let tx = M + 4;
            pdf.text('TOTALS:', tx, y + 5.5);
            tx += 20;
            totals.forEach(t => {
              pdf.setFont('helvetica', 'normal');
              pdf.setTextColor(...C.gray600);
              pdf.text(`${t.col}:`, tx, y + 5.5);
              tx += pdf.getTextWidth(`${t.col}: `) + 2;
              pdf.setFont('helvetica', 'bold');
              pdf.setTextColor(...C.navy);
              pdf.text(formatCurrency(t.sum), tx, y + 5.5);
              tx += pdf.getTextWidth(formatCurrency(t.sum)) + 12;
            });
            y += 12;
          }
        }

        // Record count footer
        needsPage(8);
        pdf.setFontSize(7);
        pdf.setFont('helvetica', 'italic');
        pdf.setTextColor(...C.gray400);
        pdf.text(`Total Records: ${tbl.rows.length}`, M + 4, y + 2);
        y += 6;
      });
    }


    // ═══════════════════════════════════
    // F. FOOTER on all pages
    // ═══════════════════════════════════

    const totalPages = pdf.internal.getNumberOfPages();
    for (let p = 1; p <= totalPages; p++) {
      pdf.setPage(p);

      // Bottom accent bar
      pdf.setFillColor(...srcColor);
      pdf.rect(0, H - 7, W, 7, 'F');

      // Footer text area
      pdf.setFillColor(248, 250, 252);
      pdf.rect(0, H - 20, W, 13, 'F');
      pdf.setDrawColor(...C.gray200);
      pdf.line(M, H - 20, W - M, H - 20);

      // Left: confidentiality label + doc code
      pdf.setFontSize(7);
      pdf.setFont('helvetica', 'normal');
      pdf.setTextColor(...C.gray400);
      pdf.text(schema.footer.left_text, M, H - 12);

      // Center: system name
      pdf.setFontSize(7);
      pdf.text(schema.footer.right_text, W / 2, H - 12, { align: 'center' });

      // Right: Page X of Y
      pdf.setFont('helvetica', 'bold');
      pdf.text(`Page ${p} of ${totalPages}`, W - M, H - 12, { align: 'right' });

      // Second line
      pdf.setFontSize(6);
      pdf.setFont('helvetica', 'normal');
      pdf.setTextColor(...C.gray400);
      pdf.text(`Generated: ${h.generated_datetime}`, M, H - 8.5);
      pdf.text(`Ref: ${h.code || 'N/A'}`, W - M, H - 8.5, { align: 'right' });
    }

    return pdf;
  }


  // ═══════════════════════════════════════════════════════════════
  // PUBLIC API
  // ═══════════════════════════════════════════════════════════════

  /**
   * Generate and download a formatted PDF document.
   *
   * @param {Object} input — Document metadata + records
   * @param {string} input.title — Document title
   * @param {string} input.doc_code — Document code (e.g. DOC-2026-00001)
   * @param {string} input.department — Department name
   * @param {string} input.source_module — Source system (HR2, HR4, Logs1, Finance, Legal, Admin)
   * @param {string} input.confidentiality — Public|Internal|Confidential|Restricted
   * @param {string} input.status — Active|Draft|Archived|Retained
   * @param {string} input.prepared_by — Preparer name
   * @param {string} input.generated_datetime — Date/time string
   * @param {string} input.description — Description / purpose text
   * @param {Array}  input.records — Array of data record objects
   * @param {Object} options — { landscape: false, autoDownload: true }
   */
  function generate(input, options = {}) {
    const schema = buildSchema(input);
    const pdf = renderPDF(schema, options);
    if (!pdf) return null;

    if (options.autoDownload !== false) {
      const safeName = (input.title || 'Document').replace(/[^a-zA-Z0-9]/g, '_').substring(0, 40);
      const code = input.doc_code || 'DOC';
      pdf.save(`${code}_${safeName}.pdf`);
    }

    return { pdf, schema };
  }

  /**
   * Build the structured JSON schema only (no PDF).
   * Useful for preview, API responses, or custom rendering.
   */
  function getSchema(input) {
    return buildSchema(input);
  }

  /**
   * Render a schema to PDF (for two-step workflows).
   */
  function renderFromSchema(schema, options = {}) {
    return renderPDF(schema, options);
  }

  /**
   * Get the detected document type info for a title + source.
   */
  function detectType(title, sourceModule) {
    return detectDocumentType(title, sourceModule);
  }


  // ─── Utility Functions ──────────────────────────────────────

  function titleCase(s) {
    return String(s || '').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
  }

  function countBy(arr, fn) {
    const map = {};
    arr.forEach(item => {
      const key = fn(item);
      if (key !== null && key !== undefined) map[key] = (map[key] || 0) + 1;
    });
    return map;
  }

  function mapEntries(obj) {
    return Object.entries(obj).map(([k, v]) => `${titleCase(k)} (${v})`).join(', ');
  }

  function parseMoney(v) {
    if (v === null || v === undefined) return 0;
    const n = parseFloat(String(v).replace(/[₱$,\s]/g, ''));
    return isNaN(n) ? 0 : n;
  }

  function formatCurrency(n) {
    return '₱' + Number(n).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function sumField(records, ...fields) {
    let total = 0;
    for (const f of fields) {
      const vals = records.map(r => parseMoney(r[f])).filter(v => v > 0);
      if (vals.length > 0) { total = vals.reduce((a, b) => a + b, 0); break; }
    }
    return total;
  }


  // ─── Return Public Interface ────────────────────────────────
  return {
    generate,
    getSchema,
    renderFromSchema,
    detectType,
    buildSchema,
  };

})();
