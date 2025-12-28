// Run script after the DOM is fully loaded
document.addEventListener("DOMContentLoaded", function () {
  // Find needed elements
  const openBtn = document.getElementById("openAddPlant");
  const modal = document.getElementById("addPlantModal");
  const cancelBtn = document.getElementById("cancelAddPlant");

  // If required elements are missing, stop execution
  if (!openBtn || !modal || !cancelBtn) {
    return;
  }

  /**
   * Open the "Add Plant" modal and focus the first input
   */
  const openModal = () => {
    modal.classList.remove("hidden");

    // Focus the first input inside the modal for better UX
    const firstInput = modal.querySelector("input");
    if (firstInput) {
      firstInput.focus();
    }
  };

  /**
   * Close the "Add Plant" modal
   */
  const closeModal = () => {
    modal.classList.add("hidden");
  };

  // Open modal when clicking the "Add Plant" button
  openBtn.addEventListener("click", openModal);

  // Close modal when clicking the cancel button
  cancelBtn.addEventListener("click", closeModal);

  // Close modal when clicking on the backdrop
  modal.addEventListener("click", (e) => {
    if (e.target === modal) {
      closeModal();
    }
  });

  // Close modal when pressing Escape
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && !modal.classList.contains("hidden")) {
      closeModal();
    }
  });

  /* ======================================================
     Edit Plant modal logic
     ====================================================== */

  // Elements related to the "Edit Plant" modal
  const editModal = document.getElementById("editPlantModal");
  const editCancel = document.getElementById("cancelEditPlant");

  // Edit form inputs
  const editPlantId = document.getElementById("edit_plant_id");
  const editPlantName = document.getElementById("edit_plant_name");
  const editSpecies = document.getElementById("edit_species_id");

  // Buttons that open the edit modal
  const editButtons = document.querySelectorAll(".edit-plant-btn");

  // Only initialize edit modal logic if required elements exist
  if (editModal && editCancel && editButtons.length > 0) {
    /**
     * Open the "Edit Plant" modal and prefill form fields
     * @param {HTMLElement} btn - Button that triggered the modal
     */
    const openEditModal = (btn) => {
      // Populate form fields using data attributes
      editPlantId.value = btn.dataset.plantId;
      editPlantName.value = btn.dataset.plantName || "";

      if (btn.dataset.speciesId) {
        editSpecies.value = btn.dataset.speciesId;
      }

      // Show modal
      editModal.classList.remove("hidden");

      // Focus plant name input for faster editing
      if (editPlantName) editPlantName.focus();
    };

    /**
     * Close the "Edit Plant" modal
     */
    const closeEditModal = () => {
      editModal.classList.add("hidden");
    };

    // Open edit modal when clicking any edit button
    editButtons.forEach((btn) => {
      btn.addEventListener("click", () => openEditModal(btn));
    });

    // Close edit modal when clicking cancel
    editCancel.addEventListener("click", closeEditModal);

    // Close edit modal when clicking on the backdrop
    editModal.addEventListener("click", (e) => {
      if (e.target === editModal) closeEditModal();
    });

    // Close edit modal when pressing Escape
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && !editModal.classList.contains("hidden")) {
        closeEditModal();
      }
    });
  }
});
