@extends('layouts.app')

@php use App\Support\ThaiDate; @endphp

@section('title', 'ตรวจหวย '.ThaiDate::long($date).' ผลสลากกินแบ่งรัฐบาล ถ่ายทอดสด | ตรวจหวยเช็คสลาก.com')
@section('description', 'ตรวจหวย งวดวันที่ '.ThaiDate::long($date).' ผลสลากกินแบ่งรัฐบาล รางวัลที่ 1 เลขหน้า 3 ตัว เลขท้าย 3 ตัว เลขท้าย 2 ตัว อัปเดตสดจากสำนักงานสลากกินแบ่งรัฐบาล เวลา 14:30 น.')

@section('content')
  @include('partials.result-board', ['date' => $date, 'draw' => null, 'latest' => $latest])
  @include('partials.full-results', ['date' => $date, 'draw' => null])

  @if ($latest)
    <section id="checker" class="checker-section">
      <div class="section-heading">
        <p class="eyebrow">ระหว่างรอผลงวด {{ ThaiDate::long($date) }}</p>
        <h2>ตรวจหวยงวดก่อนหน้า หลายใบพร้อมกัน</h2>
      </div>
      @include('partials.checker', ['selected' => $latest->draw_date->toDateString()])
    </section>
  @endif
@endsection
