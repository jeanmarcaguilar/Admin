<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialProposalStatusOverride extends Model
{
    protected $table = 'financial_proposal_status_overrides';

    protected $fillable = [
        'ref_no',
        'status',
        'updated_by',
    ];
}
