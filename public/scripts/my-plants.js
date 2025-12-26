document.addEventListener("DOMContentLoaded", function () {
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

  modal.addEventListener("click", (e) => {
    if (e.target === modal) {
      closeModal();
    }
  });

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && !modal.classList.contains("hidden")) {
      closeModal();
    }
  });

  const editModal = document.getElementById("editPlantModal");
  const editCancel = document.getElementById("cancelEditPlant");

  const editPlantId = document.getElementById("edit_plant_id");
  const editPlantName = document.getElementById("edit_plant_name");
  const editSpecies = document.getElementById("edit_species_id");

  const editButtons = document.querySelectorAll(".edit-plant-btn");

  if (editModal && editCancel && editButtons.length > 0) {
    const openEditModal = (btn) => {
      editPlantId.value = btn.dataset.plantId;
      editPlantName.value = btn.dataset.plantName || "";

      if (btn.dataset.speciesId) {
        editSpecies.value = btn.dataset.speciesId;
      }

      editModal.classList.remove("hidden");

      if (editPlantName) editPlantName.focus();
    };

    const closeEditModal = () => {
      editModal.classList.add("hidden");
    };

    editButtons.forEach((btn) => {
      btn.addEventListener("click", () => openEditModal(btn));
    });

    editCancel.addEventListener("click", closeEditModal);

    editModal.addEventListener("click", (e) => {
      if (e.target === editModal) closeEditModal();
    });

    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && !editModal.classList.contains("hidden")) {
        closeEditModal();
      }
    });
  }
});