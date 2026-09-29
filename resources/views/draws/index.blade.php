@extends('layouts.app')

@section('title', 'ตรวจหวยย้อนหลัง ผลสลากกินแบ่งรัฐบาลทุกงวด'.($draws->currentPage() > 1 ? ' หน้า '.$draws->currentPage() : '').' | ตรวจหวยเช็คสลาก.com')
@section('description', 'ตรวจหวยย้อนหลัง ผลสลากกินแบ่งรัฐบาลทุกงวด รางวัลที่ 1 เลขหน้า 3 ตัว เลขท้าย 3 ตัว เลขท้าย 2 ตัว ข้อมูลจากสำนักงานสลากกินแบ่งรัฐบาล')

@section('content')
  <section class="archive-section">
    <div class="section-heading">
      <p class="eyebrow">ตรวจหวยย้อนหลัง</p>
      <h1>ผลสลากกินแบ่งรัฐบาลย้อนหลังทุกงวด</h1>
    </div>
    <div class="archive-grid">
      @foreach ($draws as $draw)
        @include('partials.archive-card', ['draw' => $draw])
      @endforeach
    </div>
    <nav class="draw-pager" aria-label="เปลี่ยนหน้า">
      @if ($draws->previousPageUrl())<a class="button secondary" href="{{ $draws->previousPageUrl() }}">← ใหม่กว่า</a>@else<span></span>@endif
      <span class="page-info">หน้า {{ $draws->currentPage() }} / {{ $draws->lastPage() }}</span>
      @if ($draws->nextPageUrl())<a class="button secondary" href="{{ $draws->nextPageUrl() }}">เก่ากว่า →</a>@endif
    </nav>
  </section>
@endsection
