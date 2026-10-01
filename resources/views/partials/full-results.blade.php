{{-- รางวัลข้างเคียงถึงรางวัลที่ 5 ต่อจากบอร์ดผลด้านบน: ต้องส่ง $date และ $draw (?Draw) --}}
<section class="checker-section full-results">
  <div class="section-heading">
    <p class="eyebrow">ผลรางวัลครบทุกรางวัล</p>
    <h2>ผลสลากกินแบ่งรัฐบาล งวดวันที่ {{ App\Support\ThaiDate::long($date) }}</h2>
  </div>
  @include('partials.prize-table', ['draw' => $draw, 'withTop' => false])
  <p class="hint">เพื่อความถูกต้อง โปรดตรวจสอบกับประกาศของสำนักงานสลากกินแบ่งรัฐบาลอีกครั้ง</p>
</section>
