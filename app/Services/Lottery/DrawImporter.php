<?php

namespace App\Services\Lottery;

use App\Enums\PrizeTier;
use App\Models\Draw;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/** บันทึกผลรางวัลจาก payload ของ GLO ลงตาราง draws / draw_prizes */
class DrawImporter
{
    public function import(array $payload, string $source = 'glo'): Draw
    {
        if (empty($payload['date']) || ! isset($payload['data']) || ! is_array($payload['data'])) {
            throw new InvalidArgumentException('GLO payload has no date or prize data.');
        }

        [$rows, $counts] = $this->prizeRows($payload['data']);
        $status = $this->statusFor($counts);

        if (count($rows) < array_sum($counts)) {
            Log::warning('GLO payload has duplicate prize numbers.', ['draw_date' => $payload['date']]);
        }

        return DB::transaction(function () use ($payload, $source, $rows, $status) {
            $date = Carbon::parse($payload['date'])->toDateString();
            $draw = Draw::query()->whereDate('draw_date', $date)->lockForUpdate()->first()
                ?? new Draw(['draw_date' => $date]);

            // ผลที่แอดมินยืนยันแล้วคงสถานะไว้ ถ้าเลขไม่เปลี่ยน ถ้าเปลี่ยนต้องให้คนตรวจใหม่
            if ($draw->exists && $draw->status === Draw::STATUS_VERIFIED) {
                if ($this->fingerprint($rows) === $this->fingerprint($draw->prizes()->get(['tier', 'number', 'amount'])->toArray())) {
                    $draw->update(['fetched_at' => now()]);

                    return $draw;
                }

                Log::warning('GLO result changed for a verified draw; verification reset.', ['draw_date' => $draw->draw_date->toDateString()]);
                $draw->verified_at = null;
                $draw->verified_by = null;
            }

            $draw->fill([
                'status' => $status,
                'source' => $source,
                'pdf_url' => $payload['pdf_url'] ?? null,
                'youtube_url' => $payload['youtube_url'] ?? null,
                'raw' => $payload,
                'fetched_at' => now(),
            ])->save();

            $draw->prizes()->delete();
            $draw->prizes()->createMany($rows);

            return $draw->load('prizes');
        });
    }

    /**
     * @return array{0: list<array{tier: string, number: string, amount: int}>, 1: array<string, int>}
     *         แถวที่ไม่ซ้ำสำหรับบันทึก และจำนวนเลขที่ GLO ส่งมาต่อ tier (นับซ้ำด้วย
     *         เพราะข้อมูลเก่าบางงวดของ GLO มีเลขซ้ำ เช่น รางวัลที่ 5 งวด 1 ส.ค. 2562)
     */
    private function prizeRows(array $data): array
    {
        $rows = [];
        $counts = [];

        foreach (PrizeTier::cases() as $tier) {
            $group = $data[$tier->gloKey()] ?? null;
            if (! $group) {
                continue;
            }

            $amount = (int) round((float) ($group['price'] ?? 0));

            foreach ($group['number'] ?? [] as $entry) {
                $number = preg_replace('/\D/', '', (string) ($entry['value'] ?? ''));
                if (strlen($number) !== $tier->digits()) {
                    continue; // ระหว่างถ่ายทอดสด GLO อาจส่งช่องว่างหรือ "xxxxxx"
                }

                $rows[$tier->value.$number] = ['tier' => $tier->value, 'number' => $number, 'amount' => $amount];
                $counts[$tier->value] = ($counts[$tier->value] ?? 0) + 1;
            }
        }

        return [array_values($rows), $counts];
    }

    private function statusFor(array $counts): string
    {
        if ($counts === []) {
            return Draw::STATUS_PENDING;
        }

        foreach (PrizeTier::cases() as $tier) {
            if (($counts[$tier->value] ?? 0) < $tier->expectedCount()) {
                return Draw::STATUS_PARTIAL;
            }
        }

        return Draw::STATUS_OFFICIAL;
    }

    private function fingerprint(array $rows): string
    {
        $keys = array_map(fn ($r) => ($r['tier'] instanceof PrizeTier ? $r['tier']->value : $r['tier']).':'.$r['number'].':'.$r['amount'], $rows);
        sort($keys);

        return md5(implode('|', $keys));
    }
}
