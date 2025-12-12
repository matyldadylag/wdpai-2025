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
        <li class="calendar-task-list-item">
          <span class="task-dot ${colorClass}"></span>
          <div class="calendar-task-main">
            <span class="calendar-task-plant">${escapeHtml(
              task.plant_name
            )}</span>
            <span class="calendar-task-meta">
              ${escapeHtml(task.task_name)} • ${escapeHtml(task.species_name)}
            </span>
          </div>
        </li>
      `;
    });
    html += "</ul>";

    detailsContent.innerHTML = html;
  }

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