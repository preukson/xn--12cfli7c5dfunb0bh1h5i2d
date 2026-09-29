<?php

namespace Tests\Feature;

use App\Models\Draw;
use App\Services\Lottery\DrawImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LotteryTest extends TestCase
{
    use RefreshDatabase;

    private function payload(): array
    {
        return json_decode(file_get_contents(base_path('tests/Fixtures/glo-latest-2026-09-16.json')), true)['response'];
    }

    private function importDraw(?array $payload = null): Draw
    {
        return app(DrawImporter::class)->import($payload ?? $this->payload());
    }

    public function test_imports_all_173_prizes_as_official(): void
    {
        $draw = $this->importDraw();

        $this->assertSame('2026-09-16', $draw->draw_date->toDateString());
        $this->assertSame(Draw::STATUS_OFFICIAL, $draw->status);
        $this->assertCount(173, $draw->prizes);
    }

    public function test_incomplete_payload_is_partial(): void
    {
        $payload = $this->payload();
        $payload['data']['fifth']['number'] = array_slice($payload['data']['fifth']['number'], 0, 10);

        $this->assertSame(Draw::STATUS_PARTIAL, $this->importDraw($payload)->status);
    }

    public function test_reimport_keeps_verified_status_when_numbers_unchanged(): void
    {
        $this->importDraw()->update(['status' => Draw::STATUS_VERIFIED, 'verified_at' => now()]);

        $this->assertSame(Draw::STATUS_VERIFIED, $this->importDraw()->status);
    }

    public function test_check_api_finds_every_prize_tier(): void
    {
        $this->importDraw();

        $response = $this->postJson('/api/check', [
            'numbers' => "730640\n730641\n047801\n060999\n999266\n111164\n123457\nabc",
            'draw_date' => '2026-09-16',
        ]);

        $response->assertOk()
            ->assertJsonPath('draw.complete', true)
            ->assertJsonPath('invalid', ['abc'])
            ->assertJsonPath('results.0.prizes.0.tier', 'first')
            ->assertJsonPath('results.1.prizes.0.tier', 'near1')
            ->assertJsonPath('results.2.prizes.0.tier', 'second')
            ->assertJsonPath('results.3.prizes.0.tier', 'front3')
            ->assertJsonPath('results.4.prizes.0.tier', 'back3')
            ->assertJsonPath('results.5.prizes.0.tier', 'back2')
            ->assertJsonPath('results.6.won', false);

        // 730640 ลงท้ายด้วย 40 ไม่ใช่ 64 จึงได้รางวัลที่ 1 อย่างเดียว
        $this->assertSame(6000000, $response->json('results.0.total'));
        $this->assertSame(6000000 + 100000 + 200000 + 4000 + 4000 + 2000, $response->json('total'));
    }

    public function test_check_api_rejects_input_without_valid_numbers(): void
    {
        $this->importDraw();

        $this->postJson('/api/check', ['numbers' => '12345'])
            ->assertStatus(422)
            ->assertJsonPath('invalid', ['12345']);
    }

    public function test_pages_render_with_thai_urls(): void
    {
        $this->importDraw();

        $this->get('/')->assertOk()->assertSee('730640');
        $this->get('/ตรวจหวย/16-กันยายน-2569')->assertOk()->assertSee('รางวัลที่ 5')->assertSee('730639');
        $this->get('/ตรวจหวย/'.rawurlencode('16-กันยายน-2569'))->assertOk();
        $this->get('/ตรวจหวย/16-ตุลาคม-2569')->assertNotFound();
        $this->get('/ตรวจหวยย้อนหลัง')->assertOk()->assertSee('730640');
        $this->get('/sitemap.xml')->assertOk()->assertSee('16-กันยายน-2569', false);
    }
}
