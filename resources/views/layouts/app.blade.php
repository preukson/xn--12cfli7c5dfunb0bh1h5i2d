<!doctype html>
<html lang="th">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'ตรวจหวยเช็คสลาก.com | ตรวจหวยวันนี้ ตรวจหลายใบ ครบทุกรางวัล')</title>
    <meta name="description" content="@yield('description', 'ตรวจหวยวันนี้ ตรวจผลสลากกินแบ่งรัฐบาลหลายใบพร้อมกัน ครบทุกรางวัล ข้อมูลจากสำนักงานสลากกินแบ่งรัฐบาล พร้อมผลหวยย้อนหลัง')" />
    <link rel="canonical" href="@yield('canonical', url()->current())" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="@yield('title', 'ตรวจหวยเช็คสลาก.com')" />
    <meta property="og:image" content="{{ asset('assets/hero-lottery-ai-model.webp') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}" />
    @stack('head')
  </head>
  <body>
    <header class="site-header">
      <a class="brand" href="{{ route('home') }}" aria-label="ตรวจหวยเช็คสลาก.com">
        <span class="brand-mark">AI</span>
        <span>ตรวจหวยเช็คสลาก.com</span>
      </a>
      <a class="mobile-scan" href="{{ route('home') }}#checker" aria-label="ตรวจหวย">
        <svg viewBox="0 0 24 24" aria-hidden="true">
          <path d="M3 8V5a2 2 0 0 1 2-2h3M16 3h3a2 2 0 0 1 2 2v3M21 16v3a2 2 0 0 1-2 2h-3M8 21H5a2 2 0 0 1-2-2v-3M7 12h10" />
        </svg>
        <span>ตรวจหวย</span>
      </a>
      <nav class="nav-links" aria-label="เมนูหลัก">
        <a href="{{ route('home') }}#checker">ตรวจหวย</a>
        <a href="{{ route('home') }}#photo">ถ่ายรูปหวย</a>
        <a href="{{ route('home') }}#news">ข่าวหวย</a>
        <a href="{{ route('draws.index') }}">ย้อนหลัง</a>
      </nav>
    </header>

    <main id="top">
      @yield('content')
    </main>

    <footer class="site-footer">
      <strong>ตรวจหวยเช็คสลาก.com</strong>
      <span>ผลรางวัลดึงจากสำนักงานสลากกินแบ่งรัฐบาล (glo.or.th) โปรดตรวจยืนยันกับใบสลากและประกาศทางการทุกครั้ง</span>
    </footer>

    <script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}" defer></script>
  </body>
</html>
