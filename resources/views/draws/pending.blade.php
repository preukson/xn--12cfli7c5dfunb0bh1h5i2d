@extends('layouts.app')

@php
  $today = now('Asia/Bangkok');
  $drawDate = $today->day <= 15
    ? $today->copy()->day(16)
    : $today->copy()->addMonthNoOverflow()->startOfMonth();
  $thaiMonths = [
    1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
    5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
    9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม',
  ];
  $thaiDate = $drawDate->day.' '.$thaiMonths[$drawDate->month].' '.($drawDate->year + 543);
@endphp

@section('title', 'ตรวจหวย '.$thaiDate.' รอประกาศผล | ตรวจหวยเช็คสลาก.com')

@section('content')
  <main class="pending-draw-page">
    <section class="pending-draw-hero">
      <p class="eyebrow">เตรียมตรวจหวยงวดถัดไป</p>
      <h1>ผลสลากกินแบ่งรัฐบาล<br>งวดวันที่ {{ $thaiDate }}</h1>
      <p class="pending-status">ระบบกำลังรอผลรางวัลจากสำนักงานสลากกินแบ่งรัฐบาล</p>

      <div class="pending-main-prize">
        <span>รางวัลที่ 1</span>
        <strong>XXXXXX</strong>
        <small>รางวัลละ 6,000,000 บาท</small>
      </div>

      <div class="pending-prize-grid">
        <div><span>เลขท้าย 2 ตัว</span><strong>XX</strong></div>
        <div><span>เลขหน้า 3 ตัว</span><strong>XXX&nbsp;&nbsp;XXX</strong></div>
        <div><span>เลขท้าย 3 ตัว</span><strong>XXX&nbsp;&nbsp;XXX</strong></div>
      </div>

      <p class="pending-note">เมื่อประกาศผล ระบบจะอัปเดตตัวเลขและเปิดตรวจสลากงวดนี้โดยอัตโนมัติ</p>
      <a class="button primary" href="{{ url('/#checker') }}">ตรวจหวยงวดล่าสุด</a>
    </section>
  </main>
@endsection
