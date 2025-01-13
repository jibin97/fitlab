const hamburger = document.querySelector(".hamburger");
const navLinks = document.querySelector(".nav-links");

hamburger.addEventListener("click", () => {
  hamburger.classList.toggle("active");
  navLinks.classList.toggle("active");
});

// Close menu when clicking a link
document.querySelectorAll(".nav-links a").forEach((link) => {
  link.addEventListener("click", () => {
    hamburger.classList.remove("active");
    navLinks.classList.remove("active");
  });
});

// Animate features on scroll
const observerOptions = {
  threshold: 0.2,
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      if (
        entry.target.classList.contains("group-svg-line") ||
        entry.target.classList.contains("svg-line")
      ) {
        entry.target.classList.add("animate");
      } else {
        entry.target.classList.add("fade-in");
      }
    }
  });
}, observerOptions);

document.querySelectorAll(".feature-card, .service-item").forEach((item) => {
  observer.observe(item);
});

document.querySelectorAll(".group-svg-line").forEach((item) => {
  observer.observe(item);
});

document.querySelectorAll(".svg-line").forEach((item) => {
  observer.observe(item);
});

document.querySelectorAll(".nav-link").forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute("href"));

    if (target) {
      // Close mobile menu if open
      const hamburger = document.querySelector(".hamburger");
      const navLinks = document.querySelector(".nav-links");
      if (hamburger.classList.contains("active")) {
        hamburger.classList.remove("active");
        navLinks.classList.remove("active");
      }

      // Smooth scroll to target
      target.scrollIntoView({
        behavior: "smooth",
        block: "start",
      });
    }
  });
});

// Select all nav links
const navLinkList = document.querySelectorAll(".nav-link");

navLinkList.forEach((link) => {
  link.addEventListener("click", function (event) {
    event.preventDefault();
    navLinkList.forEach((link) => link.classList.remove("active"));
    this.classList.add("active");
  });
});

const swiper = new Swiper(".swiper", {
  autoplay: {
    delay: 3000,
    disableOnInteraction: true,
  },
  slidesPerView: 3,
  spaceBetween: 20,
  loop: true,
  keyboard: {
    enabled: true,
  },
  navigation: {
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev",
  },
  breakpoints: {
    320: {
      slidesPerView: 1,
      spaceBetween: 10,
    },
    768: {
      slidesPerView: 2,
      spaceBetween: 15,
    },
    1024: {
      slidesPerView: 3,
      spaceBetween: 20,
    },
  },
});

swiper.el.addEventListener("mouseleave", function () {
  swiper.autoplay.start();
});
