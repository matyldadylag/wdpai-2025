// Run script after the DOM is fully loaded
document.addEventListener("DOMContentLoaded", () => {
  // Use event delegation to handle checkbox changes anywhere in the document
  document.body.addEventListener("change", async (e) => {
    const cb = e.target;

    // Only handle task "done" checkboxes
    if (!cb.classList || !cb.classList.contains("task-done-checkbox")) return;

    // Find the parent task list item
    const item = cb.closest(".calendar-task-list-item");
    if (!item) return;

    // Extract required identifiers from data attributes
    const plantId = Number(item.dataset.plantId);
    const taskId = Number(item.dataset.taskId);

    // If identifiers are missing or invalid, revert checkbox state
    if (!plantId || !taskId) {
      cb.checked = false;
      return;
    }

    // Disable checkbox to prevent double submission
    cb.disabled = true;

    try {
      // Send AJAX request to mark task as done
      const res = await fetch("/calendar/mark-task-done", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-Requested-With": "fetch",
        },
        body: JSON.stringify({ plant_id: plantId, task_id: taskId }),
      });

      // Attempt to parse JSON response
      const data = await res.json().catch(() => null);

      // Handle server or network errors
      if (!res.ok || !data || !data.ok) {
        throw new Error((data && data.error) || "Request failed");
      }

      // Visually mark task as completed
      item.classList.add("calendar-task-done");
      cb.checked = true;

      // Reload page to keep calendar state in sync
      window.location.reload();
    } catch (err) {
      // Revert checkbox state on failure
      cb.checked = false;
      alert("Could not mark task as done. Try again.");
    } finally {
      // Re-enable checkbox regardless of outcome
      cb.disabled = false;
    }
  });
});