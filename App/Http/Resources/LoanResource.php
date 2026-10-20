<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'member_id' => $this->member_id,
            'user_id' => $this->user_id,
            'tanggal_pinjam' => $this->tanggal_pinjam,
            'tanggal_kembali' => $this->tanggal_kembali,
            'tanggal_dikembalikan' => $this->tanggal_dikembalikan,
            'status' => $this->status,
            'member' => $this->whenLoaded('member', fn (): array => (new MemberResource($this->member))->resolve($request)),
            'petugas' => $this->whenLoaded('user', fn (): array => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ]),
            'books' => $this->whenLoaded('loanItems', fn (): array => $this->loanItems
                ->map(fn ($loanItem): array => (new BookResource($loanItem->book))->resolve($request))
                ->all()),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
