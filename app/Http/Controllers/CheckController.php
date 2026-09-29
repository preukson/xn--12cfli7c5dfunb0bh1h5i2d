<?php

namespace App\Http\Controllers;

use App\Models\Draw;
use App\Services\Lottery\LotteryChecker;
use App\Services\Lottery\TicketParser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckController extends Controller
{
    public const MAX_TICKETS = 100;

    public function __invoke(Request $request, TicketParser $parser, LotteryChecker $checker): JsonResponse
    {
        $data = $request->validate([
            'numbers' => ['required', 'string', 'max:5000'],
            'draw_date' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $draw = Draw::query()->announced()->with('prizes')
            ->when($data['draw_date'] ?? null, fn ($q, $date) => $q->whereDate('draw_date', $date), fn ($q) => $q->latest('draw_date'))
            ->first();

        if (! $draw) {
            return response()->json(['message' => 'ยังไม่มีผลรางวัลของงวดนี้'], 404);
        }

        $parsed = $parser->parse($data['numbers']);

        if ($parsed['numbers'] === []) {
            return response()->json([
                'message' => 'กรุณากรอกเลขสลาก 6 หลักอย่างน้อย 1 ใบ',
                'invalid' => $parsed['invalid'],
            ], 422);
        }

        $numbers = array_slice($parsed['numbers'], 0, self::MAX_TICKETS);
        $results = $checker->check($draw, $numbers);

        return response()->json([
            'draw' => [
                'date' => $draw->draw_date->toDateString(),
                'label' => $draw->thaiDate(),
                'status' => $draw->status,
                'status_label' => $draw->statusLabel(),
                'complete' => $draw->isComplete(),
                'url' => $draw->url(),
            ],
            'results' => $results,
            'invalid' => $parsed['invalid'],
            'truncated' => count($parsed['numbers']) > self::MAX_TICKETS,
            'total' => array_sum(array_column($results, 'total')),
        ]);
    }
}
