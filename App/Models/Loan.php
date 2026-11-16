<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Loan extends Model
{
    use HasFactory;

    private const DENDA_PER_HARI = 2000;

    protected $fillable = [
        'member_id',
        'user_id',
        'tanggal_pinjam',
        'tanggal_kembali',
        'tanggal_dikembalikan',
        'status',
    ];

    protected function status(): Attribute
    {
        return Attribute::get(function (?string $value): ?string {
            if ($value === 'dipinjam' && $this->jumlahHariTerlambat() > 0) {
                return 'terlambat';
            }

            return $value;
        });
    }

    protected function denda(): Attribute
    {
        return Attribute::get(fn (): int => $this->jumlahHariTerlambat() * self::DENDA_PER_HARI);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function loanItems(): HasMany
    {
        return $this->hasMany(LoanItem::class);
    }

    private function jumlahHariTerlambat(): int
    {
        $tanggalJatuhTempo = Carbon::parse($this->tanggal_kembali)->startOfDay();
        $tanggalAkhir = $this->tanggal_dikembalikan
            ? Carbon::parse($this->tanggal_dikembalikan)->startOfDay()
            : Carbon::today();

        if (! $tanggalAkhir->isAfter($tanggalJatuhTempo)) {
            return 0;
        }

        return (int) $tanggalJatuhTempo->diffInDays($tanggalAkhir);
    }
}
