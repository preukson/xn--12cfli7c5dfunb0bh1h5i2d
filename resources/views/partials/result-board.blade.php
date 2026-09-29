{{--
  บอร์ดผลรางวัลบนสุด: "ตรวจหวย" + 4 รางวัลหลัก + ปุ่มดูผลทั้งหมด + ช่องตรวจ 1 แถว
  ต้องส่ง $date (Carbon งวดที่แสดง), $draw (?Draw ผลของงวดนั้น อาจยังไม่ครบ), $latest (?Draw งวดล่าสุดที่มีผล ใช้ตรวจ)
  และ $heading (bool ใช้ h1 หรือไม่)
--}}
@php
  use App\Enums\PrizeTier;
  use App\Support\ThaiDate;

  $complete = $draw?->isComplete() ?? false;
  $isToday = $date->isToday();
  $cards = [
      [PrizeTier::First, 'is-red'],
      [PrizeTier::Back2, 'is-red'],
      [PrizeTier::Front3, 'is-blue'],
      [PrizeTier::Back3, 'is-blue'],
  ];
  $defaultAmounts = ['first' => 6000000, 'back2' => 2000, 'front3' => 4000, 'back3' => 4000];
@endphp

<section class="result-board" @if (! $complete && $isToday) data-live-until="{{ $date->copy()->setTime(17, 0)->timestamp }}" @endif>
  @if ($heading ?? true)
    <h1 class="result-board-title">ตรวจหวย</h1>
  @else
    <p class="result-board-title">ตรวจหวย</p>
  @endif
  <p class="result-board-date">ผลสลากกินแบ่งรัฐบาล งวดวันที่<br />{{ ThaiDate::long($date) }}</p>

  @if (! $draw)
    <p class="result-board-status">
      {{ $isToday ? 'ถ่ายทอดสดการออกรางวัล 14:30 น. ผลจะขึ้นอัตโนมัติ' : 'รอประกาศผล วันที่ '.ThaiDate::long($date).' เวลา 14:30 น.' }}
    </p>
  @elseif (! $complete)
    <p class="result-board-status is-live">กำลังประกาศผลสด หน้านี้อัปเดตอัตโนมัติ</p>
  @endif

  <div class="result-board-grid">
    @foreach ($cards as [$tier, $tone])
      @php
        $numbers = $draw?->numbersFor($tier) ?? [];
        $slots = $tier->expectedCount();
      @endphp
      <div class="result-card {{ $tone }} {{ $tier === PrizeTier::First ? 'is-first' : '' }}">
        <h2>{{ $tier->label() }}</h2>
        <div class="result-card-numbers">
          @for ($i = 0; $i < $slots; $i++)
            <strong class="{{ isset($numbers[$i]) ? '' : 'is-placeholder' }}">{{ $numbers[$i] ?? str_repeat('X', $tier->digits()) }}</strong>
          @endfor
        </div>
        <small>รางวัลละ {{ number_format($draw?->amountFor($tier) ?? $defaultAmounts[$tier->value]) }} บาท</small>
      </div>
    @endforeach
  </div>

  <a class="button result-board-all" href="{{ url('/ตรวจหวย/'.ThaiDate::slug($date)) }}">ดูผลรางวัลทั้งหมด</a>

  @if ($latest)
    <form class="quick-check" data-check-form data-endpoint="{{ route('api.check') }}" data-draw-date="{{ $latest->draw_date->toDateString() }}" data-results="#quickResults">
      <label for="quickNumber">ตรวจสลากกับงวด {{ $latest->thaiDate() }}</label>
      <div class="quick-check-row">
        <input id="quickNumber" name="numbers" type="text" inputmode="numeric" autocomplete="off" maxlength="200" placeholder="กรอกเลขสลาก 6 หลัก" />
        <button class="button primary" type="submit">ตรวจ</button>
      </div>
    </form>
    <div id="quickResults" class="quick-results" aria-live="polite"></div>
  @endif

  <p class="result-board-source">ข้อมูลจากสำนักงานสลากกินแบ่งรัฐบาล</p>
</section>
