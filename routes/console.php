<?php

use Illuminate\Support\Facades\Schedule;

// วันออกรางวัลปกติคือ 1 และ 16 แต่บางงวดเลื่อน (เช่น 2 พ.ค., 17 ม.ค., 30 ธ.ค.)
// จึงเฝ้าทุกวันที่อาจมีการออกรางวัล ช่วงถ่ายทอดสด 14:25–16:45 ทุก 1 นาที
Schedule::command('lotto:fetch-latest')
    ->everyMinute()
    ->between('14:25', '16:45')
    ->when(fn () => in_array(now()->day, [1, 2, 16, 17, 30, 31], true))
    ->withoutOverlapping();

// ตรวจซ้ำตอนเย็นทุกวัน กันกรณีพลาดช่วงถ่ายทอดสด
Schedule::command('lotto:fetch-latest')->dailyAt('18:00');
