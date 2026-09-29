<?php

namespace App\Services\Lottery;

use App\Enums\PrizeTier;
use App\Models\Draw;

class LotteryChecker
{
    /**
     * ตรวจเลขสลาก 6 หลักกับผลของงวด ใบเดียวถูกได้หลายรางวัล (เช่น รางวัลที่ 2 และเลขท้าย 2 ตัว)
     *
     * @param  list<string>  $numbers
     * @return list<array{number: string, won: bool, prizes: list<array{tier: string, label: string, amount: int}>, total: int}>
     */
    public function check(Draw $draw, array $numbers): array
    {
        $lookup = [];
        foreach ($draw->prizes as $prize) {
            $lookup[$prize->tier->value][$prize->number] = $prize->amount;
        }

        return array_map(function (string $number) use ($lookup) {
            $prizes = [];

            foreach (PrizeTier::cases() as $tier) {
                $amount = $lookup[$tier->value][$tier->segment($number)] ?? null;
                if ($amount !== null) {
                    $prizes[] = ['tier' => $tier->value, 'label' => $tier->label(), 'amount' => $amount];
                }
            }

            return [
                'number' => $number,
                'won' => $prizes !== [],
                'prizes' => $prizes,
                'total' => array_sum(array_column($prizes, 'amount')),
            ];
        }, $numbers);
    }
}
