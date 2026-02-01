<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SalaryRecord extends Model
{
    protected $fillable = [
        'user_id',
        'year',
        'month',
        'term',
        'amount',
        'total_hours',
        'total_live_count',
        'total_sales',
        'total_content_edit',
        'total_content_live',
        'notes',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'total_hours' => 'decimal:2',
        'paid_at' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format((float) $this->amount, 0, ',', '.');
    }

    /**
     * Get period label
     */
    public function getPeriodLabelAttribute()
    {
        $monthName = Carbon::createFromDate($this->year, $this->month, 1)->translatedFormat('F Y');
        return $monthName;
    }

    /**
     * Get term label
     */
    public function getTermLabelAttribute()
    {
        return $this->term == '1' ? 'T1 (1-15)' : 'T2 (16-akhir)';
    }
}
