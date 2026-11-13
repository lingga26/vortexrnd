<?php

namespace App\Http\Controllers;

use App\Models\DigitalSignage;
use App\Models\Toko;
use App\Models\ModelSignage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DigitalSignageDeviceController extends Controller
{
    /**
     * 🟢 API UTAMA UNTUK TV (PENGAMBILAN PLAYLIST & HEARTBEAT ONLINE)
     * Diakses oleh TV via: https://vortexsign.web.id/api/device/playlist
     */
    public function getPlaylist(Request $request)
    {
        $deviceId = $request->input('device_id');
        $deviceToken = $request->input('device_token') ?? $request->header('X-Device-Token');

        if (!$deviceId) {
            return response()->json([
                'success' => false,
                'message' => 'Device ID is required'
            ], 400);
        }

        try {
            // Cari perangkat secara global tanpa terhalang tenant/subdomain
            $device = DigitalSignage::withoutGlobalScopes()
                ->where('device_id', $deviceId)
                ->first();

            if (!$device) {
                // Jika perangkat belum ada sama sekali di database, buat otomatis sebagai pending/nonaktif
                $device = DigitalSignage::withoutGlobalScopes()->create([
                    'device_id'      => $deviceId,
                    'nama_signage'   => 'Signage ' . substr($deviceId, 0, 8),
                    'device_token'   => $deviceToken ?: \Illuminate\Support\Str::random(64),
                    'status'         => 'Nonaktif',
                    'koneksi_status' => 'online',
                    'last_ping'      => now(),
                ]);
            } else {
                // 🟢 PERBARUI STATUS MENJADI ONLINE & CATAT WAKTU PING TERAKHIR
                $device->withoutGlobalScopes()->where('id', $device->id)->update([
                    'last_ping'      => now(),
                    'koneksi_status' => 'online',
                ]);
            }

            Log::info("Device heartbeat & playlist requested - Online: {$deviceId}");

            // Ambil data playlist / jadwal tayang yang terikat pada client_id device ini
            $playlistData = []; // Sesuaikan dengan logika pengambilan jadwal Anda

            return response()->json([
                'success' => true,
                'message' => 'Success',
                'device_id' => $device->device_id,
                'koneksi_status' => 'online',
                'playlist' => $playlistData,
            ], 200);

        } catch (\Exception $e) {
            Log::error('API getPlaylist error for device ' . $deviceId . ': ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Android device registrasi/heartbeat alternatif
     */
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'device_id'     => 'required|string|max:255',
                'nama_signage'  => 'nullable|string|max:255',
                'token'         => 'nullable|string|max:255',
            ]);

            $device = DigitalSignage::withoutGlobalScopes()->updateOrCreate(
                ['device_id' => $validated['device_id']],
                [
                    'nama_signage'   => $validated['nama_signage'] ?? 'Signage ' . substr($validated['device_id'], 0, 8),
                    'device_token'   => $validated['token'] ?? null,
                    'last_ping'      => now(),
                    'status'         => 'Aktif',
                    'koneksi_status' => 'online', 
                ]
            );

            Log::info("Device registered via API & marked Online: {$validated['device_id']}");

            return response()->json([
                'success' => true,
                'message' => 'Device registered and marked Online successfully',
                'data' => [
                    'device_id' => $device->device_id,
                    'nama_signage' => $device->nama_signage,
                    'koneksi_status' => $device->koneksi_status,
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Device registration error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}