<?php

namespace App\Services\Lottery;

/**
 * แยกเลขสลาก 6 หลักจากข้อความที่ผู้ใช้กรอก
 * รองรับเลขไทย, "730 640", "730-640" และหลายใบในบรรทัดเดียว
 * และคืนส่วนที่อ่านไม่ได้กลับไปให้ผู้ใช้เห็น แทนการตัดทิ้งเงียบ ๆ
 */
class TicketParser
{
    /** @return array{numbers: list<string>, invalid: list<string>} */
    public function parse(string $input): array
    {
        $input = strtr($input, ['๐' => '0', '๑' => '1', '๒' => '2', '๓' => '3', '๔' => '4', '๕' => '5', '๖' => '6', '๗' => '7', '๘' => '8', '๙' => '9']);

        $numbers = [];
        $invalid = [];

        foreach (preg_split('/[\r\n,，;、]+/u', $input) as $chunk) {
            $chunk = trim($chunk);
            if ($chunk === '') {
                continue;
            }

            $tokens = preg_split('/\s+/u', $chunk);
            $cleaned = array_map(fn ($t) => preg_replace('/\D/', '', $t), $tokens);

            if (count(array_filter($cleaned, fn ($t) => strlen($t) === 6)) === count($cleaned)) {
                array_push($numbers, ...$cleaned);

                continue;
            }

            $digits = implode('', $cleaned);
            if ($digits !== '' && strlen($digits) % 6 === 0) {
                array_push($numbers, ...str_split($digits, 6));

                continue;
            }

            $invalid[] = mb_substr($chunk, 0, 40);
        }

        return ['numbers' => array_values(array_unique($numbers)), 'invalid' => $invalid];
    }
}
