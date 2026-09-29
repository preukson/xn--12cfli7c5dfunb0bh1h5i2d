<?php

namespace App\Services\Lottery;

use Carbon\CarbonInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/** ดึงผลสลากจาก API สาธารณะของสำนักงานสลากกินแบ่งรัฐบาล (glo.or.th) */
class GloClient
{
    public function __construct(private string $baseUrl = 'https://www.glo.or.th/api') {}

    /** ผลงวดล่าสุด (รวมระหว่างถ่ายทอดสด) หรือ null ถ้า API ไม่ตอบกลับข้อมูล */
    public function latest(): ?array
    {
        return $this->request()->post('lottery/getLatestLottery', (object) [])->throw()->json('response');
    }

    /** ผลของงวดที่ระบุ หรือ null ถ้างวดนั้นไม่มีผล */
    public function result(CarbonInterface $date): ?array
    {
        return $this->request()->post('checking/getLotteryResult', [
            'date' => $date->format('d'),
            'month' => $date->format('m'),
            'year' => $date->format('Y'),
        ])->throw()->json('response.result');
    }

    /** @return list<string> วันที่ออกรางวัล (Y-m-d) ของปีที่ระบุ ที่ประกาศผลแล้ว */
    public function drawDates(int $year): array
    {
        $periods = $this->request()->post('lottery/getPeriodsByYear', [
            'year' => (string) $year,
            'type' => 'CHECKED',
        ])->throw()->json('response.result') ?? [];

        $dates = array_values(array_unique(array_column($periods, 'date')));
        sort($dates);

        return $dates;
    }

    private function request(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->acceptJson()
            ->asJson()
            ->timeout(20)
            ->retry(2, 1000, throw: false);
    }
}
