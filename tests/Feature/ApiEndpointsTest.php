<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_endpoints_filter_paginate_create_update_and_delete(): void
    {
        $category = Category::create(['nama_kategori' => 'Novel']);
        $book = $this->createBook($category, 'Laskar Pelangi');

        $this->getJson('/api/books?judul=laskar&category_id='.$category->id)
            ->assertOk()
            ->assertJsonPath('data.0.id', $book->id)
            ->assertJsonPath('data.0.category.nama_kategori', 'Novel')
            ->assertJsonPath('meta.total', 1);

        $this->getJson('/api/books/'.$book->id)
            ->assertOk()
            ->assertJsonPath('data.id', $book->id);

        $payload = [
            'judul' => 'Bumi Manusia',
            'penulis' => 'Pramoedya Ananta Toer',
            'penerbit' => 'Lentera Dipantara',
            'tahun_terbit' => 1980,
            'isbn' => '9789799731234',
            'stok' => 4,
            'category_id' => $category->id,
        ];

        $this->postJson('/api/books', $payload)
            ->assertCreated()
            ->assertJsonPath('data.isbn', $payload['isbn']);

        $createdBook = Book::where('isbn', $payload['isbn'])->firstOrFail();
        $this->putJson('/api/books/'.$createdBook->id, [...$payload, 'judul' => 'Bumi Manusia Updated'])
            ->assertOk()
            ->assertJsonPath('data.judul', 'Bumi Manusia Updated');

        $this->deleteJson('/api/books/'.$createdBook->id)
            ->assertOk()
            ->assertJsonPath('message', 'Buku berhasil dihapus.');

        $this->postJson('/api/books', [])->assertUnprocessable();
        $this->getJson('/api/books/999999')->assertNotFound();
    }

    public function test_member_endpoints_are_read_only_and_paginated(): void
    {
        $member = Member::create([
            'nama' => 'Ayu Lestari',
            'nim' => '20260001',
            'email' => 'ayu@example.test',
            'nomor_telepon' => '081234567890',
            'alamat' => 'Surabaya',
            'status' => 'aktif',
        ]);

        $this->getJson('/api/members')
            ->assertOk()
            ->assertJsonPath('data.0.id', $member->id)
            ->assertJsonPath('meta.total', 1);

        $this->getJson('/api/members/'.$member->id)
            ->assertOk()
            ->assertJsonPath('data.email', 'ayu@example.test');
    }

    public function test_loan_endpoints_create_list_and_return_a_loan(): void
    {
        $category = Category::create(['nama_kategori' => 'Novel']);
        $book = $this->createBook($category, 'Laskar Pelangi');
        $member = Member::create([
            'nama' => 'Ayu Lestari',
            'nim' => '20260002',
            'email' => 'ayu2@example.test',
            'nomor_telepon' => '081234567890',
            'alamat' => 'Surabaya',
            'status' => 'aktif',
        ]);
        $user = User::factory()->create();

        $response = $this->postJson('/api/loans', [
            'member_id' => $member->id,
            'user_id' => $user->id,
            'tanggal_pinjam' => '2026-10-20',
            'tanggal_kembali' => '2026-10-27',
            'book_ids' => [$book->id],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.status', 'dipinjam')
            ->assertJsonPath('data.member.id', $member->id)
            ->assertJsonPath('data.petugas.name', $user->name)
            ->assertJsonPath('data.books.0.id', $book->id);

        $loanId = $response->json('data.id');

        $this->getJson('/api/loans')
            ->assertOk()
            ->assertJsonPath('data.0.id', $loanId);

        $this->putJson('/api/loans/'.$loanId.'/kembalikan')
            ->assertOk()
            ->assertJsonPath('data.status', 'dikembalikan')
            ->assertJsonPath('data.tanggal_dikembalikan', now()->toDateString());

        $this->assertDatabaseHas('loans', [
            'id' => $loanId,
            'status' => 'dikembalikan',
        ]);
    }

    public function test_stats_endpoint_returns_current_totals(): void
    {
        $category = Category::create(['nama_kategori' => 'Novel']);
        $this->createBook($category, 'Laskar Pelangi');

        Member::create([
            'nama' => 'Ayu Lestari',
            'nim' => '20260003',
            'email' => 'ayu3@example.test',
            'nomor_telepon' => '081234567890',
            'alamat' => 'Surabaya',
            'status' => 'aktif',
        ]);

        $this->getJson('/api/stats')
            ->assertOk()
            ->assertExactJson([
                'total_buku' => 1,
                'total_anggota' => 1,
                'peminjaman_aktif' => 0,
            ]);
    }

    private function createBook(Category $category, string $title): Book
    {
        return Book::create([
            'category_id' => $category->id,
            'judul' => $title,
            'penulis' => 'Andrea Hirata',
            'penerbit' => 'Bentang Pustaka',
            'tahun_terbit' => 2005,
            'stok' => 5,
        ]);
    }
}
