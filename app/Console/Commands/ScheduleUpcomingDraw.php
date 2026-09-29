<?php

namespace App\Console\Commands;

use App\Models\Draw;
use App\Services\Lottery\DrawCalendar;
use App\Support\ThaiDate;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ScheduleUpcomingDraw extends Command
{
    protected $signature = 'lotto:upcoming {date? : วันออกรางวัลจริง (Y-m-d) กรณี GLO เลื่อนงวด}';

    protected $description = 'ดูงวดถัดไป หรือกำหนดวันออกรางวัลงวดถัดไปเองเมื่อ GLO ประกาศเลื่อนงวด';

    public function handle(DrawCalendar $calendar): int
    {
        if ($date = $this->argument('date')) {
            $date = Carbon::createFromFormat('Y-m-d', $date)->startOfDay();
            Draw::query()->whereDate('draw_date', $date)->firstOr(fn () => Draw::create([
                'draw_date' => $date->toDateString(),
                'status' => Draw::STATUS_PENDING,
            ]));
        }

        $upcoming = $calendar->upcomingDate($calendar->latestAnnounced());
        $this->info('งวดถัดไป: '.ThaiDate::long($upcoming).' (หน้าแรกเปลี่ยนเป็นหน้ารอผลวันที่ '.ThaiDate::long($calendar->previewStartsAt($upcoming)).')');

        return self::SUCCESS;
    }
}
