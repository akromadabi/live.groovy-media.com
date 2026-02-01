<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attendance_date',
        'live_duration_minutes',
        'content_edit_count',
        'content_live_count',
        'sales_count',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
            'live_duration_minutes' => 'integer',
            'content_edit_count' => 'integer',
            'content_live_count' => 'integer',
            'sales_count' => 'integer',
        ];
    }

    /**
     * Get the user that owns the attendance
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the TikTok report detail that matched this attendance
     */
    public function tiktokReportDetail(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(TiktokReportDetail::class, 'matched_attendance_id');
    }

    /**
     * Get formatted duration as hours and minutes
     */
    public function getFormattedDurationAttribute(): string
    {
        $hours = floor($this->live_duration_minutes / 60);
        $minutes = $this->live_duration_minutes % 60;
        return sprintf('%d jam %d menit', $hours, $minutes);
    }

    /**
     * Scope for pending attendances
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for validated attendances
     */
    public function scopeValidated($query)
    {
        return $query->where('status', 'validated');
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('attendance_date', [$startDate, $endDate]);
    }
}
