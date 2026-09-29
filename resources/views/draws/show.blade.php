@extends('layouts.app')

@php use App\Enums\PrizeTier; @endphp

@section('title', 'ตรวจหวย '.$draw->thaiDate().' ผลสลากกินแบ่งรัฐบาล ครบทุกรางวัล | ตรวจหวยเช็คสลาก.com')
@section('description', 'ผลสลากกินแบ่งรัฐบาล งวดวันที่ '.$draw->thaiDate().' รางวัลที่ 1 '.($draw->numbersFor(PrizeTier::First)[0] ?? '').' เลขหน้า 3 ตัว '.implode(' ', $draw->numbersFor(PrizeTier::Front3)).' เลขท้าย 3 ตัว '.implode(' ', $draw->numbersFor(PrizeTier::Back3)).' เลขท้าย 2 ตัว '.implode(' ', $draw->numbersFor(PrizeTier::Back2)).' ตรวจหลายใบพร้อมกันได้ทันที')

@push('head')
  <script type="application/ld+json">
    {!! json_encode([
      '@context' => 'https://schema.org',
      '@type' => 'BreadcrumbList',
      'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'หน้าแรก', 'item' => route('home')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'ตรวจหวยย้อนหลัง', 'item' => route('draws.index')],
        ['@type' => 'ListItem', 'position' => 3, 'name' => 'ตรวจหวย '.$draw->thaiDate(), 'item' => $draw->url()],
      ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
  </script>
@endpush

@section('content')
  <section class="draw-hero" data-draw-hero aria-label="ตรวจหวยงวด {{ $draw->thaiDate() }}">
    <div class="draw-hero-slides">
      @foreach ([
        'draw-hero-celebration-1.png',
        'draw-hero-celebration-2.png',
        'draw-hero-celebration-3.png',
      ] as $index => $image)
        <img
          class="draw-hero-slide {{ $index === 0 ? 'is-active' : '' }}"
          src="{{ asset('assets/'.$image) }}"
          alt="นางแบบชุดไทยถือป้ายตรวจรางวัลที่ 1 พร้อมผู้โชคดี"
          @if ($index > 0) loading="lazy" @else fetchpriority="high" @endif
        />
      @endforeach
    </div>
    <div class="draw-hero-overlay" aria-hidden="true"></div>
    <div class="draw-hero-content">
      <nav class="breadcrumb draw-hero-breadcrumb" aria-label="breadcrumb">
        <a href="{{ route('home') }}">หน้าแรก</a> / <a href="{{ route('draws.index') }}">ตรวจหวยย้อนหลัง</a>
      </nav>
      <p class="draw-hero-kicker">ผลสลากกินแบ่งรัฐบาล</p>
      <h1>ตรวจหวย<br />งวด {{ $draw->thaiDate() }}</h1>
      <div class="draw-hero-prize">
        <span>รางวัลที่ 1</span>
        <strong>{{ $draw->numbersFor(PrizeTier::First)[0] ?? 'รอผล' }}</strong>
        <small>เงินรางวัล 6,000,000 บาท</small>
      </div>
      <a class="button draw-hero-cta" href="#checker">ตรวจสลากงวดนี้</a>
    </div>
    <div class="draw-hero-controls" aria-label="เลือกภาพ Hero">
      @foreach ([1, 2, 3] as $index)
        <button type="button" class="{{ $index === 1 ? 'is-active' : '' }}" data-hero-dot="{{ $index - 1 }}" aria-label="ภาพที่ {{ $index }}"></button>
      @endforeach
    </div>
  </section>

  <section class="checker-section draw-page">
    <nav class="breadcrumb" aria-label="breadcrumb">
      <a href="{{ route('home') }}">หน้าแรก</a> / <a href="{{ route('draws.index') }}">ตรวจหวยย้อนหลัง</a> / <span>{{ $draw->thaiDate() }}</span>
    </nav>
    <div class="section-heading">
      <p class="eyebrow">{{ $draw->statusLabel() }}</p>
      <h2>ผลรางวัลครบทุกประเภท</h2>
      <p>ผลสลากกินแบ่งรัฐบาลครบทุกรางวัล ข้อมูลจากสำนักงานสลากกินแบ่งรัฐบาล
        @if ($draw->pdf_url) · <a class="text-link" href="{{ $draw->pdf_url }}" target="_blank" rel="noopener">ใบตรวจผลทางการ (PDF)</a>@endif
      </p>
    </div>

    @unless ($draw->isComplete())
      <p class="notice">ผลงวดนี้ยังประกาศไม่ครบ หน้านี้อัปเดตอัตโนมัติทุก 1 นาที</p>
    @endunless

    @include('partials.prize-table', ['draw' => $draw])
  </section>

  <section
    id="checker"
    class="checker-section draw-checker-feature"
    data-winner-image="{{ asset('assets/result-winner.png') }}"
    data-try-again-image="{{ asset('assets/result-try-again.png') }}"
  >
    <div class="section-heading">
      <p class="eyebrow">ตรวจเฉพาะงวดนี้</p>
      <h2>ลุ้นผลสลาก งวด {{ $draw->thaiDate() }}</h2>
      <p>ใส่เลขสลาก 6 หลักได้หลายใบ ระบบจะรวมเงินรางวัลที่ได้รับให้ทันที</p>
    </div>
    @include('partials.checker', ['selected' => $draw->draw_date->toDateString()])
  </section>

  <nav class="draw-pager checker-section" aria-label="งวดอื่น">
    @if ($previous)<a class="button secondary" href="{{ $previous->url() }}">← งวด {{ $previous->thaiDate() }}</a>@else<span></span>@endif
    @if ($next)<a class="button secondary" href="{{ $next->url() }}">งวด {{ $next->thaiDate() }} →</a>@endif
  </nav>

  @unless ($draw->isComplete())
    <script>setTimeout(() => location.reload(), 60000);</script>
  @endunless
  <script src="{{ asset('js/draw-page.js') }}?v={{ filemtime(public_path('js/draw-page.js')) }}" defer></script>
@endsection
