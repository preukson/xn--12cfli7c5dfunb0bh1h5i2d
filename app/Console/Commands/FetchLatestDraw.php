<?php

namespace App\Console\Commands;

use App\Models\Draw;
use App\Services\Lottery\DrawImporter;
use App\Services\Lottery\GloClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class FetchLatestDraw extends Command
{
    protected $signature = 'lotto:fetch-latest';

    protected $description = 'ดึงผลสลากงวดล่าสุดจาก GLO แล้วบันทึกลงฐานข้อมูล';

    public function handle(GloClient $glo, DrawImporter $importer): int
    {
        $payload = $glo->latest();

        if (! $payload || empty($payload['date'])) {
            $this->warn('GLO ไม่ส่งข้อมูลงวดล่าสุด');

            return self::FAILURE;
        }

        $existing = Draw::query()->whereDate('draw_date', $payload['date'])->first();
        // งวดที่ผลครบแล้วไม่ต้องเขียนทับทุกนาที ถ้า GLO แก้ผลภายหลังให้ใช้ lotto:backfill --force
        if ($existing?->isComplete()) {
            $this->info("งวด {$existing->thaiDate()} มีผลครบแล้ว ข้าม");

            return self::SUCCESS;
        }

        $draw = $importer->import($payload);
        Cache::forget('draws.latest');

        $this->info("งวด {$draw->thaiDate()}: {$draw->status} ({$draw->prizes->count()} เลข)");

        return self::SUCCESS;
    }
}
