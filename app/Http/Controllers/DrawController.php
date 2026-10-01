<?php

namespace App\Http\Controllers;

use App\Models\Draw;
use App\Services\Lottery\DrawCalendar;
use App\Support\ThaiDate;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class DrawController extends Controller
{
    public function __construct(private DrawCalendar $calendar) {}

    public function home(): View
    {
        $featured = $this->calendar->featured();
        $latest = $featured['latest'];

        return view('home', [
            'featured' => $featured,
            'latest' => $latest,
            'drawOptions' => $this->drawOptions(),
            'archive' => Draw::query()->announced()->with('prizes')->latest('draw_date')
                ->when($latest, fn ($q) => $q->where('id', '!=', $latest->id))
                ->take(6)->get(),
        ]);
    }

    public function show(string $slug): View
    {
        $date = ThaiDate::fromSlug($slug) ?? abort(404);
        $draw = Draw::query()->announced()->with('prizes')->whereDate('draw_date', $date)->first();

        if (! $draw) {
            return $this->upcoming($date);
        }

        return view('draws.show', [
            'draw' => $draw,
            'drawOptions' => $this->drawOptions(),
            'previous' => Draw::query()->announced()->where('draw_date', '<', $draw->draw_date)->latest('draw_date')->first(),
            'next' => Draw::query()->announced()->where('draw_date', '>', $draw->draw_date)->oldest('draw_date')->first(),
        ]);
    }

    /** หน้ารอผลงวดถัดไป แสดง XXXXXX จนกว่าผลจะเข้า มีเฉพาะงวดถัดไปเท่านั้น */
    private function upcoming($date): View
    {
        $latest = $this->calendar->latestAnnounced();
        abort_unless($date->isSameDay($this->calendar->upcomingDate($latest)), 404);

        return view('draws.upcoming', [
            'date' => $date,
            'latest' => $latest,
            'drawOptions' => $this->drawOptions(),
        ]);
    }

    public function next(): RedirectResponse
    {
        return redirect('/ตรวจหวย/'.ThaiDate::slug($this->calendar->upcomingDate($this->calendar->latestAnnounced())));
    }

    public function index(): View
    {
        $draws = Draw::query()->announced()->with('prizes')->latest('draw_date')->paginate(24);
        $featured = $this->calendar->featured();

        return view('draws.index', [
            'draws' => $draws,
            // ตั้งแต่วันที่ 15/30 จนผลออก แสดงงวดที่รอผลไว้บนสุดของหน้าแรกของรายการ
            'upcoming' => $draws->onFirstPage() && $featured['preview'] ? $featured['upcoming'] : null,
        ]);
    }

    public function sitemap()
    {
        $draws = Draw::query()->announced()->latest('draw_date')->get(['draw_date', 'updated_at']);
        $upcoming = $this->calendar->upcomingDate($draws->isEmpty() ? null : $draws->first());

        return response()->view('sitemap', ['draws' => $draws, 'upcoming' => $upcoming])->header('Content-Type', 'application/xml');
    }

    private function drawOptions()
    {
        return Draw::query()->announced()->latest('draw_date')->take(48)->get(['id', 'draw_date']);
    }
}
