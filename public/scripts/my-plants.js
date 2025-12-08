document.addEventListener("DOMContentLoaded", function () {
  const openBtn = document.getElementById("openAddPlant");
  const modal = document.getElementById("addPlantModal");
  const cancelBtn = document.getElementById("cancelAddPlant");

  if (!openBtn || !modal || !cancelBtn) {
    return;
  }

  const openModal = () => {
    modal.classList.remove("hidden");
    // Focus first input for nice UX
    const firstInput = modal.querySelector("input");
    if (firstInput) {
      firstInput.focus();
    }
  };

  const closeModal = () => {
    modal.classList.add("hidden");
  };

  openBtn.addEventListener("click", openModal);
  cancelBtn.addEventListener("click", closeModal);

  // Close when clicking outside the dialog content
  modal.addEventListener("click", (e) => {
    if (e.target === modal) {
      closeModal();
    }
  });

  // Close on ESC
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && !modal.classList.contains("hidden")) {
      closeModal();
    }
  });
});