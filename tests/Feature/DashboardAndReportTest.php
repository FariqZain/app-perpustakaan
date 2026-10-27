<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DashboardAndReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_stats_from_the_internal_api(): void
    {
        $stats = [
            'total_buku' => 20,
            'total_anggota' => 15,
            'peminjaman_aktif' => 3,
        ];
        Http::fake(['*' => Http::response($stats)]);

        $response = $this->actingAs(User::factory()->create())->get(route('dashboard'));

        $response->assertOk()
            ->assertViewIs('dashboard')
            ->assertViewHas('stats', $stats)
            ->assertSee('Total Buku')
            ->assertSee('20');

        Http::assertSent(fn (ClientRequest $request): bool => str_ends_with($request->url(), '/api/stats'));
    }

    public function test_report_displays_api_data_and_forwards_the_page_query(): void
    {
        Http::fake(['*' => Http::response([
            'data' => [[
                'id' => 14,
                'member' => ['nama' => 'Ayu Lestari'],
                'petugas' => ['name' => 'Bambang Sutrisno'],
                'books' => [['judul' => 'Laskar Pelangi']],
                'tanggal_pinjam' => '2026-10-20',
                'tanggal_kembali' => '2026-10-27',
                'status' => 'dipinjam',
            ]],
            'meta' => ['current_page' => 2, 'last_page' => 3],
        ])]);

        $response = $this->actingAs(User::factory()->create())
            ->get(route('loans.report', ['page' => 2]));

        $response->assertOk()
            ->assertViewIs('loans.report')
            ->assertSee('Ayu Lestari')
            ->assertSee('Bambang Sutrisno')
            ->assertSee('Laskar Pelangi')
            ->assertSee('Halaman 2 dari 3');

        Http::assertSent(fn (ClientRequest $request): bool => str_contains($request->url(), '/api/loans?page=2'));
    }

    public function test_dashboard_and_report_use_fallbacks_when_the_internal_api_is_unavailable(): void
    {
        Http::fake(fn () => throw new ConnectionException('Internal API offline.'));
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Total Buku')
            ->assertSee('0');

        $this->get(route('loans.report'))
            ->assertOk()
            ->assertSee('Belum ada data peminjaman.');
    }

    public function test_dashboard_and_report_use_fallbacks_for_unsuccessful_api_responses(): void
    {
        Http::fake(['*' => Http::response([], 503)]);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Total Buku')
            ->assertSee('0');

        $this->get(route('loans.report'))
            ->assertOk()
            ->assertSee('Belum ada data peminjaman.');
    }

    public function test_book_pagination_uses_the_custom_text_view(): void
    {
        Category::create(['nama_kategori' => 'Novel']);
        Book::factory()->count(11)->create();

        $response = $this->actingAs(User::factory()->create())->get(route('books.index'));

        $response->assertOk()
            ->assertSee('Sebelumnya')
            ->assertSee('Berikutnya')
            ->assertDontSee('<svg', false);
    }

    public function test_database_seeder_creates_demo_data_in_dependency_order(): void
    {
        $this->seed();

        $this->assertDatabaseCount('users', 3);
        $this->assertDatabaseCount('categories', 5);
        $this->assertDatabaseCount('books', 20);
        $this->assertDatabaseCount('members', 15);
        $this->assertDatabaseCount('loans', 10);
    }
}
