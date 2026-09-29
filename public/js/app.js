const form = document.querySelector("#lotteryForm");
const input = document.querySelector("#lotteryNumbers");
const drawSelect = document.querySelector("#drawDate");
const results = document.querySelector("#results");
const luckyButton = document.querySelector("#luckyButton");
const luckyNumber = document.querySelector("#luckyNumber");
const luckyModes = document.querySelectorAll(".lucky-modes button");
const luckyAction = document.querySelector("#luckyAction");
let luckyDigits = 3;
let luckyRolling;

const baht = new Intl.NumberFormat("th-TH");

function escapeHtml(value) {
  const div = document.createElement("div");
  div.textContent = value;
  return div.innerHTML;
}

function renderResults(data) {
  const winners = data.results.filter((r) => r.won).length;
  const summary = `
    <div class="result-summary ${winners ? "win" : ""}">
      <strong>งวด ${escapeHtml(data.draw.label)} · ตรวจ ${data.results.length} ใบ ถูกรางวัล ${winners} ใบ</strong>
      ${winners ? `<span>รวมเงินรางวัล ${baht.format(data.total)} บาท</span>` : ""}
      ${data.draw.complete ? "" : `<small>ผลงวดนี้ยังประกาศไม่ครบ ผลตรวจอาจเปลี่ยนแปลง</small>`}
    </div>`;

  const notices = [];
  if (data.invalid.length) {
    notices.push(`อ่านเลขไม่ได้ ${data.invalid.length} รายการ: ${data.invalid.map(escapeHtml).join(", ")}`);
  }
  if (data.truncated) notices.push("ตรวจได้ครั้งละไม่เกิน 100 ใบ ระบบตรวจเฉพาะ 100 ใบแรก");

  const items = data.results
    .map(
      (r) => `
        <div class="result-item">
          <div class="result-number">${r.number}</div>
          <div>
            <strong class="${r.won ? "win" : "miss"}">${r.won ? `ถูกรางวัล ${baht.format(r.total)} บาท` : "ไม่ถูกรางวัล"}</strong>
            <p>${r.won ? r.prizes.map((p) => `${escapeHtml(p.label)} (${baht.format(p.amount)} บาท)`).join(" · ") : "ลองตรวจงวดอื่น"}</p>
          </div>
        </div>`,
    )
    .join("");

  results.innerHTML =
    summary + notices.map((n) => `<p class="notice">${n}</p>`).join("") + items;
}

form?.addEventListener("submit", async (event) => {
  event.preventDefault();
  const button = form.querySelector("button[type=submit]");
  button.disabled = true;
  results.innerHTML = '<p class="empty-state">กำลังตรวจ...</p>';

  try {
    const response = await fetch(form.dataset.endpoint, {
      method: "POST",
      headers: { "Content-Type": "application/json", Accept: "application/json" },
      body: JSON.stringify({ numbers: input.value, draw_date: drawSelect?.value || null }),
    });
    const data = await response.json();

    if (!response.ok) {
      const invalid = data.invalid?.length ? `<br>อ่านไม่ได้: ${data.invalid.map(escapeHtml).join(", ")}` : "";
      const message = response.status === 429 ? "ตรวจถี่เกินไป กรุณารอสักครู่" : data.message || "ตรวจไม่สำเร็จ";
      results.innerHTML = `<p class="empty-state">${escapeHtml(message)}${invalid}</p>`;
      return;
    }

    renderResults(data);
    window.gtag?.("event", "check_lottery", {
      draw_date: data.draw.date,
      tickets: data.results.length,
      winners: data.results.filter((r) => r.won).length,
    });
  } catch {
    results.innerHTML = '<p class="empty-state">เชื่อมต่อไม่ได้ กรุณาลองใหม่อีกครั้ง</p>';
  } finally {
    button.disabled = false;
  }
});

luckyModes.forEach((mode) => {
  mode.addEventListener("click", () => {
    luckyDigits = Number(mode.dataset.digits);
    luckyModes.forEach((button) => {
      const isActive = button === mode;
      button.classList.toggle("is-active", isActive);
      button.setAttribute("aria-pressed", String(isActive));
    });

    luckyButton.dataset.digits = String(luckyDigits);
    luckyNumber.textContent = "0".repeat(luckyDigits);
    luckyAction.textContent = `แตะเพื่อสุ่มเลข ${luckyDigits} ตัว`;
  });
});

luckyButton?.addEventListener("click", () => {
  window.clearInterval(luckyRolling);
  luckyButton.classList.add("is-rolling");
  let spins = 0;
  const limit = 10 ** luckyDigits;
  luckyRolling = window.setInterval(() => {
    luckyNumber.textContent = Math.floor(Math.random() * limit)
      .toString()
      .padStart(luckyDigits, "0");
    spins += 1;

    if (spins >= 9) {
      window.clearInterval(luckyRolling);
      luckyButton.classList.remove("is-rolling");
    }
  }, 55);
});
