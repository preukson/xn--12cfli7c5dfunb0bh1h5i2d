{{-- ตัวตรวจหวยหลายใบ ใช้ทั้งหน้าแรกและหน้าผลแต่ละงวด: ต้องส่ง $drawOptions และ $selected (Y-m-d หรือ null) --}}
<div class="checker-layout">
  <form class="checker-card" id="lotteryForm" data-endpoint="{{ route('api.check') }}">
    <label for="lotteryNumbers">เลขสลากของคุณ</label>
    <textarea id="lotteryNumbers" name="numbers" rows="7" inputmode="numeric" placeholder="เช่น 730640&#10;417 212, 004615&#10;639214"></textarea>
    <div class="form-row">
      <select id="drawDate" name="draw_date" aria-label="เลือกงวดวันที่">
        @foreach ($drawOptions as $option)
          <option value="{{ $option->draw_date->toDateString() }}" @selected($selected === $option->draw_date->toDateString())>
            งวด {{ $option->thaiDate() }}
          </option>
        @endforeach
      </select>
      <button class="button primary" type="submit">ตรวจสลากฯ ของคุณ</button>
    </div>
    <p class="hint">ตรวจได้ครั้งละไม่เกิน 100 ใบ ครบทุกรางวัลรวมรางวัลข้างเคียง ข้อมูลจากสำนักงานสลากกินแบ่งรัฐบาล โปรดตรวจกับใบสลากจริงอีกครั้งก่อนขึ้นเงิน</p>
  </form>
  <div class="results-card" id="results" aria-live="polite">
    <p class="empty-state">ผลตรวจจะแสดงที่นี่เมื่อคุณกรอกเลขสลาก</p>
  </div>
</div>
