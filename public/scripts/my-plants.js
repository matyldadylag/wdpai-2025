document.addEventListener("DOMContentLoaded", function () {
  // Add plant modal
  const openBtn = document.getElementById("openAddPlant");
  const modal = document.getElementById("addPlantModal");
  const cancelBtn = document.getElementById("cancelAddPlant");

  if (!openBtn || !modal || !cancelBtn) {
    return;
  }

  const openModal = () => {
    modal.classList.remove("hidden");
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

  // Edit plant modal
  const editModal = document.getElementById("editPlantModal");
  const editCancel = document.getElementById("cancelEditPlant");

  const editPlantId = document.getElementById("edit_plant_id");
  const editPlantName = document.getElementById("edit_plant_name");
  const editSpecies = document.getElementById("edit_species_id");

  const editButtons = document.querySelectorAll(".edit-plant-btn");

  if (editModal && editCancel && editButtons.length > 0) {
    const openEditModal = (btn) => {
      // Populate form fields from data attributes
      editPlantId.value = btn.dataset.plantId;
      editPlantName.value = btn.dataset.plantName || "";

      if (btn.dataset.speciesId) {
        editSpecies.value = btn.dataset.speciesId;
      }

      // Show modal
      editModal.classList.remove("hidden");

      // Autofocus first input
      if (editPlantName) editPlantName.focus();
    };

    const closeEditModal = () => {
      editModal.classList.add("hidden");
    };

    // Open modal on clicking "Edit"
    editButtons.forEach((btn) => {
      btn.addEventListener("click", () => openEditModal(btn));
    });

    // Close on cancel button
    editCancel.addEventListener("click", closeEditModal);

    // Close when clicking backdrop
    editModal.addEventListener("click", (e) => {
      if (e.target === editModal) closeEditModal();
    });

    // Close on ESC key
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && !editModal.classList.contains("hidden")) {
        closeEditModal();
      }
    });
  }
});