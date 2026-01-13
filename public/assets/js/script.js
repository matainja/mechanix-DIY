// document.addEventListener("DOMContentLoaded", function () {
//   const navbar = document.querySelector(".navbar");

//   window.addEventListener("scroll", function () {
//     if (window.scrollY > 50) {
//       navbar.style.backgroundColor = "#1a1a1a";
//       navbar.style.boxShadow = "0 2px 10px rgba(0, 0, 0, 0.5)";
//     } else {
//       navbar.style.backgroundColor = "#2a2a2a";
//       navbar.style.boxShadow = "none";
//     }
//   });

//   const navLinks = document.querySelectorAll(".nav-link");
//   navLinks.forEach((link) => {
//     link.addEventListener("click", function (e) {
//       const href = this.getAttribute("href");
//       if (href.startsWith("#")) {
//         e.preventDefault();
//         const target = document.querySelector(href);
//         if (target) {
//           target.scrollIntoView({
//             behavior: "smooth",
//             block: "start",
//           });
//         }

//         const navbarCollapse = document.querySelector(".navbar-collapse");
//         if (navbarCollapse.classList.contains("show")) {
//           const bsCollapse = new bootstrap.Collapse(navbarCollapse);
//           bsCollapse.hide();
//         }
//       }
//     });
//   });

//   const serviceItems = document.querySelectorAll(".service-item");
//   serviceItems.forEach((item) => {
//     item.addEventListener("mouseenter", function () {
//       this.style.cursor = "pointer";
//     });
//   });

//   const buttons = document.querySelectorAll(".btn");
//   buttons.forEach((button) => {
//     button.addEventListener("click", function (e) {
//       const href = this.getAttribute("href");
//       if (href && href.startsWith("#")) {
//         e.preventDefault();
//         console.log("Button clicked:", href);
//       }
//     });
//   });
// });

document.addEventListener("click", (e) => {
  const item = e.target.closest(".service-item-custom");
  if (!item) return;

  const link = item.dataset.link;
  if (link) window.location.href = link;
});
