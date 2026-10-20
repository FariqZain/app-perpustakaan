<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoanResource;
use App\Models\Loan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $loans = Loan::with(['member', 'user', 'loanItems.book'])->paginate(10);

        return LoanResource::collection($loans);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'member_id' => 'required|integer|exists:members,id',
            'user_id' => 'required|integer|exists:users,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            'book_ids' => 'required|array|min:1',
            'book_ids.*' => 'required|integer|distinct|exists:books,id',
        ]);

        $loan = DB::transaction(function () use ($validated): Loan {
            $loan = Loan::create([
                'member_id' => $validated['member_id'],
                'user_id' => $validated['user_id'],
                'tanggal_pinjam' => $validated['tanggal_pinjam'],
                'tanggal_kembali' => $validated['tanggal_kembali'],
            ]);

            foreach ($validated['book_ids'] as $bookId) {
                $loan->loanItems()->create(['book_id' => $bookId]);
            }

            return $loan->refresh()->load(['member', 'user', 'loanItems.book']);
        });

        return (new LoanResource($loan))->response()->setStatusCode(201);
    }

    public function kembalikan(string $id): LoanResource
    {
        $loan = Loan::findOrFail($id);
        $loan->update([
            'status' => 'dikembalikan',
            'tanggal_dikembalikan' => now()->toDateString(),
        ]);

        return new LoanResource($loan->load(['member', 'user', 'loanItems.book']));
    }
}
