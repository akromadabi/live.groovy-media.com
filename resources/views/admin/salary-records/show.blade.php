@extends('layouts.app')

@section('title', 'Detail Rekap Gaji')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <a href="{{ route('admin.salary-records.index') }}" class="btn btn-secondary btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
        <div class="flex gap-2">
            <button onclick="downloadAsImage()" class="btn btn-primary btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Download Gambar
            </button>
        </div>
    </div>

    <!-- Salary Slip Card (for download) -->
    <div id="salary-slip" style="max-width: 400px; margin: 0 auto;">
        <div class="salary-slip-card">
            <div class="slip-header">
                <div class="slip-logo">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="32" height="32">
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

    <!-- Actions -->
    <div class="flex justify-center gap-3 mt-4">
        <a href="{{ route('admin.salary-records.edit', $salaryRecord) }}" class="btn btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Edit
        </a>
        @if($salaryRecord->status !== 'paid')
            <form action="{{ route('admin.salary-records.mark-paid', $salaryRecord) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Tandai Dibayar
                </button>
            </form>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script>
        function downloadAsImage() {
            const element = document.getElementById('salary-slip');

            html2canvas(element, {
                backgroundColor: '#ffffff',
                scale: 2,
                useCORS: true,
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'slip-gaji-{{ $salaryRecord->user->name }}-{{ $salaryRecord->period_label }}-T{{ $salaryRecord->term }}.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            });
        }
    </script>
@endpush