/**
 * Perceptual Image Compression Engine (Client-Side)
 * Adapted from app-sekolah WhatsApp-HD compression engine.
 * 
 * Compresses camera photos (12MP-48MP) to ~100-220KB crystal clear Web/Mobile JPEG.
 */
(function (global) {
    'use strict';

    const DEFAULT_MAX_DIMENSION = 1400;
    const DEFAULT_INITIAL_QUALITY = 0.82;
    const MIN_QUALITY_FLOOR = 0.72;
    const DEFAULT_TARGET_KB = 200;

    function renderMultiStepDownscale(source, srcWidth, srcHeight, targetWidth, targetHeight) {
        let curWidth = srcWidth;
        let curHeight = srcHeight;
        let curSource = source;

        while (curWidth > targetWidth * 1.8 || curHeight > targetHeight * 1.8) {
            const nextWidth = Math.max(targetWidth, Math.round(curWidth * 0.5));
            const nextHeight = Math.max(targetHeight, Math.round(curHeight * 0.5));

            const stepCanvas = document.createElement('canvas');
            stepCanvas.width = nextWidth;
            stepCanvas.height = nextHeight;

            const stepCtx = stepCanvas.getContext('2d', { alpha: false });
            stepCtx.imageSmoothingEnabled = true;
            stepCtx.imageSmoothingQuality = 'high';
            stepCtx.fillStyle = '#FFFFFF';
            stepCtx.fillRect(0, 0, nextWidth, nextHeight);
            stepCtx.drawImage(curSource, 0, 0, curWidth, curHeight, 0, 0, nextWidth, nextHeight);

            curSource = stepCanvas;
            curWidth = nextWidth;
            curHeight = nextHeight;
        }

        const finalCanvas = document.createElement('canvas');
        finalCanvas.width = targetWidth;
        finalCanvas.height = targetHeight;

        const finalCtx = finalCanvas.getContext('2d', { willReadFrequently: true });
        finalCtx.imageSmoothingEnabled = true;
        finalCtx.imageSmoothingQuality = 'high';
        finalCtx.fillStyle = '#FFFFFF';
        finalCtx.fillRect(0, 0, targetWidth, targetHeight);
        finalCtx.drawImage(curSource, 0, 0, curWidth, curHeight, 0, 0, targetWidth, targetHeight);

        return { canvas: finalCanvas, ctx: finalCtx };
    }

    function applyPerceptualSharpen(ctx, width, height, amount = 0.20, threshold = 4) {
        if (width < 64 || height < 64 || amount <= 0) return;

        try {
            const imgData = ctx.getImageData(0, 0, width, height);
            const src = imgData.data;
            const copy = new Uint8ClampedArray(src);
            const stride = width * 4;

            for (let y = 1; y < height - 1; y++) {
                const rowOffset = y * stride;
                for (let x = 1; x < width - 1; x++) {
                    const idx = rowOffset + (x << 2);
                    for (let c = 0; c < 3; c++) {
                        const center = copy[idx + c];
                        const neighbors = (
                            copy[idx - 4 + c] +
                            copy[idx + 4 + c] +
                            copy[idx - stride + c] +
                            copy[idx + stride + c]
                        ) * 0.25;

                        const diff = center - neighbors;
                        if (Math.abs(diff) >= threshold) {
                            const sharpened = center + diff * (amount * 3.0);
                            src[idx + c] = sharpened < 0 ? 0 : (sharpened > 255 ? 255 : sharpened);
                        }
                    }
                }
            }

            ctx.putImageData(imgData, 0, 0);
        } catch (_) {}
    }

    async function decodeImageSource(file) {
        if (typeof createImageBitmap === 'function') {
            try {
                const bitmap = await createImageBitmap(file);
                return {
                    source: bitmap,
                    width: bitmap.width,
                    height: bitmap.height,
                    cleanup: () => bitmap.close && bitmap.close(),
                };
            } catch (_) {}
        }

        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = (event) => {
                const img = new Image();
                img.onload = () => {
                    resolve({
                        source: img,
                        width: img.naturalWidth || img.width,
                        height: img.naturalHeight || img.height,
                        cleanup: () => {},
                    });
                };
                img.onerror = (err) => reject(err);
                img.src = event.target.result;
            };
            reader.onerror = (err) => reject(err);
            reader.readAsDataURL(file);
        });
    }

    function computeTargetDimensions(srcWidth, srcHeight, maxDimension) {
        let width = srcWidth;
        let height = srcHeight;

        if (width > maxDimension || height > maxDimension) {
            if (width >= height) {
                height = Math.round(height * (maxDimension / width));
                width = maxDimension;
            } else {
                width = Math.round(width * (maxDimension / height));
                height = maxDimension;
            }
        }

        return { width: Math.max(1, width), height: Math.max(1, height) };
    }

    function encodeSmartBlob(canvas, initialQuality = DEFAULT_INITIAL_QUALITY, targetKb = DEFAULT_TARGET_KB) {
        return new Promise((resolve) => {
            const maxAcceptableBytes = targetKb * 1024 * 1.35;

            const tryEncode = (q) => {
                canvas.toBlob(
                    (blob) => {
                        if (!blob) return resolve(null);
                        if (blob.size <= maxAcceptableBytes || q <= MIN_QUALITY_FLOOR) {
                            return resolve(blob);
                        }
                        const nextQ = Math.max(MIN_QUALITY_FLOOR, Number((q - 0.05).toFixed(2)));
                        if (nextQ === q) return resolve(blob);
                        tryEncode(nextQ);
                    },
                    'image/jpeg',
                    q
                );
            };

            tryEncode(initialQuality);
        });
    }

    function formatBytes(bytes) {
        if (!bytes || bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    }

    async function compressImage(file, maxDimension = DEFAULT_MAX_DIMENSION, quality = DEFAULT_INITIAL_QUALITY) {
        if (!file || !file.type || !file.type.startsWith('image/')) {
            return file;
        }

        if (file.type === 'image/gif' || file.type === 'image/svg+xml') {
            return file;
        }

        try {
            const decoded = await decodeImageSource(file);
            const { source, width: srcW, height: srcH, cleanup } = decoded;

            // If already small JPEG within size limit
            if (
                (file.type === 'image/jpeg' || file.type === 'image/webp') &&
                file.size > 0 &&
                file.size <= 200 * 1024 &&
                srcW <= maxDimension &&
                srcH <= maxDimension
            ) {
                cleanup();
                return file;
            }

            const { width, height } = computeTargetDimensions(srcW, srcH, maxDimension);
            const wasDownscaled = width < srcW || height < srcH;

            const { canvas, ctx } = renderMultiStepDownscale(source, srcW, srcH, width, height);
            cleanup();

            applyPerceptualSharpen(ctx, width, height, wasDownscaled ? 0.20 : 0.10, 4);

            const blob = await encodeSmartBlob(canvas, quality, DEFAULT_TARGET_KB);
            if (!blob) return file;

            if (!wasDownscaled && file.type === 'image/jpeg' && file.size > 0 && blob.size >= file.size) {
                return file;
            }

            const newFilename = file.name ? file.name.replace(/\.[^/.]+$/, '.jpg') : 'compressed_screenshot.jpg';
            return new File([blob], newFilename, {
                type: 'image/jpeg',
                lastModified: Date.now(),
            });
        } catch (err) {
            console.warn('Image compression fallback:', err);
            return file;
        }
    }

    /**
     * Binds an <input type="file"> to auto-compress on change
     * and shows a neat live thumbnail preview with size savings.
     */
    function attachToFileInput(inputEl, previewEl, options = {}) {
        if (!inputEl) return;

        inputEl.addEventListener('change', async function (e) {
            const file = this.files && this.files[0];
            if (!file || !file.type.startsWith('image/')) {
                if (previewEl) previewEl.innerHTML = '';
                return;
            }

            const originalSize = file.size;

            if (previewEl) {
                previewEl.innerHTML = `
                    <div style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; background: rgba(59, 130, 246, 0.08); border-radius: 6px; font-size: 0.8rem; color: #2563eb; margin-top: 8px;">
                        <svg class="animate-spin" style="width: 16px; height: 16px; animation: spin 1s linear infinite;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Mengompres bukti screenshot (${formatBytes(originalSize)})...</span>
                    </div>
                `;
            }

            try {
                const compressedFile = await compressImage(file, options.maxDimension || DEFAULT_MAX_DIMENSION, options.quality || DEFAULT_INITIAL_QUALITY);

                // Replace input files via DataTransfer
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(compressedFile);
                inputEl.files = dataTransfer.files;

                const compressedSize = compressedFile.size;
                const savings = Math.max(0, Math.round(((originalSize - compressedSize) / originalSize) * 100));
                const previewUrl = URL.createObjectURL(compressedFile);

                if (previewEl) {
                    previewEl.innerHTML = `
                        <div style="margin-top: 10px; border: 1px solid var(--border); border-radius: 8px; padding: 8px; background: var(--bg-hover);">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <img src="${previewUrl}" alt="Preview" style="width: 58px; height: 58px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border);" />
                                <div style="flex: 1; min-width: 0;">
                                    <div style="font-size: 0.8rem; font-weight: 600; color: var(--text);">Foto Siap Diupload</div>
                                    <div style="font-size: 0.72rem; color: var(--text-muted);">
                                        Ukuran: <strong style="color: #10b981;">${formatBytes(compressedSize)}</strong>
                                        ${savings > 5 ? `<span style="color: #059669; font-weight: 600;">(Hemat ${savings}%)</span>` : ''}
                                    </div>
                                    <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 2px;">
                                        Otomatis terkompresi HD tanpa pecah.
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }
            } catch (err) {
                console.error('Compression error:', err);
                if (previewEl) previewEl.innerHTML = '';
            }
        });
    }

    global.ImageCompressor = {
        compress: compressImage,
        attachToFileInput: attachToFileInput,
        formatBytes: formatBytes
    };

})(window);
