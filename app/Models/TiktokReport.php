<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TiktokReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'original_filename',
        'report_date',
        'report_data',
        'uploaded_by',
        'total_records',
        'total_duration_minutes',
    ];

    protected function casts(): array
    {
        return [
            'report_date' => 'date',
            'report_data' => 'json',
            'total_records' => 'integer',
            'total_duration_minutes' => 'integer',
        ];
    }

    /**
     * Get the admin who uploaded this report
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get report details
     */
    public function details(): HasMany
    {
        return $this->hasMany(TiktokReportDetail::class);
    }
}
