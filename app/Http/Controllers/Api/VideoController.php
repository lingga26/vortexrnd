<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FileIklan;
use Illuminate\Http\Request;
use App\Support\MediaStorage;

class VideoController extends Controller
{
    public function info(Request $request, int $fileIklanId)
    {
        $device = $request->input('_device');
        if (!$device || !$device->client_id) {
            return response()->json(['success' => false, 'message' => 'Perangkat belum terhubung ke client.'], 403);
        }
        // Tanpa login web => scope tidak aktif; batasi ke client milik perangkat
        $file = FileIklan::where('client_id', $device->client_id)->findOrFail($fileIklanId);

        if (!$file->has_video || !$file->file_path) {
            return response()->json(['success' => false, 'message' => 'File video tidak tersedia.'], 404);
        }

        $videoUrl = MediaStorage::url($file->file_path);
        $exists   = $videoUrl !== null;

        return response()->json([
            'success'      => true,
            'id'           => $file->id,
            'nama_iklan'   => $file->nama_iklan,
            'ukuran_bytes' => $file->ukuran_bytes,
            'durasi_detik' => $file->durasi_detik,
            'video_url'    => $videoUrl,
            'tersedia'     => $exists,
        ]);
    }

    public function download(Request $request, int $fileIklanId)
    {
        $device = $request->input('_device');
        if (!$device || !$device->client_id) {
            return response()->json(['success' => false, 'message' => 'Perangkat belum terhubung ke client.'], 403);
        }
        // Tanpa login web => scope tidak aktif; batasi ke client milik perangkat
        $file = FileIklan::where('client_id', $device->client_id)->findOrFail($fileIklanId);

        if (!$file->has_video || !$file->file_path) {
            return response()->json(['success' => false, 'message' => 'File video tidak tersedia.'], 404);
        }

        $path = MediaStorage::resolve($file->file_path);

        if ($path === null) {
            return response()->json(['success' => false, 'message' => 'File tidak ditemukan di server.'], 404);
        }

        $fileName = $file->file_name ?? basename($file->file_path);

        return response()->download($path, $fileName, ['Content-Type' => 'video/mp4']);
    }
}