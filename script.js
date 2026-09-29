const prizeData = {
  first: "730640",
  front3: ["060", "521"],
  back3: ["266", "041"],
  back2: "64",
};

const form = document.querySelector("#lotteryForm");
const input = document.querySelector("#lotteryNumbers");
const results = document.querySelector("#results");
const photoInput = document.querySelector("#ticketPhoto");
const photoPreview = document.querySelector("#photoPreview");
const luckyButton = document.querySelector("#luckyButton");
const luckyNumber = document.querySelector("#luckyNumber");
const luckyModes = document.querySelectorAll(".lucky-modes button");
const luckyAction = document.querySelector("#luckyAction");
let luckyDigits = 3;
let luckyRolling;

function normalizeNumbers(value) {
  return value
    .split(/[\s,，]+/)
    .map((item) => item.replace(/\D/g, ""))
    .filter((item) => item.length === 6);
}

function checkNumber(number) {
  const prizes = [];
  const front = number.slice(0, 3);
  const back3 = number.slice(3);
  const back2 = number.slice(4);

  if (number === prizeData.first) prizes.push("ถูกรางวัลที่ 1");
  if (prizeData.front3.includes(front)) prizes.push("ถูกเลขหน้า 3 ตัว");
  if (prizeData.back3.includes(back3)) prizes.push("ถูกเลขท้าย 3 ตัว");
  if (back2 === prizeData.back2) prizes.push("ถูกเลขท้าย 2 ตัว");

  return prizes;
}

form.addEventListener("submit", (event) => {
  event.preventDefault();
  const numbers = normalizeNumbers(input.value);

  if (!numbers.length) {
    results.innerHTML = '<p class="empty-state">กรุณากรอกเลขสลาก 6 หลักอย่างน้อย 1 ใบ</p>';
    return;
  }

  results.innerHTML = numbers
    .map((number) => {
      const prizes = checkNumber(number);
      const hasPrize = prizes.length > 0;
      return `
        <div class="result-item">
          <div class="result-number">${number}</div>
          <div>
            <strong class="${hasPrize ? "win" : "miss"}">${hasPrize ? "ยินดีด้วย ถูกรางวัล" : "ยังไม่พบรางวัล"}</strong>
            <p>${hasPrize ? prizes.join(" · ") : "ลองตรวจงวดอื่น หรือตรวจซ้ำกับผลจากกองสลาก"}</p>
          </div>
        </div>
      `;
    })
    .join("");
});

photoInput.addEventListener("change", () => {
  const file = photoInput.files[0];
  if (!file) return;
  photoPreview.textContent = `AI OCR พร้อมอ่านเลขจากรูป: ${file.name}`;
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

luckyButton.addEventListener("click", () => {
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
