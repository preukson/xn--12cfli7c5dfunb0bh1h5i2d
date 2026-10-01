{{--
  ตารางผลรางวัลครบทุกรางวัลของงวด
  $draw: ?Draw (โหลด prizes แล้ว) ถ้า null หรือผลยังไม่ครบ ช่องที่ยังไม่ออกแสดงเป็น XXXXXX
  $withTop: แสดงรางวัลที่ 1 / เลขหน้า / เลขท้ายด้วยหรือไม่ (หน้าแรกมีบอร์ดด้านบนแล้วจึงปิดได้)
--}}
@php
  use App\Enums\PrizeTier;

  $draw ??= null;
  $withTop ??= true;
  $defaultAmounts = [
      'first' => 6000000, 'near1' => 100000, 'second' => 200000, 'third' => 80000,
      'fourth' => 40000, 'fifth' => 20000, 'front3' => 4000, 'back3' => 4000, 'back2' => 2000,
  ];
  // เลขที่ออกแล้วตามด้วยช่อง XXX จนครบจำนวนรางวัลของ tier
  $slots = function (PrizeTier $tier) use ($draw): array {
      $numbers = $draw?->numbersFor($tier) ?? [];

      return array_pad($numbers, max($tier->expectedCount(), count($numbers)), null);
  };
  $amount = fn (PrizeTier $tier) => number_format($draw?->amountFor($tier) ?? $defaultAmounts[$tier->value]);
  $placeholder = fn (PrizeTier $tier) => str_repeat('X', $tier->digits());
@endphp
<div class="prize-board">
  @if ($withTop)
    <div class="prize-board-top">
      <div class="prize-card main-prize">
        <span>{{ PrizeTier::First->label() }}</span>
        <strong>{{ $slots(PrizeTier::First)[0] ?? 'XXXXXX' }}</strong>
        <small>รางวัลละ {{ $amount(PrizeTier::First) }} บาท</small>
      </div>
      <div class="mini-grid">
        @foreach ([PrizeTier::Front3, PrizeTier::Back3, PrizeTier::Back2] as $tier)
          <div>
            <span>{{ $tier->label() }}</span>
            <strong>{{ implode('  ', array_map(fn ($n) => $n ?? $placeholder($tier), $slots($tier))) }}</strong>
          </div>
        @endforeach
      </div>
    </div>
  @endif

  @foreach ([PrizeTier::Near1, PrizeTier::Second, PrizeTier::Third, PrizeTier::Fourth, PrizeTier::Fifth] as $tier)
    <section class="prize-tier">
      <h3>
        {{ $tier->label() }}
        <small>{{ $tier->expectedCount() }} รางวัล · รางวัลละ {{ $amount($tier) }} บาท</small>
      </h3>
      <ul class="number-grid">
        @foreach ($slots($tier) as $number)
          <li @class(['pending' => $number === null])>{{ $number ?? $placeholder($tier) }}</li>
        @endforeach
      </ul>
    </section>
  @endforeach
</div>
