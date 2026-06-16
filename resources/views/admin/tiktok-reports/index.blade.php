@extends('layouts.app')

@section('title', 'Report TikTok')

@section('content')
    <!-- Filter Bar -->
    <div class="card mb-4">
        <div class="card-body" style="padding: 1rem;">
            <form action="{{ route('admin.tiktok-reports.index') }}" method="GET" class="flex items-center gap-3 flex-wrap">
                <select name="year" class="form-control form-select" style="width: auto; min-width: 100px;" onchange="this.form.submit()">
                    @for($y = now()->year; $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <select name="month" class="form-control form-select" style="width: auto; min-width: 130px;" onchange="this.form.submit()">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('month', now()->month) == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
                <button type="button" data-modal="upload-modal" class="btn btn-success" style="margin-left: auto;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Upload Report
                </button>
            </form>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="stats-grid-mobile mb-4">
        <div class="stat-card-compact">
            <div class="stat-icon-sm primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $dailyData->count() }}</div>
                <div class="stat-label">Hari Tercatat</div>
            </div>
        </div>
        <div class="stat-card-compact">
            <div class="stat-icon-sm success">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ number_format($totalAbsenHours, 1) }} jam</div>
                <div class="stat-label">Total Absen</div>
            </div>
        </div>
        <div class="stat-card-compact">
            <div class="stat-icon-sm warning">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ number_format($totalTiktokHours, 1) }} jam</div>
                <div class="stat-label">Total TikTok</div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">Perbandingan Harian</h3>
        </div>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Absen</th>
                        <th>Report TikTok</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dailyData as $data)
                        <tr>
                            <td>
                                <div class="font-medium">{{ \Carbon\Carbon::parse($data->date)->translatedFormat('d M Y') }}
                                </div>
                                <div class="text-xs text-muted">{{ \Carbon\Carbon::parse($data->date)->translatedFormat('l') }}
                                </div>
                            </td>
                            <td>
                                @if($data->absen_minutes > 0)
                                    <span class="font-medium">{{ number_format($data->absen_minutes / 60, 1) }} jam</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($data->tiktok_minutes > 0)
                                    <span class="font-medium">{{ number_format($data->tiktok_minutes / 60, 1) }} jam</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $diff = $data->absen_minutes - $data->tiktok_minutes;
                                    $diffHours = abs($diff) / 60;
                                @endphp
                                @if($data->tiktok_minutes == 0 && $data->absen_minutes > 0)
                                    <span class="badge badge-warning">Belum ada data TikTok</span>
                                @elseif($data->absen_minutes == 0 && $data->tiktok_minutes > 0)
                                    <span class="badge badge-danger">Tidak ada absen</span>
                                @elseif($diff == 0)
                                    <span class="badge badge-success">Sesuai</span>
                                @elseif($diff > 0)
                                    <span class="badge badge-danger">Absen +{{ number_format($diffHours, 1) }} jam</span>
                                @else
                                    <span class="badge badge-primary">TikTok +{{ number_format($diffHours, 1) }} jam</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted" style="padding: 3rem;">
                                Tidak ada data untuk periode ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Uploaded Files List -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">File Report yang Diupload</h3>
            <button type="button" id="btn-bulk-delete" class="btn btn-danger btn-sm" style="display: none; align-items: center; gap: 0.35rem;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Hapus Terpilih (<span id="selected-count">0</span>)
            </button>
        </div>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 40px; text-align: center;"><input type="checkbox" id="select-all-files" style="cursor: pointer; width: 16px; height: 16px;"></th>
                        <th>Nama File</th>
                        <th class="hide-on-mobile">Tanggal Upload</th>
                        <th class="hide-on-mobile">Total Data</th>
                        <th>Total Durasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($uploadedFiles as $file)
                        <tr>
                            <td style="text-align: center;"><input type="checkbox" value="{{ $file->id }}" class="file-checkbox" style="cursor: pointer; width: 16px; height: 16px;"></td>
                            <td>
                                <div class="font-medium">{{ $file->original_filename ?? $file->filename }}</div>
                                <div class="text-xs text-muted">oleh {{ $file->uploader->name ?? 'Unknown' }}</div>
                            </td>
                            <td class="hide-on-mobile">{{ $file->created_at->format('d M Y H:i') }}</td>
                            <td class="hide-on-mobile">{{ $file->total_records ?? $file->details()->count() }} data</td>
                            <td>{{ number_format(($file->total_duration_minutes ?? $file->details()->sum('duration_minutes')) / 60, 1) }}
                                jam</td>
                            <td>
                                <form action="{{ route('admin.tiktok-reports.destroy', $file) }}" method="POST"
                                    style="display:inline;" id="delete-file-{{ $file->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete('delete-file-{{ $file->id }}')"
                                        class="btn btn-danger btn-sm btn-icon" title="Hapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted" style="padding: 2rem;">
                                Belum ada file diupload
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bulk Delete Hidden Form -->
    <form action="{{ route('admin.tiktok-reports.bulk-delete') }}" method="POST" id="bulk-delete-form" style="display: none;">
        @csrf
        @method('DELETE')
        <input type="hidden" name="ids" id="bulk-delete-ids">
    </form>

    <!-- Upload Modal -->
    <div class="modal-overlay" id="upload-modal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Upload Report TikTok</h3>
                <button type="button" class="modal-close" data-modal-close>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20"
                        height="20">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form action="{{ route('admin.tiktok-reports.store') }}" method="POST" enctype="multipart/form-data" id="upload-form">
                @csrf
                <div class="modal-body" id="form-fields-container">
                    <div class="form-group">
                        <label class="form-label">File Excel (.xlsx)</label>
                        <div id="drop-zone"
                            style="border: 2px dashed var(--border-color, #ddd); border-radius: 12px; padding: 2rem; text-align: center; cursor: pointer; transition: all 0.3s ease; background: var(--card-bg, #f8f9fa);"
                            onclick="document.getElementById('report-files-input').click()">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                width="40" height="40" style="margin: 0 auto 0.5rem; display: block; opacity: 0.5;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <div style="font-weight: 600; margin-bottom: 0.25rem;">Klik atau drag file ke sini</div>
                            <div style="font-size: 0.85rem; opacity: 0.7;">Bisa pilih lebih dari 1 file Excel sekaligus</div>
                        </div>
                        <input type="file" name="report_files[]" id="report-files-input" class="form-control"
                            accept=".xlsx,.xls" multiple required style="display: none;">
                        <div id="selected-files-list" style="margin-top: 0.75rem; display: none;">
                            <div
                                style="font-weight: 600; font-size: 0.85rem; margin-bottom: 0.5rem; color: var(--primary-color, #4f46e5);">
                                📎 <span id="file-count">0</span> file dipilih:
                            </div>
                            <div id="file-names" style="font-size: 0.8rem; max-height: 150px; overflow-y: auto;"></div>
                        </div>
                    </div>
                    
                    <!-- Clean Format File Listing (Hapus Box Alert) -->
                    <div style="margin-top: 1.25rem; border-top: 1px solid var(--border-color, #eee); padding-top: 1rem; font-size: 0.85rem;">
                        <strong style="display: block; margin-bottom: 0.5rem; color: var(--text-color, #1e293b);">📌 Format File:</strong>
                        <div style="opacity: 0.8; line-height: 1.6; color: var(--text-color, #475569);">
                            File harus memiliki kolom:
                            <ul style="margin: 0.35rem 0 0 1.25rem; padding: 0; list-style-type: disc;">
                                <li style="margin-bottom: 0.25rem;"><strong>Start time</strong> - Format: <code style="font-family: monospace; background: rgba(0,0,0,0.05); padding: 0.1rem 0.3rem; border-radius: 4px; font-size: 0.8rem;">2026-01-29 19:16</code></li>
                                <li><strong>Duration</strong> - Nilai dalam detik</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Progress Container (Show during upload) -->
                <div class="modal-body" id="upload-progress-container" style="display: none; padding: 2.5rem 1.5rem; text-align: center;">
                    <style>
                        @keyframes pulse {
                            0% { opacity: 0.6; }
                            50% { opacity: 1; }
                            100% { opacity: 0.6; }
                        }
                    </style>
                    <div class="progress-circle-wrapper" style="position: relative; width: 120px; height: 120px; margin: 0 auto 1.5rem;">
                        <svg width="120" height="120" viewBox="0 0 120 120" style="transform: rotate(-90deg); filter: drop-shadow(0 4px 6px rgba(79, 70, 229, 0.15));">
                            <!-- Background circle -->
                            <circle cx="60" cy="60" r="50" stroke="var(--border-color, #f1f5f9)" stroke-width="8" fill="transparent" />
                            <!-- Foreground circle -->
                            <circle cx="60" cy="60" r="50" stroke="url(#progress-gradient)" stroke-width="8" fill="transparent"
                                stroke-dasharray="314.16" stroke-dashoffset="314.16" id="progress-ring-circle"
                                stroke-linecap="round" style="transition: stroke-dashoffset 0.15s ease-out;" />
                            <defs>
                                <linearGradient id="progress-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#818cf8" />
                                    <stop offset="100%" stop-color="#4f46e5" />
                                </linearGradient>
                            </defs>
                        </svg>
                        <div id="progress-percent" style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; font-weight: 800; color: #4f46e5; text-shadow: 0 1px 1px rgba(255,255,255,0.8);">0%</div>
                    </div>
                    
                    <h4 id="progress-status-title" style="margin: 0 0 0.5rem; font-weight: 700; font-size: 1.2rem; color: var(--text-color, #1e293b);">Mengupload File...</h4>
                    <p id="progress-status-subtitle" style="font-size: 0.875rem; color: #64748b; margin: 0 0 1.5rem;">0 KB dari 0 KB</p>
                    
                    <!-- Progress Line -->
                    <div style="width: 100%; height: 8px; background: #f1f5f9; border-radius: 999px; overflow: hidden; position: relative; border: 1px solid rgba(0,0,0,0.03);">
                        <div id="progress-line" style="width: 0%; height: 100%; background: linear-gradient(90deg, #818cf8, #4f46e5); border-radius: 999px; transition: width 0.15s ease-out;"></div>
                    </div>
                </div>

                <div class="modal-footer" id="modal-actions-footer">
                    <button type="button" class="btn btn-secondary" data-modal-close>Batal</button>
                    <button type="submit" class="btn btn-primary" id="upload-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Upload & Proses
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fileInput = document.getElementById('report-files-input');
            const fileList = document.getElementById('selected-files-list');
            const fileNames = document.getElementById('file-names');
            const fileCount = document.getElementById('file-count');
            const dropZone = document.getElementById('drop-zone');
            const uploadBtn = document.getElementById('upload-btn');
            
            const uploadForm = document.getElementById('upload-form');
            const formFieldsContainer = document.getElementById('form-fields-container');
            const progressContainer = document.getElementById('upload-progress-container');
            const modalActionsFooter = document.getElementById('modal-actions-footer');
            const modalCloseBtn = document.querySelector('.modal-close');
            
            const progressRingCircle = document.getElementById('progress-ring-circle');
            const progressPercent = document.getElementById('progress-percent');
            const progressStatusTitle = document.getElementById('progress-status-title');
            const progressStatusSubtitle = document.getElementById('progress-status-subtitle');
            const progressLine = document.getElementById('progress-line');

            // Circumference of the circle ring (2 * PI * r) where r = 50
            const circumference = 2 * Math.PI * 50; // ~314.16

            function setProgress(percent) {
                percent = Math.max(0, Math.min(100, percent));
                progressPercent.textContent = Math.round(percent) + '%';
                progressLine.style.width = percent + '%';
                const offset = circumference - (percent / 100 * circumference);
                progressRingCircle.style.strokeDashoffset = offset;
            }

            fileInput.addEventListener('change', function () {
                updateFileList(this.files);
            });

            // Drag and drop
            dropZone.addEventListener('dragover', function (e) {
                e.preventDefault();
                this.style.borderColor = 'var(--primary-color, #4f46e5)';
                this.style.background = 'var(--primary-light, rgba(79,70,229,0.05))';
            });

            dropZone.addEventListener('dragleave', function (e) {
                e.preventDefault();
                this.style.borderColor = 'var(--border-color, #ddd)';
                this.style.background = 'var(--card-bg, #f8f9fa)';
            });

            dropZone.addEventListener('drop', function (e) {
                e.preventDefault();
                this.style.borderColor = 'var(--border-color, #ddd)';
                this.style.background = 'var(--card-bg, #f8f9fa)';

                const dt = new DataTransfer();
                for (let f of e.dataTransfer.files) {
                    if (f.name.match(/\.(xlsx|xls)$/i)) {
                        dt.items.add(f);
                    }
                }
                fileInput.files = dt.files;
                updateFileList(dt.files);
            });

            function updateFileList(files) {
                if (files.length > 0) {
                    fileList.style.display = 'block';
                    fileCount.textContent = files.length;
                    fileNames.innerHTML = '';
                    for (let i = 0; i < files.length; i++) {
                        const div = document.createElement('div');
                        div.style.cssText = 'padding: 0.35rem 0.6rem; background: var(--card-bg, #f0f0f0); border-radius: 6px; margin-bottom: 0.3rem; display: flex; align-items: center; gap: 0.4rem;';
                        div.innerHTML = '<span style="color: #22c55e;">📄</span> ' + files[i].name +
                            ' <span style="opacity:0.5; font-size:0.75rem;">(' + (files[i].size / 1024).toFixed(0) + ' KB)</span>';
                        fileNames.appendChild(div);
                    }
                    dropZone.querySelector('div:first-of-type').textContent = files.length + ' file dipilih';
                    uploadBtn.textContent = 'Upload ' + files.length + ' File & Proses';
                } else {
                    fileList.style.display = 'none';
                    dropZone.querySelector('div:first-of-type').textContent = 'Klik atau drag file ke sini';
                }
            }

            // AJAX form submission with progress percentage UI
            uploadForm.addEventListener('submit', function (e) {
                if (!fileInput.files || fileInput.files.length === 0) {
                    return;
                }
                
                e.preventDefault();
                
                // Hide input fields, close button, and footer actions
                formFieldsContainer.style.display = 'none';
                modalActionsFooter.style.display = 'none';
                if (modalCloseBtn) modalCloseBtn.style.display = 'none';
                
                // Show progress container and reset status
                progressContainer.style.display = 'block';
                setProgress(0);
                
                const formData = new FormData(uploadForm);
                const xhr = new XMLHttpRequest();
                xhr.open('POST', uploadForm.action, true);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                
                xhr.upload.addEventListener('progress', function (e) {
                    if (e.lengthComputable) {
                        const percentComplete = (e.loaded / e.total) * 100;
                        setProgress(percentComplete);
                        
                        const uploaded = (e.loaded / 1024 / 1024).toFixed(2);
                        const total = (e.total / 1024 / 1024).toFixed(2);
                        
                        if (percentComplete < 100) {
                            progressStatusTitle.textContent = 'Mengupload File...';
                            progressStatusSubtitle.textContent = `Mengirim data: ${uploaded} MB dari ${total} MB`;
                        } else {
                            progressStatusTitle.textContent = 'Memproses & Menganalisis Excel...';
                            progressStatusSubtitle.textContent = 'Harap tunggu, data sedang disimpan ke database...';
                            progressRingCircle.style.animation = 'pulse 1.5s infinite ease-in-out';
                        }
                    }
                });
                
                xhr.onload = function () {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.success && response.redirect) {
                                progressStatusTitle.textContent = 'Selesai!';
                                progressStatusSubtitle.textContent = 'Mengalihkan halaman...';
                                setProgress(100);
                                setTimeout(() => {
                                    window.location.href = response.redirect;
                                }, 500);
                            } else {
                                handleUploadError(response.message || 'Terjadi kesalahan saat memproses file.');
                            }
                        } catch (err) {
                            handleUploadError('Terjadi kesalahan tidak terduga pada server.');
                        }
                    } else {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            handleUploadError(response.message || 'Terjadi kesalahan HTTP status ' + xhr.status);
                        } catch (err) {
                            handleUploadError('Terjadi kesalahan koneksi server (HTTP ' + xhr.status + ').');
                        }
                    }
                };
                
                xhr.onerror = function () {
                    handleUploadError('Koneksi internet terputus atau server tidak dapat dijangkau.');
                };
                
                xhr.send(formData);
            });

            function handleUploadError(errorMessage) {
                progressStatusTitle.textContent = 'Upload Gagal!';
                progressStatusTitle.style.color = '#ef4444';
                progressStatusSubtitle.textContent = errorMessage;
                progressStatusSubtitle.style.color = '#ef4444';
                
                progressLine.style.background = '#ef4444';
                progressRingCircle.style.stroke = '#ef4444';
                progressPercent.style.color = '#ef4444';
                
                modalActionsFooter.innerHTML = '<button type="button" class="btn btn-secondary" onclick="window.location.reload()">Tutup & Ulangi</button>';
                modalActionsFooter.style.display = 'flex';
                if (modalCloseBtn) {
                    modalCloseBtn.style.display = 'block';
                    modalCloseBtn.setAttribute('onclick', 'window.location.reload()');
                }
            }

            // Bulk Delete Logic
            const selectAllCheckbox = document.getElementById('select-all-files');
            const fileCheckboxes = document.querySelectorAll('.file-checkbox');
            const btnBulkDelete = document.getElementById('btn-bulk-delete');
            const selectedCountSpan = document.getElementById('selected-count');
            const bulkDeleteForm = document.getElementById('bulk-delete-form');
            const bulkDeleteIdsInput = document.getElementById('bulk-delete-ids');

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function () {
                    const isChecked = this.checked;
                    fileCheckboxes.forEach(checkbox => {
                        checkbox.checked = isChecked;
                    });
                    updateBulkDeleteButton();
                });

                fileCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function () {
                        updateBulkDeleteButton();
                        
                        const allChecked = Array.from(fileCheckboxes).every(cb => cb.checked);
                        selectAllCheckbox.checked = allChecked;
                    });
                });
            }

            function updateBulkDeleteButton() {
                const checkedCheckboxes = document.querySelectorAll('.file-checkbox:checked');
                const count = checkedCheckboxes.length;
                
                if (count > 0) {
                    selectedCountSpan.textContent = count;
                    btnBulkDelete.style.display = 'flex';
                } else {
                    btnBulkDelete.style.display = 'none';
                }
            }

            if (btnBulkDelete) {
                btnBulkDelete.addEventListener('click', function () {
                    const checkedCheckboxes = document.querySelectorAll('.file-checkbox:checked');
                    const count = checkedCheckboxes.length;
                    
                    if (count === 0) return;
                    
                    if (confirm(`Apakah Anda yakin ingin menghapus ${count} file report terpilih secara permanen? Data perbandingan live juga akan ikut dihapus.`)) {
                        const ids = Array.from(checkedCheckboxes).map(cb => cb.value).join(',');
                        bulkDeleteIdsInput.value = ids;
                        
                        btnBulkDelete.disabled = true;
                        btnBulkDelete.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" style="width: 14px; height: 14px; margin-right: 0.25rem;"></span> Menghapus...';
                        
                        bulkDeleteForm.submit();
                    }
                });
            }
        });
    </script>
@endsection