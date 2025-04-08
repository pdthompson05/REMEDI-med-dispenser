const CALENDAR_API = "https://section-three.it313communityprojects.website/src/routes/calendar/events.php";

function loadCalendarEvents() {
  fetch(CALENDAR_API, {
    method: "GET",
    credentials: "include"
  })
    .then(res => res.json())
    .then(data => {
      if (data.status === "success") {
        renderCalendarEvents(data.data);
      } else {
        console.error("Error loading events:", data.message);
      }
    })
    .catch(err => console.error("Calendar fetch error:", err));
}

function renderCalendarEvents(events) {
  document.querySelectorAll(".reminder-block").forEach(el => el.remove());

  events.forEach(event => {
    const eventDate = new Date(event.event_datetime);
    const hourLabel = `${eventDate.getHours()}:00`;
    const dayIndex = (eventDate.getDay() + 6) % 7; // Convert Sunday=0 to index 6

    const targetCells = document.querySelectorAll(
      `.time-slot[data-day="${dayIndex}"][data-hour="${hourLabel}"]`
    );

    targetCells.forEach(cell => {
      const block = document.createElement("div");
      block.className = "reminder-block";
      block.textContent = event.med_name;
      cell.appendChild(block);
    });
  });
}

document.addEventListener("DOMContentLoaded", () => {
  loadCalendarEvents();
});