// TODO Add comments
document.addEventListener("DOMContentLoaded", () => {
  const cells = document.querySelectorAll(".calendar-cell[data-date]");
  const detailsDateLabel = document.getElementById("detailsDateLabel");
  const detailsContent = document.getElementById("detailsContent");

  if (!cells.length || !detailsContent) return;

  function renderDetails(dateKey) {
    detailsDateLabel.textContent = dateKey;

    const tasks = tasksByDate[dateKey] || [];

    if (!tasks.length) {
      detailsContent.innerHTML =
        '<p class="empty-state">No tasks scheduled for this day.</p>';
      return;
    }

    let html = '<ul class="calendar-task-list">';
    tasks.forEach((task) => {
      const colorClass = "task-dot-color-" + (task.color_index || 0);

      html += `
        <li class="calendar-task-list-item" data-plant-id="${task.plant_id}" data-task-id="${task.task_id}">
          <span class="task-dot ${colorClass}"></span>

          <div class="calendar-task-main">
            <span class="calendar-task-plant">${escapeHtml(task.plant_name)}</span>
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

    detailsContent.innerHTML = html;
  }

  detailsContent.addEventListener("change", async (e) => {
    const cb = e.target;
    if (!cb.classList || !cb.classList.contains("task-done-checkbox")) return;

    const item = cb.closest(".calendar-task-list-item");
    if (!item) return;

    const plantId = Number(item.dataset.plantId);
    const taskId = Number(item.dataset.taskId);

    cb.disabled = true;

    try {
      const res = await fetch("/calendar/mark-task-done", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-Requested-With": "fetch",
        },
        body: JSON.stringify({ plant_id: plantId, task_id: taskId }),
      });

      const data = await res.json().catch(() => null);

      if (!res.ok || !data || !data.ok) {
        throw new Error((data && data.error) || "Request failed");
      }

      item.classList.add("calendar-task-done");
      cb.checked = true;
      window.location.reload();
    } catch (err) {
      cb.checked = false;
      alert("Could not mark task as done. Try again.");
    } finally {
      cb.disabled = false;
    }
  });

  function escapeHtml(str) {
    if (str == null) return "";
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#39;");
  }

  cells.forEach((cell) => {
    cell.addEventListener("click", () => {
      const dateKey = cell.getAttribute("data-date");
      if (!dateKey) return;

      cells.forEach((c) => c.classList.remove("calendar-cell-selected"));
      cell.classList.add("calendar-cell-selected");

      renderDetails(dateKey);
    });
  });

  renderDetails(selectedDate);
});

const dataEl = document.getElementById("calendarData");
const tasksByDate = JSON.parse(dataEl.dataset.tasks);
const selectedDate = dataEl.dataset.selectedDate;