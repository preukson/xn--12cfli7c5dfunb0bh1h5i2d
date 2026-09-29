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
  <section class="checker-section draw-page">
    <nav class="breadcrumb" aria-label="breadcrumb">
      <a href="{{ route('home') }}">หน้าแรก</a> / <a href="{{ route('draws.index') }}">ตรวจหวยย้อนหลัง</a> / <span>{{ $draw->thaiDate() }}</span>
    </nav>
    <div class="section-heading">
      <p class="eyebrow">{{ $draw->statusLabel() }}</p>
      <h1>ตรวจหวย งวด {{ $draw->thaiDate() }}</h1>
      <p>ผลสลากกินแบ่งรัฐบาลครบทุกรางวัล ข้อมูลจากสำนักงานสลากกินแบ่งรัฐบาล
        @if ($draw->pdf_url) · <a class="text-link" href="{{ $draw->pdf_url }}" target="_blank" rel="noopener">ใบตรวจผลทางการ (PDF)</a>@endif
      </p>
    </div>

    @unless ($draw->isComplete())
      <p class="notice">ผลงวดนี้ยังประกาศไม่ครบ หน้านี้อัปเดตอัตโนมัติทุก 1 นาที</p>
    @endunless

    @include('partials.prize-table', ['draw' => $draw])
  </section>

  <section id="checker" class="checker-section">
    <div class="section-heading">
      <h2>ตรวจสลากงวด {{ $draw->thaiDate() }} หลายใบพร้อมกัน</h2>
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
@endsection
