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

const scrollToTop = () => {
  // First method: Using smooth scroll behavior
  window.scrollTo({
    top: 0,
    behavior: "smooth",
  });

  // Fallback method for browsers that don't support smooth scroll
  if (!("scrollBehavior" in document.documentElement.style)) {
    window.scrollTo(0, 0);
  }
};

// Select all nav links
const navLinkList = document.querySelectorAll(".nav-link[data-content]");
let currentIndex = 0;
navLinkList.forEach((link) => {
  link.addEventListener("click", function (event) {
    event.preventDefault();
    // this.classList.add("active");

    const newIndex = parseInt(link.getAttribute("data-index"));
    const contentId = link.getAttribute("data-content");

    // Don't do anything if clicking the same link
    if (newIndex === currentIndex) return;

    const goingRight = newIndex > currentIndex;
    const currentContent = document.querySelector(".main-content.active");
    const newContent = document.getElementById(contentId);

    // Remove active class from all links
    navLinkList.forEach((link) => link.classList.remove("active"));
    link.classList.add("active");

    // Remove any existing animation classes
    const allContents = document.querySelectorAll(".main-content");
    allContents.forEach((content) => {
      content.classList.remove(
        "slide-in-left",
        "slide-in-right",
        "slide-out-left",
        "slide-out-right"
      );
    });

    // Add appropriate animation classes
    if (goingRight) {
      currentContent.classList.add("slide-out-left");
      newContent.classList.add("slide-in-right");
    } else {
      currentContent.classList.add("slide-out-right");
      newContent.classList.add("slide-in-left");
    }

    // Update active states
    currentContent.classList.remove("active");
    newContent.classList.add("active");

    // Update current index
    currentIndex = newIndex;
    // scrollToTop();
    // Clean up animation classes after animation ends
    setTimeout(() => {
      allContents.forEach((content) => {
        if (!content.classList.contains("active")) {
          content.classList.remove(
            "slide-out-left",
            "slide-out-right",
            "slide-in-left",
            "slide-in-right"
          );
        }
      });
    }, 500);
  });
});

// document.addEventListener("DOMContentLoaded", function () {
//   const observer = new IntersectionObserver(
//     (entries) => {
//       entries.forEach((entry) => {
//         if (entry.isIntersecting) {
//           entry.target.style.animationPlayState = "running";
//         }
//       });
//     },
//     {
//       threshold: 0.1,
//     }
//   );

//   document
//     .querySelectorAll("._about_card, ._about_image_container")
//     .forEach((el) => {
//       observer.observe(el);
//     });
// });

//HERO BANNERS
const images = document.querySelectorAll(".hero-bg");
let currentImageIndex = 0;

function nextImage() {
  images[currentImageIndex].classList.remove("active");
  currentImageIndex = (currentImageIndex + 1) % images.length;
  images[currentImageIndex].classList.add("active");
}
// Change image every 5 seconds
setInterval(nextImage, 5000);

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
      spaceBetween: 30,
    },
    768: {
      slidesPerView: 2,
      spaceBetween: 30,
    },
    1024: {
      slidesPerView: 3,
      spaceBetween: 40,
    },
  },
});

swiper.el.addEventListener("mouseleave", function () {
  swiper.autoplay.start();
});
