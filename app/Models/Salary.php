<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Salary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'year',
        'month',
        'term',
        'period_start',
        'period_end',
        'total_hours',
        'live_salary',
        'content_edit_bonus',
        'content_live_bonus',
        'total_sales',
        'sales_bonus',
        'target_met',
        'deduction',
        'deduction_notes',
        'total_salary',
        'status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'datetime',
            'period_end' => 'datetime',
            'total_hours' => 'decimal:2',
            'live_salary' => 'decimal:2',
            'content_edit_bonus' => 'decimal:2',
            'content_live_bonus' => 'decimal:2',
            'sales_bonus' => 'decimal:2',
            'deduction' => 'decimal:2',
            'total_salary' => 'decimal:2',
            'target_met' => 'boolean',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns this salary record
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get formatted total salary
     */
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->total_salary, 0, ',', '.');
    }

    /**
     * Get term label (Termin 1 atau Termin 2)
     */
    public function getTermLabelAttribute(): string
    {
        return 'Termin ' . $this->term;
    }

    /**
     * Get period label
     */
    public function getPeriodLabelAttribute(): string
    {
        $monthName = Carbon::createFromDate($this->year, $this->month, 1)->translatedFormat('F Y');
        return $this->term_label . ' - ' . $monthName;
    }

    /**
     * Get date range label
     */
    public function getDateRangeLabelAttribute(): string
    {
        return $this->period_start->format('d') . ' - ' . $this->period_end->format('d M Y');
    }

    /**
     * Calculate total from components
     */
    public function calculateTotal(): float
    {
        return (float) $this->live_salary
            + (float) $this->content_edit_bonus
            + (float) $this->content_live_bonus
            + (float) $this->sales_bonus
            - (float) $this->deduction;
    }

    /**
     * Scope for term 1 (tanggal 1-15)
     */
    public function scopeTerm1($query)
    {
        return $query->where('term', 1);
    }

    /**
     * Scope for term 2 (tanggal 16-akhir bulan)
     */
    public function scopeTerm2($query)
    {
        return $query->where('term', 2);
    }

    /**
     * Get term dates for a given year, month, and term
     */
    public static function getTermDates(int $year, int $month, int $term): array
    {
        if ($term === 1) {
            $start = Carbon::createFromDate($year, $month, 1);
            $end = Carbon::createFromDate($year, $month, 15);
        } else {
            $start = Carbon::createFromDate($year, $month, 16);
            $end = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        }

        return [
            'start' => $start,
            'end' => $end,
        ];
    }
}
