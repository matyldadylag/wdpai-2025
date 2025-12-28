// Run script after the DOM is fully loaded
document.addEventListener("DOMContentLoaded", () => {
  // Find needed elements
  const cells = document.querySelectorAll(".calendar-cell[data-date]");
  const detailsDateLabel = document.getElementById("detailsDateLabel");
  const detailsContent = document.getElementById("detailsContent");

  // If calendar or details panel is missing, stop execution
  if (!cells.length || !detailsContent) return;

  // Render task details for a given date
  function renderDetails(dateKey) {
    // Set the selected date label
    detailsDateLabel.textContent = dateKey;

    // Get tasks assigned to this date
    const tasks = tasksByDate[dateKey] || [];

    // Show empty state if no tasks exist
    if (!tasks.length) {
      detailsContent.innerHTML =
        '<p class="empty-state">No tasks scheduled for this day.</p>';
      return;
    }

    // Build task list HTML
    let html = '<ul class="calendar-task-list">';

    tasks.forEach((task) => {
      // Determine color class for the task dot
      const colorClass = "task-dot-color-" + (task.color_index || 0);

      html += `
        <li class="calendar-task-list-item"
            data-plant-id="${task.plant_id}"
            data-task-id="${task.task_id}">
          
          <span class="task-dot ${colorClass}"></span>

          <div class="calendar-task-main">
            <span class="calendar-task-plant">
              ${escapeHtml(task.plant_name)}
            </span>
            <span class="calendar-task-meta">
              ${escapeHtml(task.task_name)} • ${escapeHtml(task.species_name)}
            </span>
          </div>

          <label class="task-done">
            <input type="checkbox" class="task-done-checkbox" />
            <span>Done</span>
          </label>
        </li>
      `;
    });

    html += "</ul>";

    // Inject generated HTML into the details panel
    detailsContent.innerHTML = html;
  }

  /**
   * Handle marking a task as done (event delegation)
   */
  detailsContent.addEventListener("change", async (e) => {
    const cb = e.target;

    // Only react to task "done" checkboxes
    if (!cb.classList || !cb.classList.contains("task-done-checkbox")) return;

    // Find the parent task item
    const item = cb.closest(".calendar-task-list-item");
    if (!item) return;

    // Extract identifiers from data attributes
    const plantId = Number(item.dataset.plantId);
    const taskId = Number(item.dataset.taskId);

    // Prevent double submission
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

      // Try to parse JSON response
      const data = await res.json().catch(() => null);

      // Handle server-side errors
      if (!res.ok || !data || !data.ok) {
        throw new Error((data && data.error) || "Request failed");
      }

      // Visually mark task as done
      item.classList.add("calendar-task-done");
      cb.checked = true;

      // Reload page to sync calendar state
      window.location.reload();
    } catch (err) {
      // Revert checkbox state on failure
      cb.checked = false;
      alert("Could not mark task as done. Try again.");
    } finally {
      // Re-enable checkbox
      cb.disabled = false;
    }
  });

  /**
   * Escape HTML to prevent XSS attacks
   * @param {string} str
   * @returns {string}
   */
  function escapeHtml(str) {
    if (str == null) return "";
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#39;");
  }

  /**
   * Handle clicking on calendar cells
   */
  cells.forEach((cell) => {
    cell.addEventListener("click", () => {
      const dateKey = cell.getAttribute("data-date");
      if (!dateKey) return;

      // Remove selection from all cells
      cells.forEach((c) => c.classList.remove("calendar-cell-selected"));

      // Highlight selected cell
      cell.classList.add("calendar-cell-selected");

      // Render tasks for selected date
      renderDetails(dateKey);
    });
  });

  // Render details for initially selected date
  renderDetails(selectedDate);
});

// Read calendar data injected by backend
const dataEl = document.getElementById("calendarData");

// Map of tasks grouped by date
const tasksByDate = JSON.parse(dataEl.dataset.tasks);

// Initially selected date
const selectedDate = dataEl.dataset.selectedDate;
