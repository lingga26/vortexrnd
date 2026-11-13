@extends('layouts.app')

@section('title', 'Edit Jadwal Tayang')
@section('page-title', 'Iklan > Jadwal Tayang > Edit Jadwal')

@section('content')
@php
    $jadwalItem = $jadwal ?? $jadwalTayang ?? null;
    $daftarVideo = $fileIklan ?? $fileIklans ?? $videos ?? $iklan ?? $dataIklan ?? null;
    if (empty($daftarVideo) || (is_countable($daftarVideo) && count($daftarVideo) === 0)) {
        $daftarVideo = \App\Models\FileIklan::where('aktif', true)->orderBy('nama_iklan', 'asc')->get();
    }

    $videoMap = [];
    foreach ($daftarVideo as $v) {
        $namaV = $v->nama_iklan ?? ('Video #' . $v->id);
        $pelangganV = $v->pelanggan ?? '-';
        $tipeV = $v->tipe ?? 'CONTENT';
        $fullLabel = $namaV . ' -- ' . $pelangganV . ' (' . $tipeV . ')';
        $videoMap[$v->id] = [
            'id' => $v->id,
            'nama' => $namaV,
            'pelanggan' => $pelangganV,
            'tipe' => $tipeV,
            'full_label' => $fullLabel,
            'search' => strtolower($fullLabel),
        ];
    }

    $existingIds = [];
    if ($jadwalItem && method_exists($jadwalItem, 'fileIklan')) {
        $existingIds = $jadwalItem->fileIklan()->orderByPivot('urutan', 'asc')->pluck('file_iklan.id')->toArray();
    }

    $oldVideos = array_values(array_filter((array) old('file_iklan_ids', $existingIds)));
    $initialSlotCount = max(5, count($oldVideos));
@endphp

<style>
    .jadwal-breadcrumb {
        display: none;
    }

    .jadwal-card {
        background-color: var(--bg-card) !important;
        border: 1px solid var(--border) !important;
        border-radius: var(--radius-card) !important;
        padding: 28px;
        box-shadow: var(--shadow-card) !important;
    }

    .jadwal-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 22px;
    }

    .jadwal-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background-color: rgba(160, 104, 65, 0.15) !important;
        color: var(--accent) !important;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .jadwal-header-title {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--ink) !important;
        margin: 0;
    }

    .jadwal-form-group {
        margin-bottom: 22px;
    }

    .jadwal-label {
        display: block;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--muted) !important;
        margin-bottom: 8px;
    }

    .jadwal-input,
    .jadwal-textarea {
        width: 100%;
        padding: 13px 16px;
        border-radius: 12px;
        border: 1px solid var(--border) !important;
        background-color: var(--bg-card) !important;
        color: var(--ink) !important;
        font-size: 14px;
        font-weight: 600;
        outline: none;
        transition: all 0.2s ease;
        box-sizing: border-box;
    }

    .jadwal-input:focus,
    .jadwal-textarea:focus {
        border-color: var(--accent) !important;
        box-shadow: 0 0 0 3px rgba(160, 104, 65, 0.18) !important;
    }

    .video-section-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 30px;
        margin-bottom: 16px;
        padding-top: 20px;
        border-top: 1px solid var(--border);
    }

    .video-section-title {
        font-size: 0.95rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--ink) !important;
        margin-bottom: 4px;
    }

    .video-section-desc {
        font-size: 0.84rem;
        color: var(--muted) !important;
    }

    .btn-add-slot {
        padding: 10px 20px;
        border-radius: var(--radius-pill) !important;
        border: 1px solid var(--accent) !important;
        background-color: rgba(160, 104, 65, 0.12) !important;
        color: var(--accent) !important;
        font-size: 0.84rem;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }

    .btn-add-slot:hover {
        background-color: var(--accent) !important;
        color: #ffffff !important;
        transform: translateY(-1px);
    }

    .video-row-item {
        background-color: var(--track) !important;
        border: 1px solid var(--border) !important;
        border-radius: 12px;
        padding: 12px 18px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 14px;
        transition: background-color 0.2s ease, border-color 0.2s ease;
        position: relative;
    }

    .video-row-number {
        font-weight: 800;
        font-size: 0.9rem;
        color: var(--muted) !important;
        min-width: 68px;
        white-space: nowrap;
    }

    .video-select-wrap {
        flex: 1;
        min-width: 240px;
        position: relative;
    }

    .video-search-box {
        position: relative;
        display: flex;
        align-items: center;
    }

    .video-search-icon {
        position: absolute;
        left: 14px;
        color: var(--muted);
        font-size: 0.85rem;
        pointer-events: none;
    }

    .video-search-chevron {
        position: absolute;
        right: 14px;
        color: var(--muted);
        font-size: 0.8rem;
        pointer-events: none;
        transition: transform 0.2s ease;
    }

    .video-select-wrap.open .video-search-chevron {
        transform: rotate(180deg);
    }

    .video-search-input {
        width: 100%;
        padding: 11px 36px 11px 38px;
        border-radius: 10px;
        border: 1px solid var(--border) !important;
        background-color: var(--bg-card) !important;
        color: var(--ink) !important;
        font-size: 0.92rem;
        font-weight: 600;
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .video-search-input:focus {
        border-color: var(--accent) !important;
        box-shadow: 0 0 0 3px rgba(160, 104, 65, 0.18) !important;
    }

    .video-search-input::placeholder {
        color: var(--muted) !important;
    }

    .video-dropdown-menu {
        display: none;
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        right: 0;
        max-height: 250px;
        overflow-y: auto;
        background-color: var(--bg-card) !important;
        border: 1px solid var(--border) !important;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        z-index: 999;
        padding: 6px;
    }

    .video-select-wrap.open .video-dropdown-menu {
        display: block;
    }

    .video-dropdown-item {
        padding: 10px 14px;
        border-radius: 8px;
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--ink) !important;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        transition: background-color 0.15s ease;
    }

    .video-dropdown-item:hover {
        background-color: rgba(160, 104, 65, 0.15) !important;
        color: var(--accent) !important;
    }

    .video-dropdown-item.selected-item {
        background-color: rgba(160, 104, 65, 0.18) !important;
        color: var(--accent) !important;
    }

    .video-type-badge {
        font-size: 0.74rem;
        padding: 3px 10px;
        border-radius: var(--radius-pill);
        background-color: rgba(160, 104, 65, 0.15) !important;
        color: var(--accent) !important;
        font-weight: 700;
        white-space: nowrap;
    }

    .video-dropdown-empty {
        padding: 12px;
        text-align: center;
        font-size: 0.85rem;
        color: var(--muted);
        display: none;
    }

    .btn-clear-slot {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.25);
        color: #ef4444;
        border-radius: var(--radius-pill);
        width: 36px;
        height: 36px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        transition: all 0.15s ease;
        flex-shrink: 0;
    }

    .btn-clear-slot:hover {
        background: #ef4444;
        color: #ffffff;
    }

    .jadwal-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 1px solid var(--border) !important;
    }

    .btn-batal {
        padding: 12px 28px;
        border-radius: var(--radius-pill) !important;
        border: 1px solid var(--border) !important;
        background-color: var(--track) !important;
        color: var(--ink) !important;
        font-weight: 700;
        font-size: 13px;
        text-transform: uppercase;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-batal:hover {
        background-color: var(--border) !important;
        transform: translateY(-2px);
        color: var(--ink) !important;
    }

    .btn-simpan {
        padding: 12px 28px;
        border-radius: var(--radius-pill) !important;
        border: none;
        background: var(--accent) !important;
        color: #ffffff !important;
        font-weight: 700;
        font-size: 13px;
        text-transform: uppercase;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(160, 104, 65, 0.3) !important;
        transition: all 0.2s ease;
    }

    .btn-simpan:hover {
        filter: brightness(1.05);
        transform: translateY(-2px);
        color: #ffffff !important;
    }
</style>

<div class="jadwal-breadcrumb" style="display: none;">
    Iklan &gt; Jadwal Tayang &gt; Edit
</div>

@if ($errors->any())
    <div style="background: rgba(239, 68, 68, 0.12); border: 1px solid #ef4444; color: #ef4444; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 0.9rem;">
        <ul style="margin: 0; padding-left: 18px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="jadwal-card">
    <div class="jadwal-header">
        <div class="jadwal-icon-box">
            <i class="fas fa-edit"></i>
        </div>
        <h2 class="jadwal-header-title">Edit Jadwal Tayang</h2>
    </div>

    <form action="{{ route('jadwal.update', $jadwalItem->id) }}" method="POST" onsubmit="return prepareVideosBeforeSubmit()">
        @csrf
        @method('PUT')

        <div class="jadwal-form-group">
            <label class="jadwal-label">Nama Jadwal Tayang <span style="color: #ef4444;">*</span></label>
            <input type="text" name="nama_jadwal" class="jadwal-input"
                   value="{{ old('nama_jadwal', $jadwalItem->nama_jadwal ?? '') }}" required>
        </div>

        <div class="jadwal-form-group">
            <label class="jadwal-label">Keterangan</label>
            <textarea name="keterangan" rows="3" class="jadwal-textarea">{{ old('keterangan', $jadwalItem->keterangan ?? '') }}</textarea>
        </div>

        <div class="video-section-top">
            <div>
                <div class="video-section-title">Urutan Video Iklan</div>
                <div class="video-section-desc">Ketik untuk mencari video iklan. Slot kosong akan diabaikan secara otomatis saat disimpan.</div>
            </div>
            <button type="button" class="btn-add-slot" onclick="addNewVideoSlot()">
                <i class="fas fa-plus"></i>
                <span>TAMBAH SLOT VIDEO</span>
            </button>
        </div>

        <div id="videoSlotsContainer">
            @for ($i = 0; $i < $initialSlotCount; $i++)
                @php
                    $valVideo = $oldVideos[$i] ?? '';
                    $selectedLabel = isset($videoMap[$valVideo]) ? $videoMap[$valVideo]['full_label'] : '';
                @endphp
                <div class="video-row-item" id="row-video-{{ $i }}" data-slot-index="{{ $i }}">
                    <span class="video-row-number">#{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}:</span>

                    <div class="video-select-wrap" id="wrap-video-{{ $i }}" data-row="{{ $i }}">
                        <div class="video-search-box">
                            <i class="fas fa-search video-search-icon"></i>
                            <input type="text"
                                   id="video_search_{{ $i }}"
                                   class="video-search-input"
                                   value="{{ $selectedLabel }}"
                                   placeholder="-- Ketik atau klik untuk memilih video --"
                                   autocomplete="off"
                                   onfocus="openVideoDropdown({{ $i }})"
                                   oninput="filterVideoDropdown({{ $i }})"
                                   onkeydown="handleVideoKeydown(event, {{ $i }})">
                            <i class="fas fa-chevron-down video-search-chevron"></i>
                        </div>

                        <input type="hidden" name="file_iklan_ids[]" class="slot-hidden-val" id="hidden_video_{{ $i }}" value="{{ $valVideo }}">

                        <div class="video-dropdown-menu" id="video_menu_{{ $i }}">
                            <div class="video-dropdown-item" data-id="" data-label="" onmousedown="selectVideoOption({{ $i }}, '', '')">
                                <span style="color: #94a3b8; font-style: italic;">-- Kosong --</span>
                            </div>
                            @foreach ($videoMap as $v)
                                <div class="video-dropdown-item {{ (string)$valVideo === (string)$v['id'] ? 'selected-item' : '' }}"
                                     data-id="{{ $v['id'] }}"
                                     data-label="{{ $v['full_label'] }}"
                                     data-nama="{{ strtolower($v['nama']) }}"
                                     data-search="{{ $v['search'] }}"
                                     onmousedown="selectVideoOption({{ $i }}, '{{ $v['id'] }}', '{{ addslashes($v['full_label']) }}')">
                                    <span>{{ $v['nama'] }} <small style="opacity: 0.75;">-- {{ $v['pelanggan'] }}</small></span>
                                    <span class="video-type-badge">{{ $v['tipe'] }}</span>
                                </div>
                            @endforeach
                            <div class="video-dropdown-empty" id="video_empty_{{ $i }}">
                                Video iklan tidak ditemukan
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn-clear-slot" onclick="removeOrResetVideoRow({{ $i }})" title="Kosongkan / Hapus slot">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            @endfor
        </div>

        <div class="jadwal-actions">
            <a href="{{ route('jadwal.index') }}" class="btn-batal">Batal</a>
            <button type="submit" class="btn-simpan">
                <i class="fas fa-save"></i>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </form>
</div>

<script>
    const MASTER_VIDEOS = @json(array_values($videoMap));
    let nextSlotIndex = {{ $initialSlotCount }};

    function setRowHiddenValues(index, id) {
        const hidden = document.getElementById('hidden_video_' + index);
        if (hidden) {
            hidden.value = id;
            hidden.disabled = false;
        }
    }

    function autoMatchTypedVideo(index) {
        const input = document.getElementById('video_search_' + index);
        const hidden = document.getElementById('hidden_video_' + index);
        if (!input || !hidden) return;

        const text = input.value.trim().toLowerCase();
        if (text === '') {
            setRowHiddenValues(index, '');
            return;
        }

        if (hidden.value !== '') {
            const currentMatch = MASTER_VIDEOS.find((v) => String(v.id) === String(hidden.value));
            if (currentMatch && currentMatch.full_label.toLowerCase() === text) {
                setRowHiddenValues(index, currentMatch.id);
                return;
            }
        }

        let found = MASTER_VIDEOS.find(
            (v) => v.full_label.toLowerCase() === text || v.nama.toLowerCase() === text
        );

        if (!found) {
            const partialMatches = MASTER_VIDEOS.filter((v) => v.search.includes(text));
            if (partialMatches.length === 1) {
                found = partialMatches[0];
            }
        }

        if (found) {
            input.value = found.full_label;
            setRowHiddenValues(index, found.id);
        }
    }

    function prepareVideosBeforeSubmit() {
        document.querySelectorAll('.video-select-wrap').forEach((wrap) => {
            const idx = parseInt(wrap.getAttribute('data-row'), 10);
            autoMatchTypedVideo(idx);
        });

        document.querySelectorAll('.slot-hidden-val').forEach((inp) => {
            if (!inp.value || inp.value.trim() === '') {
                inp.disabled = true;
            } else {
                inp.disabled = false;
            }
        });

        return true;
    }

    function closeAllVideoDropdowns(exceptIndex = null) {
        document.querySelectorAll('.video-select-wrap').forEach((wrap) => {
            const idx = parseInt(wrap.getAttribute('data-row'), 10);
            if (idx !== exceptIndex) {
                wrap.classList.remove('open');
                autoMatchTypedVideo(idx);
                restoreValidVideoText(idx);
            }
        });
    }

    function openVideoDropdown(index) {
        closeAllVideoDropdowns(index);
        const wrap = document.getElementById('wrap-video-' + index);
        const input = document.getElementById('video_search_' + index);
        if (!wrap || !input) return;
        wrap.classList.add('open');
        input.select();
        filterVideoDropdown(index, true);
    }

    function filterVideoDropdown(index, showAll = false) {
        const wrap = document.getElementById('wrap-video-' + index);
        const input = document.getElementById('video_search_' + index);
        const menu = document.getElementById('video_menu_' + index);
        const emptyMsg = document.getElementById('video_empty_' + index);
        if (!wrap || !input || !menu) return;

        wrap.classList.add('open');
        const keyword = showAll ? '' : input.value.trim().toLowerCase();

        if (input.value.trim() === '') {
            setRowHiddenValues(index, '');
        } else {
            autoMatchTypedVideo(index);
        }

        let visibleCount = 0;
        menu.querySelectorAll('.video-dropdown-item').forEach((item) => {
            const id = item.getAttribute('data-id');
            if (id === '') {
                item.style.display = keyword === '' ? 'flex' : 'none';
                return;
            }
            const searchStr = item.getAttribute('data-search') || '';
            if (keyword === '' || searchStr.includes(keyword)) {
                item.style.display = 'flex';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        if (emptyMsg) emptyMsg.style.display = visibleCount === 0 ? 'block' : 'none';
    }

    function selectVideoOption(index, id, label) {
        const wrap = document.getElementById('wrap-video-' + index);
        const input = document.getElementById('video_search_' + index);
        const menu = document.getElementById('video_menu_' + index);

        if (input) input.value = label;
        setRowHiddenValues(index, id);

        if (menu) {
            menu.querySelectorAll('.video-dropdown-item').forEach((el) => {
                if (el.getAttribute('data-id') === String(id) && id !== '') {
                    el.classList.add('selected-item');
                } else {
                    el.classList.remove('selected-item');
                }
            });
        }
        if (wrap) wrap.classList.remove('open');
    }

    function restoreValidVideoText(index) {
        const input = document.getElementById('video_search_' + index);
        const hidden = document.getElementById('hidden_video_' + index);
        const menu = document.getElementById('video_menu_' + index);
        if (!input || !hidden || !menu) return;

        const currentId = hidden.value;
        if (!currentId) {
            input.value = '';
            return;
        }
        const matched = menu.querySelector('.video-dropdown-item[data-id="' + currentId + '"]');
        if (matched) input.value = matched.getAttribute('data-label') || '';
    }

    function handleVideoKeydown(e, index) {
        const wrap = document.getElementById('wrap-video-' + index);
        const menu = document.getElementById('video_menu_' + index);
        if (!wrap || !menu) return;

        if (e.key === 'Escape') {
            wrap.classList.remove('open');
            autoMatchTypedVideo(index);
            restoreValidVideoText(index);
            return;
        }
        if (e.key === 'Enter') {
            e.preventDefault();
            const firstVisible = Array.from(menu.querySelectorAll('.video-dropdown-item')).find(
                (el) => el.style.display !== 'none' && el.getAttribute('data-id') !== ''
            );
            if (firstVisible) {
                selectVideoOption(index, firstVisible.getAttribute('data-id'), firstVisible.getAttribute('data-label'));
            }
        }
    }

    function removeOrResetVideoRow(index) {
        const allRows = document.querySelectorAll('#videoSlotsContainer .video-row-item');
        const row = document.getElementById('row-video-' + index);
        if (!row) return;

        const hidden = document.getElementById('hidden_video_' + index);
        if (hidden && hidden.value !== '') {
            selectVideoOption(index, '', '');
            return;
        }
        if (allRows.length > 1) {
            row.remove();
            renumberVideoRows();
        } else {
            selectVideoOption(index, '', '');
        }
    }

    function renumberVideoRows() {
        document.querySelectorAll('#videoSlotsContainer .video-row-item').forEach((row, idx) => {
            const numSpan = row.querySelector('.video-row-number');
            if (numSpan) numSpan.textContent = '#' + String(idx + 1).padStart(2, '0') + ':';
        });
    }

    function escapeHtml(str) {
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    function addNewVideoSlot() {
        const container = document.getElementById('videoSlotsContainer');
        const idx = nextSlotIndex++;
        const currentCount = container.querySelectorAll('.video-row-item').length + 1;

        let optionsHtml = `<div class="video-dropdown-item" data-id="" data-label="" onmousedown="selectVideoOption(${idx}, '', '')"><span style="color: #94a3b8; font-style: italic;">-- Kosong --</span></div>`;
        MASTER_VIDEOS.forEach((v) => {
            const safeLabelAttr = escapeHtml(v.full_label);
            const safeLabelJs = v.full_label.replace(/\\/g, '\\\\').replace(/'/g, "\\'");
            optionsHtml += `<div class="video-dropdown-item" data-id="${v.id}" data-label="${safeLabelAttr}" data-nama="${escapeHtml(v.nama.toLowerCase())}" data-search="${escapeHtml(v.search)}" onmousedown="selectVideoOption(${idx}, '${v.id}', '${safeLabelJs}')"><span>${escapeHtml(v.nama)} <small style="opacity: 0.75;">-- ${escapeHtml(v.pelanggan)}</small></span><span class="video-type-badge">${escapeHtml(v.tipe)}</span></div>`;
        });

        const rowDiv = document.createElement('div');
        rowDiv.className = 'video-row-item';
        rowDiv.id = 'row-video-' + idx;
        rowDiv.setAttribute('data-slot-index', idx);
        rowDiv.innerHTML = `
            <span class="video-row-number">#${String(currentCount).padStart(2, '0')}:</span>
            <div class="video-select-wrap" id="wrap-video-${idx}" data-row="${idx}">
                <div class="video-search-box">
                    <i class="fas fa-search video-search-icon"></i>
                    <input type="text" id="video_search_${idx}" class="video-search-input" value="" placeholder="-- Ketik atau klik untuk memilih video --" autocomplete="off" onfocus="openVideoDropdown(${idx})" oninput="filterVideoDropdown(${idx})" onkeydown="handleVideoKeydown(event, ${idx})">
                    <i class="fas fa-chevron-down video-search-chevron"></i>
                </div>
                <input type="hidden" name="file_iklan_ids[]" class="slot-hidden-val" id="hidden_video_${idx}" value="">
                <div class="video-dropdown-menu" id="video_menu_${idx}">
                    ${optionsHtml}
                    <div class="video-dropdown-empty" id="video_empty_${idx}">Video iklan tidak ditemukan</div>
                </div>
            </div>
            <button type="button" class="btn-clear-slot" onclick="removeOrResetVideoRow(${idx})" title="Kosongkan / Hapus slot">
                <i class="fas fa-trash"></i>
            </button>
        `;
        container.appendChild(rowDiv);
    }

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.video-select-wrap')) closeAllVideoDropdowns();
    });
</script>
@endsection