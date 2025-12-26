document.addEventListener("DOMContentLoaded", () => {
  document.body.addEventListener("submit", (e) => {
    const form = e.target;
    if (!form.classList || !form.classList.contains("admin-delete-form")) return;

    const ok = window.confirm("Are you sure you want to delete this user?");
    if (!ok) {
      e.preventDefault();
    }
  });
});
