<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TiktokReportDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'tiktok_report_id',
        'user_id',
        'live_date',
        'duration_minutes',
        'match_status',
        'matched_attendance_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'live_date' => 'date',
            'duration_minutes' => 'integer',
        ];
    }

    /**
     * Get the parent report
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(TiktokReport::class, 'tiktok_report_id');
    }

    /**
     * Get the associated user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the matched attendance
     */
    public function matchedAttendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class, 'matched_attendance_id');
    }

    /**
     * Check if matched
     */
    public function isMatched(): bool
    {
        return $this->match_status === 'matched';
    }

    /**
     * Check if needs verification
     */
    public function needsVerification(): bool
    {
        return $this->match_status === 'needs_verification';
    }
}
