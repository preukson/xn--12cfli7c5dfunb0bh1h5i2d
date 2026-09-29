<?php

namespace App\Console\Commands;

use App\Models\Draw;
use App\Services\Lottery\DrawImporter;
use App\Services\Lottery\GloClient;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Throwable;

class BackfillDraws extends Command
{
    protected $signature = 'lotto:backfill {--from= : ปี ค.ศ. เริ่มต้น (ค่าเริ่มต้น 5 ปีย้อนหลัง)} {--to= : ปี ค.ศ. สิ้นสุด} {--force : ดึงซ้ำแม้มีผลครบแล้ว}';

    protected $description = 'ดึงผลสลากย้อนหลังจาก GLO เข้าฐานข้อมูล';

    public function handle(GloClient $glo, DrawImporter $importer): int
    {
        $to = (int) ($this->option('to') ?: now()->year);
        $from = (int) ($this->option('from') ?: $to - 5);

        $complete = Draw::query()
            ->whereIn('status', [Draw::STATUS_OFFICIAL, Draw::STATUS_VERIFIED])
            ->pluck('draw_date')
            ->map(fn ($d) => $d->toDateString())
            ->flip();

        for ($year = $from; $year <= $to; $year++) {
            $dates = $glo->drawDates($year);
            $this->line("ปี {$year}: พบ ".count($dates).' งวด');

            foreach ($dates as $date) {
                if (! $this->option('force') && $complete->has($date)) {
                    continue;
                }

                try {
                    $payload = $glo->result(Carbon::parse($date));
                    if (! $payload) {
                        $this->warn("  {$date}: ไม่มีข้อมูล");

                        continue;
                    }

                    $draw = $importer->import($payload);
                    $this->line("  {$date}: {$draw->status}");
                } catch (Throwable $e) {
                    $this->error("  {$date}: {$e->getMessage()}");
                }

                usleep(300_000); // เว้นจังหวะไม่ให้ยิง API ของ GLO ถี่เกินไป
            }
        }

        return self::SUCCESS;
    }
}
