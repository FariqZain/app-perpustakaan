<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_loan_create_page_lists_members_users_and_books(): void
    {
        $member = $this->createMember();
        $user = User::factory()->create();
        $book = $this->createBook();

        $response = $this->get(route('loans.create'));

        $response->assertViewIs('loans.create')
            ->assertSee($member->nama)
            ->assertSee($member->nim)
            ->assertSee($user->name)
            ->assertSee($book->judul);
    }

    public function test_a_loan_can_be_created_with_multiple_books(): void
    {
        $member = $this->createMember();
        $user = User::factory()->create();
        $firstBook = $this->createBook();
        $secondBook = $this->createBook();

        $response = $this->post(route('loans.store'), [
            'member_id' => $member->id,
            'user_id' => $user->id,
            'tanggal_pinjam' => '2026-09-29',
            'tanggal_kembali' => '2026-10-06',
            'book_ids' => [$firstBook->id, $secondBook->id],
        ]);

        $response->assertRedirectToRoute('loans.index')
            ->assertSessionHas('success', 'Transaksi peminjaman berhasil dibuat.');

        $loan = Loan::query()->sole();

        $this->assertDatabaseHas('loans', [
            'id' => $loan->id,
            'member_id' => $member->id,
            'user_id' => $user->id,
            'status' => 'dipinjam',
        ]);
        $this->assertDatabaseHas('loan_items', [
            'loan_id' => $loan->id,
            'book_id' => $firstBook->id,
        ]);
        $this->assertDatabaseHas('loan_items', [
            'loan_id' => $loan->id,
            'book_id' => $secondBook->id,
        ]);
    }

    public function test_returning_a_loan_sets_status_and_return_date(): void
    {
        $member = $this->createMember();
        $user = User::factory()->create();
        $loan = $this->createLoan($member, $user, 'dipinjam');
        $returnDate = now()->toDateString();

        $response = $this->patch(route('loans.kembalikan', $loan));

        $response->assertRedirectToRoute('loans.index')
            ->assertSessionHas('success', 'Buku berhasil dikembalikan.');
        $this->assertDatabaseHas('loans', [
            'id' => $loan->id,
            'status' => 'dikembalikan',
            'tanggal_dikembalikan' => $returnDate,
        ]);
    }

    public function test_loan_index_shows_status_badges_and_only_offers_return_for_borrowed_loans(): void
    {
        $member = $this->createMember();
        $user = User::factory()->create();
        $borrowedLoan = $this->createLoan($member, $user, 'dipinjam');
        $returnedLoan = $this->createLoan($member, $user, 'dikembalikan');

        $response = $this->get(route('loans.index'));

        $response->assertSee('<span class="badge badge-warning">Dipinjam</span>', false)
            ->assertSee('<span class="badge badge-success">Dikembalikan</span>', false)
            ->assertSee('action="'.route('loans.kembalikan', $borrowedLoan).'"', false)
            ->assertDontSee('action="'.route('loans.kembalikan', $returnedLoan).'"', false);
    }

    public function test_loan_detail_shows_the_red_late_status_badge(): void
    {
        $member = $this->createMember();
        $user = User::factory()->create();
        $loan = $this->createLoan($member, $user, 'terlambat');

        $response = $this->get(route('loans.show', $loan));

        $response->assertViewIs('loans.show')
            ->assertSee('<span class="badge badge-danger">Terlambat</span>', false);
    }

    private function createMember(): Member
    {
        return Member::create([
            'nama' => 'Anggota Uji',
            'nim' => 'NIM-001',
            'email' => 'anggota@example.test',
            'nomor_telepon' => '081234567890',
            'alamat' => 'Alamat Uji',
            'status' => 'aktif',
        ]);
    }

    private function createBook(): Book
    {
        $category = Category::create(['nama_kategori' => 'Kategori Uji']);

        return Book::create([
            'category_id' => $category->id,
            'judul' => 'Buku Uji '.$category->id,
            'penulis' => 'Penulis Uji',
            'penerbit' => 'Penerbit Uji',
            'tahun_terbit' => 2024,
            'stok' => 3,
        ]);
    }

    private function createLoan(Member $member, User $user, string $status): Loan
    {
        return Loan::create([
            'member_id' => $member->id,
            'user_id' => $user->id,
            'tanggal_pinjam' => '2026-09-22',
            'tanggal_kembali' => '2026-09-29',
            'status' => $status,
        ]);
    }
}
