<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_buku' => 0,
            'total_anggota' => 0,
            'peminjaman_aktif' => 0,
        ];

        try {
            $baseUrl = rtrim((string) config('services.internal_api.base_url'), '/');
            $response = Http::timeout(5)->get($baseUrl.'/api/stats');

            if ($response->successful()) {
                $stats = $response->json();
            }
        } catch (ConnectionException) {
            // Keep the dashboard available when the internal API server is offline.
        }

        return view('dashboard', compact('stats'));
    }
}
