function loadCalendarEvents() {
    fetch("https://section-three.it313communityprojects.website/src/routes/calendar/events.php", {
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
      const dayIndex = (eventDate.getDay() + 6) % 7; // Monday = 0
      const hour = `${eventDate.getHours()}:00`;
      const cells = document.querySelectorAll(`.time-slot[data-day="${dayIndex}"][data-hour="${hour}"]`);
  
      cells.forEach(cell => {
        const reminder = document.createElement("div");
        reminder.className = "reminder-block";
        reminder.textContent = event.med_name;
        cell.appendChild(reminder);
      });
    });
  }
  
  function addCalendarEvent(medId, datetimeUTC) {
    const formData = new FormData();
    formData.append("med_id", medId);
    formData.append("event_datetime", datetimeUTC);
  
    fetch("https://section-three.it313communityprojects.website/src/routes/calendar/events.php", {
      method: "POST",
      credentials: "include",
      body: formData
    })
      .then(res => res.json())
      .then(data => {
        if (data.status === "success") {
          loadCalendarEvents();
        } else {
          console.error("Error adding event:", data.message);
        }
      })
      .catch(err => console.error("Add event error:", err));
  }
  
  function updateCalendarEvent(eventId, medId, datetimeUTC) {
    const formData = new URLSearchParams();
    formData.append("event_id", eventId);
    formData.append("med_id", medId);
    formData.append("event_datetime", datetimeUTC);
  
    fetch("https://section-three.it313communityprojects.website/src/routes/calendar/events.php", {
      method: "PUT",
      credentials: "include",
      body: formData
    })
      .then(res => res.json())
      .then(data => {
        if (data.status === "success") {
          loadCalendarEvents();
        } else {
          console.error("Error updating event:", data.message);
        }
      })
      .catch(err => console.error("Update event error:", err));
  }
  
  function deleteCalendarEvent(eventId) {
    const formData = new URLSearchParams();
    formData.append("event_id", eventId);
  
    fetch("https://section-three.it313communityprojects.website/src/routes/calendar/events.php", {
      method: "DELETE",
      credentials: "include",
      body: formData
    })
      .then(res => res.json())
      .then(data => {
        if (data.status === "success") {
          loadCalendarEvents();
        } else {
          console.error("Error deleting event:", data.message);
        }
      })
      .catch(err => console.error("Delete event error:", err));
  }
  
  document.addEventListener("DOMContentLoaded", () => {
    loadCalendarEvents();
  });  