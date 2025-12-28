// Run script after the page is fully loaded
document.addEventListener("DOMContentLoaded", () => {
  // Find needed elements
  const header = document.querySelector(".top-nav");
  const toggleBtn = document.querySelector(".nav-toggle");
  const nav = document.querySelector(".nav-right");

  // If the page doesn't have a navbar, do nothing
  if (!header || !toggleBtn || !nav) return;

  // Toggle mobile menu
  toggleBtn.addEventListener("click", (e) => {
    e.stopPropagation();

    const isOpen = header.classList.toggle("nav-open");
    toggleBtn.setAttribute("aria-expanded", String(isOpen));
  });

  // Close menu when clicking a link
  nav.addEventListener("click", (e) => {
    const link = e.target.closest("a");
    if (!link) return;

    header.classList.remove("nav-open");
    toggleBtn.setAttribute("aria-expanded", "false");
  });

  // Close menu when clicking outside
  document.addEventListener("click", (e) => {
    if (!header.contains(e.target)) {
      header.classList.remove("nav-open");
      toggleBtn.setAttribute("aria-expanded", "false");
    }
  });

  // Close menu on resize to desktop
  window.addEventListener("resize", () => {
    if (window.innerWidth > 768) {
      header.classList.remove("nav-open");
      toggleBtn.setAttribute("aria-expanded", "false");
    }
  });
});
