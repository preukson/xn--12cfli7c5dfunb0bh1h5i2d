<?php

namespace App\Http\Controllers;

use App\Models\Draw;
use App\Support\ThaiDate;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;

class DrawController extends Controller
{
    public function home(): View
    {
        $latest = Draw::query()->announced()->with('prizes')->latest('draw_date')->first();

        return view('home', [
            'latest' => $latest,
            'drawOptions' => $this->drawOptions(),
            'archive' => Draw::query()->announced()->with('prizes')->latest('draw_date')->skip(1)->take(6)->get(),
            'nextDrawDate' => $this->nextDrawDate(),
        ]);
    }

    public function show(string $slug): View
    {
        $date = ThaiDate::fromSlug($slug) ?? abort(404);
        $draw = Draw::query()->announced()->with('prizes')->whereDate('draw_date', $date)->firstOrFail();

        return view('draws.show', [
            'draw' => $draw,
            'drawOptions' => $this->drawOptions(),
            'previous' => Draw::query()->announced()->where('draw_date', '<', $draw->draw_date)->latest('draw_date')->first(),
            'next' => Draw::query()->announced()->where('draw_date', '>', $draw->draw_date)->oldest('draw_date')->first(),
        ]);
    }

    public function index(): View
    {
        $draws = Draw::query()->announced()->with('prizes')->latest('draw_date')->paginate(24);

        return view('draws.index', ['draws' => $draws]);
    }

    public function sitemap()
    {
        $draws = Draw::query()->announced()->latest('draw_date')->get(['draw_date', 'updated_at']);

        return response()->view('sitemap', ['draws' => $draws])->header('Content-Type', 'application/xml');
    }

    private function drawOptions()
    {
        return Draw::query()->announced()->latest('draw_date')->take(48)->get(['id', 'draw_date']);
    }

    /** งวดถัดไปตามรอบปกติ (1 และ 16) ใช้แสดงผลเท่านั้น วันจริงอาจเลื่อนตามประกาศ GLO */
    private function nextDrawDate(): Carbon
    {
        $today = now()->startOfDay();

        return match (true) {
            $today->day === 1 => $today,
            $today->day <= 16 => $today->copy()->day(16),
            default => $today->copy()->addMonthNoOverflow()->day(1),
        };
    }
}
