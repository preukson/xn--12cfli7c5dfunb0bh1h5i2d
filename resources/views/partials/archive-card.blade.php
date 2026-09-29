@php use App\Enums\PrizeTier; use App\Support\ThaiDate; @endphp
<article>
  <a href="{{ $draw->url() }}">
    <span>{{ ThaiDate::short($draw->draw_date) }}</span>
    <h3>รางวัลที่ 1: {{ $draw->numbersFor(PrizeTier::First)[0] ?? '------' }}</h3>
    <p>
      เลขหน้า 3 ตัว {{ implode(', ', $draw->numbersFor(PrizeTier::Front3)) }}
      · เลขท้าย 3 ตัว {{ implode(', ', $draw->numbersFor(PrizeTier::Back3)) }}
      · เลขท้าย 2 ตัว {{ implode(', ', $draw->numbersFor(PrizeTier::Back2)) }}
    </p>
  </a>
</article>
