<?php

namespace Tests\Feature;

use App\Models\Draw;
use App\Services\Lottery\DrawCalendar;
use App\Services\Lottery\DrawImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class UpcomingDrawTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $payload = json_decode(file_get_contents(base_path('tests/Fixtures/glo-latest-2026-09-16.json')), true)['response'];
        app(DrawImporter::class)->import($payload);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_regular_dates_follow_known_shifts(): void
    {
        $calendar = app(DrawCalendar::class);
        $next = fn (string $d) => $calendar->regularDateAfter(Carbon::parse($d))->toDateString();

        $this->assertSame('2026-10-01', $next('2026-09-16'));
        $this->assertSame('2026-10-16', $next('2026-10-01'));
        $this->assertSame('2027-01-02', $next('2026-12-16'));
        $this->assertSame('2027-01-17', $next('2027-01-02'));
        $this->assertSame('2027-05-02', $next('2027-04-16'));
        // งวดที่เลื่อนมาออกก่อน (31 ก.ค. 2566 แทน 1 ส.ค.)
        $this->assertSame('2023-08-16', $next('2023-07-31'));
    }

    public function test_preview_starts_on_15th_and_30th(): void
    {
        $calendar = app(DrawCalendar::class);
        $start = fn (string $d) => $calendar->previewStartsAt(Carbon::parse($d))->toDateString();

        $this->assertSame('2026-09-30', $start('2026-10-01'));
        $this->assertSame('2026-10-15', $start('2026-10-16'));
        $this->assertSame('2027-01-15', $start('2027-01-17'));
        $this->assertSame('2027-02-28', $start('2027-03-01'));
    }

    public function test_home_shows_latest_result_before_preview_day(): void
    {
        Carbon::setTestNow('2026-09-29 12:00');

        $this->get('/')->assertOk()
            ->assertSee('16 กันยายน 2569')
            ->assertSee('730640')
            ->assertDontSee('XXXXXX');
    }

    public function test_home_switches_to_next_draw_placeholders_on_the_30th(): void
    {
        Carbon::setTestNow('2026-09-30 00:05');

        $this->get('/')->assertOk()
            ->assertSee('1 ตุลาคม 2569')
            ->assertSee('XXXXXX')
            ->assertSee('ตรวจสลากกับงวด 16 กันยายน 2569');
    }

    public function test_upcoming_page_exists_only_for_next_draw(): void
    {
        $this->get('/ตรวจหวย/1-ตุลาคม-2569')->assertOk()->assertSee('XXXXXX')->assertSee('XXX');
        $this->get('/ตรวจหวย/16-ตุลาคม-2569')->assertNotFound();
        $this->get('/ตรวจหวย/งวดถัดไป')->assertRedirect(url('/ตรวจหวย/1-ตุลาคม-2569'));
    }

    public function test_admin_can_schedule_a_postponed_draw(): void
    {
        $this->artisan('lotto:upcoming', ['date' => '2026-10-02'])->assertSuccessful();

        $this->assertSame('2026-10-02', app(DrawCalendar::class)->upcomingDate(Draw::query()->announced()->latest('draw_date')->first())->toDateString());
        $this->get('/ตรวจหวย/2-ตุลาคม-2569')->assertOk()->assertSee('XXXXXX');
    }
}
