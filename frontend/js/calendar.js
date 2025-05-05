const CALENDAR_API = "https://section-three.it313communityprojects.website/src/routes/calendar/render.php";

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
        const dayIndex = (eventDate.getDay() + 6) % 7;
        const hour = `${eventDate.getHours()}:00`;
        const cells = document.querySelectorAll(`.time-slot[data-day="${dayIndex}"][data-hour="${hour}"]`);
        cells.forEach(cell => {
            const reminder = document.createElement("div");
            reminder.className = "reminder-block";
            reminder.textContent = event.med_name;

            const deleteBtn = document.createElement("button");
            deleteBtn.innerText = "✖";
            deleteBtn.classList.add("delete-event-btn");
            deleteBtn.onclick = () => {
                if (confirm(`Delete reminder for ${event.med_name}?`)) {
                    deleteCalendarEvent(event.event_id);
                }
            };

            reminder.appendChild(deleteBtn);
            cell.appendChild(reminder);
        });
    });
}

function deleteCalendarEvent(eventId) {
    const formData = new URLSearchParams();
    formData.append("event_id", eventId);

    fetch("https://section-three.it313communityprojects.website/src/routes/calendar/render.php", {
            method: "DELETE",
            credentials: "include",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === "success") {
                loadCalendarEvents();
            } else {
                console.error("Failed to delete event:", data.message);
            }
        })
        .catch(err => console.error("Delete request failed:", err));
}

function fetchMonthlyEvents(year, month) {
    const startDate = `${year}-${String(month).padStart(2, '0')}-01`;
    const endDate = new Date(year, month, 0).toISOString().split('T')[0]; // last day of month
  
    fetch(`${CALENDAR_API}?start_date=${startDate}&end_date=${endDate}`, {
      method: "GET",
      credentials: "include"
    })
      .then(res => res.json())
      .then(json => {
        if (json.status === "success") {
          const events = json.data;
          reminders = {}; // Reset
  
          events.forEach(ev => {
            const date = ev.event_datetime.split("T")[0];
            if (!reminders[date]) reminders[date] = [];
            reminders[date].push(`${ev.med_name} at ${new Date(ev.event_datetime).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`);
          });
  
          renderCalendar(); // re-render with new reminders
        }
      })
      .catch(err => console.error("Failed to fetch monthly events:", err));
  }  

document.addEventListener("DOMContentLoaded", () => {
    loadCalendarEvents();
});

