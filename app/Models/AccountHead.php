<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountHead extends Model
{
    use HasFactory;
    //cost head_types
    const HEADTYPE = [
        'asset'     => 'Asset',
        'liability' => 'Liability',
        'equity'    => 'Equity',
        'income'    => 'Income',
        'expense'   => 'Expense',
    ];
    protected $fillable = [
        'name',
        'head_type',
    ];
}
