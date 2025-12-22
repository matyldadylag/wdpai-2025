// TODO Add comments
document.addEventListener("DOMContentLoaded", () => {
  document.body.addEventListener("change", async (e) => {
    const cb = e.target;
    if (!cb.classList || !cb.classList.contains("task-done-checkbox")) return;

    const item = cb.closest(".calendar-task-list-item");
    if (!item) return;

    const plantId = Number(item.dataset.plantId);
    const taskId = Number(item.dataset.taskId);

    if (!plantId || !taskId) {
      cb.checked = false;
      return;
    }

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
});
