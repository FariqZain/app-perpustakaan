<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAndReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_current_database_stats(): void
    {
        Category::create(['nama_kategori' => 'Novel']);
        Book::factory()->count(2)->create();
        $member = Member::factory()->create();
        $user = User::factory()->create();
        $this->createLoan($user, $member, 'dipinjam');
        $this->createLoan($user, $member, 'terlambat');
        $this->createLoan($user, $member, 'dikembalikan');

        $response = $this->actingAs(User::factory()->create())->get(route('dashboard'));

        $response->assertOk()
            ->assertViewIs('dashboard')
            ->assertViewHas('stats', [
                'total_buku' => 2,
                'total_anggota' => 1,
                'peminjaman_aktif' => 2,
            ])
            ->assertSee('Total Buku')
            ->assertSee('2');
    }

    public function test_report_displays_returned_loans_from_the_database(): void
    {
        Category::create(['nama_kategori' => 'Novel']);
        $book = Book::factory()->create(['judul' => 'Bumi Manusia']);
        $member = Member::factory()->create(['nama' => 'Ayu Lestari']);
        $user = User::factory()->create(['name' => 'Bambang Sutrisno']);
        $loan = $this->createLoan($user, $member, 'dikembalikan');
        $loan->loanItems()->create(['book_id' => $book->id]);

        $response = $this->actingAs(User::factory()->create())
            ->get(route('loans.report'));

        $response->assertOk()
            ->assertViewIs('loans.report')
            ->assertSee('Ayu Lestari')
            ->assertSee('Bambang Sutrisno')
            ->assertSee('Bumi Manusia')
            ->assertSee('Dikembalikan');
    }

    public function test_report_paginates_database_loans(): void
    {
        $user = User::factory()->create();
        $member = Member::factory()->create();

        for ($index = 0; $index < 11; $index++) {
            $this->createLoan($user, $member, 'dikembalikan');
        }

        $this->actingAs(User::factory()->create())
            ->get(route('loans.report', ['page' => 2]))
            ->assertOk()
            ->assertSee('Halaman 2 dari 2');
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

    private function createLoan(User $user, Member $member, string $status): Loan
    {
        return Loan::create([
            'member_id' => $member->id,
            'user_id' => $user->id,
            'tanggal_pinjam' => '2026-10-20',
            'tanggal_kembali' => '2026-10-27',
            'status' => $status,
        ]);
    }
}
