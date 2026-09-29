<?php

namespace App\Models;

use App\Enums\PrizeTier;
use App\Support\ThaiDate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Draw extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PARTIAL = 'partial';
    public const STATUS_OFFICIAL = 'official';
    public const STATUS_VERIFIED = 'verified';

    protected $fillable = [
        'draw_date', 'status', 'source', 'pdf_url', 'youtube_url', 'raw', 'fetched_at', 'verified_at', 'verified_by',
    ];

    protected function casts(): array
    {
        return [
            'draw_date' => 'date',
            'raw' => 'array',
            'fetched_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function prizes(): HasMany
    {
        return $this->hasMany(DrawPrize::class);
    }

    public function scopeAnnounced(Builder $query): void
    {
        $query->whereIn('status', [self::STATUS_PARTIAL, self::STATUS_OFFICIAL, self::STATUS_VERIFIED]);
    }

    public function isComplete(): bool
    {
        return in_array($this->status, [self::STATUS_OFFICIAL, self::STATUS_VERIFIED], true);
    }

    /** @return list<string> เลขรางวัลของ tier เรียงจากน้อยไปมาก */
    public function numbersFor(PrizeTier $tier): array
    {
        return $this->prizes
            ->filter(fn (DrawPrize $p) => $p->tier === $tier)
            ->pluck('number')
            ->sort()
            ->values()
            ->all();
    }

    public function amountFor(PrizeTier $tier): ?int
    {
        return $this->prizes->first(fn (DrawPrize $p) => $p->tier === $tier)?->amount;
    }

    public function slug(): string
    {
        return ThaiDate::slug($this->draw_date);
    }

    public function thaiDate(): string
    {
        return ThaiDate::long($this->draw_date);
    }

    public function url(): string
    {
        return url('/ตรวจหวย/'.$this->slug());
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PARTIAL => 'ผลสด กำลังทยอยประกาศ',
            self::STATUS_OFFICIAL => 'ผลจากสำนักงานสลากฯ',
            self::STATUS_VERIFIED => 'ตรวจยืนยันแล้ว',
            default => 'ยังไม่ออกรางวัล',
        };
    }
}
