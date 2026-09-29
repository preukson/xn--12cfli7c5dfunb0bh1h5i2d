<?php

namespace App\Enums;

enum PrizeTier: string
{
    case First = 'first';
    case Near1 = 'near1';
    case Second = 'second';
    case Third = 'third';
    case Fourth = 'fourth';
    case Fifth = 'fifth';
    case Front3 = 'front3';
    case Back3 = 'back3';
    case Back2 = 'back2';

    public function label(): string
    {
        return match ($this) {
            self::First => 'รางวัลที่ 1',
            self::Near1 => 'รางวัลข้างเคียงรางวัลที่ 1',
            self::Second => 'รางวัลที่ 2',
            self::Third => 'รางวัลที่ 3',
            self::Fourth => 'รางวัลที่ 4',
            self::Fifth => 'รางวัลที่ 5',
            self::Front3 => 'เลขหน้า 3 ตัว',
            self::Back3 => 'เลขท้าย 3 ตัว',
            self::Back2 => 'เลขท้าย 2 ตัว',
        };
    }

    /** Key ที่ใช้ใน response ของ API สำนักงานสลากฯ */
    public function gloKey(): string
    {
        return match ($this) {
            self::Front3 => 'last3f',
            self::Back3 => 'last3b',
            self::Back2 => 'last2',
            default => $this->value,
        };
    }

    /** จำนวนเลขที่ออกต่องวด ใช้ตัดสินว่าผลครบแล้วหรือยัง */
    public function expectedCount(): int
    {
        return match ($this) {
            self::First, self::Back2 => 1,
            self::Near1, self::Front3, self::Back3 => 2,
            self::Second => 5,
            self::Third => 10,
            self::Fourth => 50,
            self::Fifth => 100,
        };
    }

    public function digits(): int
    {
        return match ($this) {
            self::Front3, self::Back3 => 3,
            self::Back2 => 2,
            default => 6,
        };
    }

    /** ส่วนของเลขสลาก 6 หลักที่ต้องนำไปเทียบกับเลขรางวัลของ tier นี้ */
    public function segment(string $ticket): string
    {
        return match ($this) {
            self::Front3 => substr($ticket, 0, 3),
            self::Back3 => substr($ticket, 3),
            self::Back2 => substr($ticket, 4),
            default => $ticket,
        };
    }
}
