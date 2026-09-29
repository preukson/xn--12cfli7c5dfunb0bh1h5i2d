const drawHero = document.querySelector("[data-draw-hero]");

if (drawHero) {
  const slides = [...drawHero.querySelectorAll(".draw-hero-slide")];
  const dots = [...drawHero.querySelectorAll("[data-hero-dot]")];
  let activeSlide = 0;
  let heroTimer;

  const showSlide = (index) => {
    activeSlide = index;
    slides.forEach((slide, slideIndex) => slide.classList.toggle("is-active", slideIndex === index));
    dots.forEach((dot, dotIndex) => {
      const isActive = dotIndex === index;
      dot.classList.toggle("is-active", isActive);
      dot.setAttribute("aria-current", isActive ? "true" : "false");
    });
  };

  const startHero = () => {
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;
    window.clearInterval(heroTimer);
    heroTimer = window.setInterval(() => showSlide((activeSlide + 1) % slides.length), 5500);
  };

  dots.forEach((dot) => {
    dot.addEventListener("click", () => {
      showSlide(Number(dot.dataset.heroDot));
      startHero();
    });
  });

  showSlide(0);
  startHero();
}

const drawChecker = document.querySelector(".draw-checker-feature");
const drawResults = drawChecker?.querySelector("#results");

if (drawChecker && drawResults) {
  const renderResultScene = () => {
    if (drawResults.querySelector(".draw-result-scene")) return;

    const resultText = drawResults.textContent.replace(/\s+/g, " ").trim();
    const isPending = !resultText || resultText.includes("กำลังตรวจ") || resultText.includes("ผลตรวจจะแสดง");
    if (isPending) return;

    const hasPrize = /ถูกรางวัล\s+[1-9]\d*\s+ใบ/.test(resultText);
    const wonFirstPrize = hasPrize && resultText.includes("รางวัลที่ 1");
    const amount = resultText.match(/รวมเงินรางวัล\s+([\d,]+)\s+บาท/)?.[1];

    const scene = document.createElement("section");
    scene.className = `draw-result-scene ${hasPrize ? "is-winner" : "is-try-again"}`;

    const image = document.createElement("img");
    image.src = hasPrize ? drawChecker.dataset.winnerImage : drawChecker.dataset.tryAgainImage;
    image.alt = hasPrize
      ? "นางแบบชุดไทยและผู้โชคดีร่วมแสดงความยินดี"
      : "นางแบบชุดไทยให้กำลังใจสำหรับงวดถัดไป";

    const copy = document.createElement("div");
    copy.className = "draw-result-copy";

    const eyebrow = document.createElement("span");
    eyebrow.className = "draw-result-eyebrow";
    eyebrow.textContent = hasPrize ? "ผลตรวจสลากของคุณ" : "งวดนี้ยังไม่ใช่ของเรา";

    const title = document.createElement("h3");
    title.textContent = hasPrize
      ? wonFirstPrize
        ? "ยินดีด้วย คุณถูกรางวัลที่ 1"
        : "ยินดีด้วย คุณถูกรางวัล"
      : "คุณไม่ถูกรางวัล";

    const message = document.createElement("p");
    message.textContent = hasPrize
      ? "ตรวจรายละเอียดรางวัลด้านล่าง และตรวจยืนยันกับใบสลากจริงก่อนขึ้นเงิน"
      : "โอกาสหน้าคุณอาจจะรวยก็ได้นะคะ ลองใหม่อีกครั้งในงวดหน้า";

    copy.append(eyebrow, title);

    if (amount) {
      const prizeAmount = document.createElement("strong");
      prizeAmount.className = "draw-result-amount";
      prizeAmount.textContent = `${amount} บาท`;
      copy.append(prizeAmount);
    }

    copy.append(message);
    scene.append(image, copy);
    drawResults.prepend(scene);
  };

  new MutationObserver(renderResultScene).observe(drawResults, { childList: true, subtree: true });
  renderResultScene();
}
