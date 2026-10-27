<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Seeder;

class LoanSeeder extends Seeder
{
    public function run(): void
    {
        $memberIds = Member::query()->pluck('id');
        $userIds = User::query()->pluck('id');
        $bookIds = Book::query()->pluck('id');

        for ($index = 0; $index < 10; $index++) {
            $borrowedAt = now()->subDays(fake()->numberBetween(1, 30));
            $dueAt = $borrowedAt->copy()->addDays(7);
            $status = fake()->randomElement(['dipinjam', 'dikembalikan', 'dikembalikan', 'terlambat']);

            $loan = Loan::create([
                'member_id' => $memberIds->random(),
                'user_id' => $userIds->random(),
                'tanggal_pinjam' => $borrowedAt->toDateString(),
                'tanggal_kembali' => $dueAt->toDateString(),
                'tanggal_dikembalikan' => $status === 'dikembalikan'
                    ? $dueAt->copy()->subDays(fake()->numberBetween(0, 3))->toDateString()
                    : null,
                'status' => $status,
            ]);

            $selectedBookIds = $bookIds->random(fake()->numberBetween(1, min(3, $bookIds->count())));

            foreach ($selectedBookIds as $bookId) {
                $loan->loanItems()->create(['book_id' => $bookId]);
            }
        }
    }
}
