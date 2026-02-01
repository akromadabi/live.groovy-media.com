<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - {{ $salaryRecord->user->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        .actions {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background: #6366f1;
            color: white;
        }

        .btn-primary:hover {
            background: #4f46e5;
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #374151;
        }

        .btn-secondary:hover {
            background: #d1d5db;
        }

        /* Match show.blade.php styling exactly */
        #salary-slip {
            max-width: 400px;
            margin: 0 auto;
        }

        .salary-slip-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            padding: 24px;
            width: 100%;
            color: white;
            box-shadow: 0 10px 40px rgba(102, 126, 234, 0.4);
        }

        .slip-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .slip-logo {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .slip-title h2 {
            font-size: 18px;
            font-weight: 600;
        }

        .slip-title p {
            font-size: 12px;
            opacity: 0.8;
        }

        .slip-body {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 16px;
        }

        .slip-info {
            margin-bottom: 12px;
        }

        .slip-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 14px;
        }

        .slip-label {
            opacity: 0.8;
        }

        .slip-value {
            font-weight: 500;
        }

        .slip-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.2);
            margin: 12px 0;
        }

        .slip-amount-section {
            text-align: center;
            padding: 16px 0;
        }

        .slip-amount-label {
            font-size: 12px;
            opacity: 0.8;
            display: block;
            margin-bottom: 4px;
        }

        .slip-amount {
            font-size: 28px;
            font-weight: 700;
        }

        .slip-status {
            text-align: center;
            margin-top: 12px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-badge.paid {
            background: rgba(16, 185, 129, 0.3);
            color: #a7f3d0;
        }

        .status-badge.pending {
            background: rgba(251, 191, 36, 0.3);
            color: #fde68a;
        }

        .status-date {
            display: block;
            font-size: 11px;
            opacity: 0.7;
            margin-top: 4px;
        }

        .slip-notes {
            margin-top: 12px;
            padding: 12px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
        }

        .notes-label {
            font-size: 11px;
            opacity: 0.7;
            display: block;
            margin-bottom: 4px;
        }

        .slip-notes p {
            font-size: 13px;
        }

        .slip-footer {
            text-align: center;
            margin-top: 16px;
            font-size: 11px;
            opacity: 0.6;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .actions {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="actions">
        <button onclick="downloadAsImage()" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18"
                height="18">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Download Gambar
        </button>
        <button onclick="window.close()" class="btn btn-secondary">Tutup</button>
    </div>

    <div id="salary-slip">
        <div class="salary-slip-card">
            <div class="slip-header">
                <div class="slip-logo">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="32"
                        height="32">
                        <path
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                    </svg>
                </div>
                <div class="slip-title">
                    <h2>Slip Gaji</h2>
                    <p>TikTok Live Manager</p>
                </div>
            </div>

            <div class="slip-body">
                <div class="slip-info">
                    <div class="slip-row">
                        <span class="slip-label">Nama</span>
                        <span class="slip-value">{{ $salaryRecord->user->name }}</span>
                    </div>
                    <div class="slip-row">
                        <span class="slip-label">Jabatan</span>
                        <span class="slip-value">{{ $salaryRecord->user->task ?? 'Host' }}</span>
                    </div>
                    <div class="slip-row">
                        <span class="slip-label">Periode</span>
                        <span class="slip-value">{{ $salaryRecord->period_label }}</span>
                    </div>
                    <div class="slip-row">
                        <span class="slip-label">Termin</span>
                        <span class="slip-value">{{ $salaryRecord->term_label }}</span>
                    </div>
                </div>

                <div class="slip-divider"></div>

                <!-- Attendance Details -->
                <div class="slip-info">
                    <div class="slip-row">
                        <span class="slip-label">Total Jam</span>
                        <span class="slip-value">{{ number_format($salaryRecord->total_hours, 1) }} jam</span>
                    </div>
                    <div class="slip-row">
                        <span class="slip-label">Jumlah Live</span>
                        <span class="slip-value">{{ $salaryRecord->total_live_count }}x</span>
                    </div>
                    <div class="slip-row">
                        <span class="slip-label">Penjualan</span>
                        <span class="slip-value">{{ $salaryRecord->total_sales }}</span>
                    </div>
                    @if($salaryRecord->total_content_edit || $salaryRecord->total_content_live)
                        <div class="slip-row">
                            <span class="slip-label">Konten Edit</span>
                            <span class="slip-value">{{ $salaryRecord->total_content_edit }}</span>
                        </div>
                        <div class="slip-row">
                            <span class="slip-label">Konten Live</span>
                            <span class="slip-value">{{ $salaryRecord->total_content_live }}</span>
                        </div>
                    @endif
                </div>

                <div class="slip-divider"></div>

                <div class="slip-amount-section">
                    <span class="slip-amount-label">Total Gaji</span>
                    <span class="slip-amount">{{ $salaryRecord->formatted_amount }}</span>
                </div>

                <div class="slip-divider"></div>

                <div class="slip-status">
                    @if($salaryRecord->status === 'paid')
                        <span class="status-badge paid">✓ DIBAYAR</span>
                        @if($salaryRecord->paid_at)
                            <span class="status-date">{{ $salaryRecord->paid_at->format('d M Y') }}</span>
                        @endif
                    @else
                        <span class="status-badge pending">PENDING</span>
                    @endif
                </div>

                @if($salaryRecord->notes)
                    <div class="slip-notes">
                        <span class="notes-label">Catatan:</span>
                        <p>{{ $salaryRecord->notes }}</p>
                    </div>
                @endif
            </div>

            <div class="slip-footer">
                <p>Dicetak pada {{ now()->format('d M Y H:i') }}</p>
            </div>
        </div>
    </div>

    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script>
        function downloadAsImage(autoClose = false) {
            const element = document.getElementById('salary-slip');

            html2canvas(element, {
                backgroundColor: '#ffffff',
                scale: 2,
                useCORS: true,
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'slip-gaji-{{ Str::slug($salaryRecord->user->name) }}-{{ $salaryRecord->period_label }}-T{{ $salaryRecord->term }}.png';
                link.href = canvas.toDataURL('image/png');
                link.click();

                // Close window after download if auto mode
                if (autoClose) {
                    setTimeout(() => window.close(), 1000);
                }
            });
        }

        // Auto-download when page is opened with ?auto=1 parameter
        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('auto') === '1') {
                // Wait for fonts and styles to fully load
                setTimeout(() => {
                    downloadAsImage(true);
                }, 800);
            }
        });
    </script>

</body>

</html>