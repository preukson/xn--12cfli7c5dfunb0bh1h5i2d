<?php

namespace App\Services\Lottery;

use App\Models\Draw;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

/**
 * คำนวณงวดถัดไป และตัดสินว่าหน้าแรกควรโชว์ผลงวดล่าสุด หรือหน้ารอผลงวดถัดไป (XXXXXX)
 *
 * หน้ารอผลเริ่มแสดงวันที่ 15 (ก่อนงวด 16) และวันที่ 30 (ก่อนงวด 1; เดือนที่สั้นกว่าใช้วันสุดท้าย)
 * งวดที่เลื่อนผิดปกติให้แอดมินสร้างแถว draws สถานะ pending ไว้ล่วงหน้าด้วย lotto:upcoming
 */
class DrawCalendar
{
    public function latestAnnounced(): ?Draw
    {
        return Draw::query()->announced()->with('prizes')->latest('draw_date')->first();
    }

    /** วันออกรางวัลงวดถัดจากงวดล่าสุดที่มีผล */
    public function upcomingDate(?Draw $latest = null): Carbon
    {
        $after = $latest?->draw_date ?? now()->subDay()->startOfDay();

        $scheduled = Draw::query()
            ->where('status', Draw::STATUS_PENDING)
            ->whereDate('draw_date', '>', $after)
            ->oldest('draw_date')
            ->value('draw_date');

        return $scheduled ? Carbon::parse($scheduled)->startOfDay() : $this->regularDateAfter($after);
    }

    /** งวดปกติ 1 และ 16 พร้อมวันเลื่อนที่เกิดทุกปี (17 ม.ค., 2 พ.ค., 2 ม.ค.) */
    public function regularDateAfter(CarbonInterface $date): Carbon
    {
        $date = Carbon::instance($date)->startOfDay();
        $next = $date->day < 16 ? $date->copy()->day(16) : $date->copy()->addMonthNoOverflow()->day(1);

        $next = match (true) {
            $next->month === 1 && $next->day === 16 => $next->day(17),
            $next->month === 5 && $next->day === 1 => $next->day(2),
            $next->month === 1 && $next->day === 1 => $next->day(2),
            default => $next,
        };

        // งวดที่เลื่อนมาออกก่อน (เช่น 31 ก.ค. แทน 1 ส.ค.) ถือว่าเป็นงวดนั้นแล้ว ให้ข้ามไปงวดถัดไป
        return $date->diffInDays($next) < 7 ? $this->regularDateAfter($next) : $next;
    }

    /** วันที่เริ่มเปลี่ยนหน้าแรกเป็นหน้ารอผลของงวดที่ระบุ */
    public function previewStartsAt(CarbonInterface $drawDate): Carbon
    {
        $drawDate = Carbon::instance($drawDate)->startOfDay();

        if ($drawDate->day >= 15) {
            return $drawDate->copy()->day(15);
        }

        $previousMonth = $drawDate->copy()->subMonthNoOverflow();

        return $previousMonth->day(min(30, $previousMonth->daysInMonth));
    }

    /** ถึงเวลาออกรางวัลแล้ว (14:25 ของวันงวด) แต่ผลยังไม่ครบในฐานข้อมูล */
    public function awaitingResults(): bool
    {
        $latest = $this->latestAnnounced();

        if ($latest && ! $latest->isComplete()) {
            return true;
        }

        return now()->gte($this->upcomingDate($latest)->setTime(14, 25));
    }

    /**
     * ข้อมูลสำหรับบอร์ดผลรางวัลด้านบนหน้าแรก
     *
     * @return array{date: Carbon, draw: ?Draw, latest: ?Draw, upcoming: Carbon, preview: bool}
     */
    public function featured(): array
    {
        $latest = $this->latestAnnounced();
        $upcoming = $this->upcomingDate($latest);
        $preview = ! $latest || now()->startOfDay()->gte($this->previewStartsAt($upcoming));

        return [
            'date' => $preview ? $upcoming : $latest->draw_date,
            'draw' => $preview ? null : $latest,
            'latest' => $latest,
            'upcoming' => $upcoming,
            'preview' => $preview,
        ];
    }
}
