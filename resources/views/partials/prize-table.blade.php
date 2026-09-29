{{-- ตารางผลรางวัลครบทุกรางวัลของงวด: ต้องส่ง $draw (โหลด prizes แล้ว) --}}
@php use App\Enums\PrizeTier; @endphp
<div class="prize-board">
  <div class="prize-board-top">
    <div class="prize-card main-prize">
      <span>{{ PrizeTier::First->label() }}</span>
      <strong>{{ $draw->numbersFor(PrizeTier::First)[0] ?? 'รอผล' }}</strong>
      <small>รางวัลละ {{ number_format($draw->amountFor(PrizeTier::First) ?? 6000000) }} บาท</small>
    </div>
    <div class="mini-grid">
      @foreach ([PrizeTier::Front3, PrizeTier::Back3, PrizeTier::Back2] as $tier)
        <div>
          <span>{{ $tier->label() }}</span>
          <strong>{{ implode('  ', $draw->numbersFor($tier)) ?: str_repeat('-', $tier->digits()) }}</strong>
        </div>
      @endforeach
    </div>
  </div>

  @foreach ([PrizeTier::Near1, PrizeTier::Second, PrizeTier::Third, PrizeTier::Fourth, PrizeTier::Fifth] as $tier)
    @php $numbers = $draw->numbersFor($tier); @endphp
    <section class="prize-tier">
      <h3>
        {{ $tier->label() }}
        <small>{{ $tier->expectedCount() }} รางวัล · รางวัลละ {{ number_format($draw->amountFor($tier) ?? 0) }} บาท</small>
      </h3>
      <ul class="number-grid">
        @forelse ($numbers as $number)
          <li>{{ $number }}</li>
        @empty
          <li class="pending">รอผล</li>
        @endforelse
      </ul>
    </section>
  @endforeach
</div>
