@extends('layouts.app')

@php
  use App\Enums\PrizeTier;
  use App\Support\ThaiDate;
@endphp

@section('title', 'ตรวจหวยวันนี้'.($latest ? ' งวด '.$latest->thaiDate() : '').' | ตรวจหวยเช็คสลาก.com')

@section('content')
  <section class="hero">
    <img class="hero-image" src="{{ asset('assets/hero-lottery-ai-model.webp') }}" alt="สาวไทยกำลังใช้โทรศัพท์ตรวจสลากหลายใบ" fetchpriority="high" />
    <div class="hero-shade" aria-hidden="true"></div>
    <div class="hero-copy">
      <p class="eyebrow hero-eyebrow">ตรวจหวย ง่าย แม่นยำ รู้ผลไว</p>
      <h1>ตรวจหวยวันนี้ ตรวจได้ทีละหลายใบในหน้าเดียว</h1>
      <p class="lead">
        กรอกเลขสลากหลายใบพร้อมกัน ระบบตรวจครบทุกรางวัล ตั้งแต่รางวัลที่ 1 รางวัลข้างเคียง ถึงเลขท้าย 2 ตัว
        ด้วยผลจากสำนักงานสลากกินแบ่งรัฐบาลที่ดึงอัตโนมัติทุกงวด
      </p>
      <div class="hero-actions">
        <a class="button primary" href="#checker">ตรวจหวยวันนี้</a>
        <a class="button secondary" href="{{ route('draws.index') }}">ตรวจหวยย้อนหลัง</a>
      </div>
      <div class="trust-row" aria-label="จุดเด่น">
        <span>ครบ 173 รางวัลทุกงวด</span>
        <span>ตรวจได้ 100 ใบต่อครั้ง</span>
        <span>ผลย้อนหลังตั้งแต่ปี 2559</span>
      </div>
    </div>
    <div class="hero-panel" aria-label="สรุปผลรางวัลงวดล่าสุด">
      <div class="status-pill">งวดถัดไป {{ ThaiDate::long($nextDrawDate) }}</div>
      @if ($latest)
        <p class="panel-title">ผลงวด {{ $latest->thaiDate() }} · {{ $latest->statusLabel() }}</p>
        <div class="prize-card main-prize">
          <span>รางวัลที่ 1</span>
          <strong>{{ $latest->numbersFor(PrizeTier::First)[0] ?? 'รอผลสด' }}</strong>
          <small>รางวัลละ {{ number_format($latest->amountFor(PrizeTier::First) ?? 6000000) }} บาท</small>
        </div>
        <div class="mini-grid">
          @foreach ([PrizeTier::Front3, PrizeTier::Back3, PrizeTier::Back2] as $tier)
            <div><span>{{ $tier->label() }}</span><strong>{{ implode(' ', $latest->numbersFor($tier)) ?: str_repeat('-', $tier->digits()) }}</strong></div>
          @endforeach
        </div>
        <a class="panel-link" href="{{ $latest->url() }}">ดูผลรางวัลครบทุกรางวัล →</a>
      @else
        <p class="panel-title">ยังไม่มีข้อมูลผลรางวัล</p>
      @endif
    </div>
  </section>

  <section id="checker" class="checker-section">
    <div class="section-heading">
      <p class="eyebrow">ตรวจผลสลากกินแบ่งรัฐบาล</p>
      <h2>ตรวจหวยหลายใบพร้อมกัน</h2>
      <p>ใส่เลขสลาก 6 หลัก คั่นด้วยเว้นวรรค จุลภาค หรือขึ้นบรรทัดใหม่ รองรับเลขไทย และเลขที่พิมพ์เว้นวรรคกลาง เช่น 730 640</p>
    </div>
    @include('partials.checker', ['selected' => $latest?->draw_date->toDateString()])
  </section>

  <section id="photo" class="photo-section">
    <div class="photo-copy">
      <p class="eyebrow">ถ่ายรูปหวย ตรวจจับเลขหวย · เร็ว ๆ นี้</p>
      <h2>อัปโหลดรูปสลาก แล้วให้ AI ดึงเลขไปตรวจ</h2>
      <p>
        สำหรับคนมีหลายใบ: ถ่ายรูปแผงสลากรูปเดียว AI จะอ่านเลข 6 หลักทุกใบ ให้คุณตรวจทานเลขก่อน แล้วส่งเข้าตัวตรวจหวยทันที
        ระหว่างนี้กรุณากรอกเลขด้วยตนเอง
      </p>
    </div>
    <div class="upload-card">
      <input id="ticketPhoto" type="file" accept="image/*" disabled />
      <label for="ticketPhoto" aria-disabled="true">
        <span class="upload-icon">+</span>
        <strong>ถ่ายรูปสลาก (กำลังพัฒนา)</strong>
        <small>รองรับ JPG, PNG, HEIC</small>
      </label>
      <div id="photoPreview" class="photo-preview">ฟีเจอร์นี้จะเปิดใช้งานเร็ว ๆ นี้</div>
    </div>
  </section>

  <section class="agent-section">
    <div>
      <p class="eyebrow">ระบบดึงผลอัตโนมัติ</p>
      <h2>ผลสดจากสำนักงานสลากกินแบ่งรัฐบาล</h2>
      <p>
        ในวันออกรางวัล ระบบดึงผลจากสำนักงานสลากกินแบ่งรัฐบาลทุก 1 นาทีระหว่างถ่ายทอดสด แสดงสถานะชัดเจนว่าผลออกครบหรือยัง
        และเก็บผลย้อนหลังทุกงวดไว้ให้ตรวจได้ตลอด
      </p>
    </div>
    <div class="agent-steps">
      <div><span>01</span><strong>เฝ้าประกาศผล</strong><small>ดึงผลทุกนาทีช่วง 14:30–16:45 ของวันออกรางวัล</small></div>
      <div><span>02</span><strong>ตรวจครบทุกรางวัล</strong><small>รางวัลที่ 1–5 รางวัลข้างเคียง เลขหน้า เลขท้าย รวม 173 รางวัล</small></div>
      <div><span>03</span><strong>ยืนยันความถูกต้อง</strong><small>ผลขึ้นสถานะ "ผลสด" จนกว่าจะครบ แล้วจึงเป็นผลทางการ</small></div>
    </div>
  </section>

  @if ($archive->isNotEmpty())
    <section id="archive" class="archive-section">
      <div class="section-heading">
        <p class="eyebrow">ตรวจหวยย้อนหลัง</p>
        <h2>ผลสลากงวดก่อนหน้า</h2>
      </div>
      <div class="archive-grid">
        @foreach ($archive as $draw)
          @include('partials.archive-card', ['draw' => $draw])
        @endforeach
      </div>
      <p class="more-link"><a class="button secondary" href="{{ route('draws.index') }}">ดูผลย้อนหลังทั้งหมด</a></p>
    </section>
  @endif

  <section id="news" class="news-section">
    <div class="section-heading">
      <p class="eyebrow">บทความหวย</p>
      <h2>ข่าวหวย บทความหวย เรื่องเล่าคนถูกหวย</h2>
    </div>
    <div class="news-grid">
      <article class="feature-article">
        <img src="{{ asset('assets/news-lucky-goddess.webp') }}" alt="เจ้าแม่เลขเด็ดน่ารักกำลังหยิบเลขนำโชคจากขันทอง" loading="lazy" />
        <div class="news-content">
          <span>สถิติหวย</span>
          <h3>สถิติเลขท้าย 2 ตัวที่ออกบ่อยจากผลย้อนหลัง</h3>
          <p>รวมสถิติจากผลรางวัลย้อนหลังหลายปี อ่านเพื่อความบันเทิง ผลสลากแต่ละงวดเป็นการสุ่ม</p>
        </div>
      </article>
      <article>
        <img src="{{ asset('assets/news-fortune-goddess.webp') }}" alt="เจ้าแม่สายมูน่ารักกำลังเสี่ยงเซียมซี" loading="lazy" />
        <div class="news-content">
          <span>สายมู</span>
          <h3>รวมคาถาขอหวยยอดนิยมของสายมู</h3>
          <p>รวบรวมบทสวดที่คนนิยมท่องก่อนซื้อสลาก อ่านเป็นความเชื่อและความบันเทิง</p>
        </div>
      </article>
      <article>
        <img src="{{ asset('assets/news-ai-goddess.webp') }}" alt="เจ้าแม่ AI น่ารักกำลังแตะหน้าจอสุ่มเลข" loading="lazy" />
        <div class="news-content">
          <span>สุ่มเลข</span>
          <h3>สุ่มเลขนำโชคแบบสนุก ๆ</h3>
          <p>ลองสุ่มเลขนำโชค 2, 3 หรือ 6 ตัวด้านล่าง เพื่อความบันเทิง</p>
        </div>
      </article>
    </div>
  </section>

  <section id="lucky" class="lucky-section">
    <img class="lucky-model" src="{{ asset('assets/lucky-number-model.webp') }}" alt="นางแบบชุดไทยกำลังชี้ไปที่เครื่องสุ่มเลขนำโชค" loading="lazy" />
    <div class="lucky-interface">
      <div class="lucky-copy">
        <p class="eyebrow">ขูดเลขนำโชค</p>
        <h2>เซียมซีสุ่มเลขนำโชค</h2>
        <p id="luckyDescription">เลือกจำนวนหลัก แล้วแตะเซียมซีเพื่อรับเลขนำโชค (สุ่มเพื่อความบันเทิง ไม่มีผลต่อโอกาสถูกรางวัล)</p>
      </div>
      <div class="lucky-modes" role="group" aria-label="เลือกจำนวนเลขเสี่ยงโชค">
        <button type="button" data-digits="6" aria-pressed="false">เสี่ยงโชค 6 ตัว</button>
        <button class="is-active" type="button" data-digits="3" aria-pressed="true">เสี่ยงโชค 3 ตัว</button>
        <button type="button" data-digits="2" aria-pressed="false">เสี่ยงโชค 2 ตัว</button>
      </div>
      <button class="scratch-card" id="luckyButton" type="button" data-digits="3">
        <span class="number-mist" aria-hidden="true"><b>9</b><b>3</b><b>6</b><b>8</b><b>2</b><b>7</b></span>
        <span class="scratch-label">
          <svg viewBox="0 0 64 64" aria-hidden="true">
            <path d="M22 18 18 5M30 17 29 3M38 18l4-13M17 21h30l-4 36H21z" />
            <path d="M20 29h24M23 49h18" />
          </svg>
          <span>เซียมซี</span>
        </span>
        <strong id="luckyNumber" aria-live="polite">369</strong>
        <span class="scratch-action" id="luckyAction">แตะเพื่อสุ่มเลข 3 ตัวใหม่</span>
      </button>
    </div>
  </section>

  <section class="seo-section">
    <h2>ตรวจหวยงวดล่าสุด</h2>
    <div class="tag-cloud">
      @foreach ($drawOptions->take(12) as $option)
        <a href="{{ $option->url() }}">ตรวจหวย {{ $option->thaiDate() }}</a>
      @endforeach
      <a href="{{ route('draws.index') }}">ตรวจหวยย้อนหลังทั้งหมด</a>
    </div>
  </section>
@endsection
