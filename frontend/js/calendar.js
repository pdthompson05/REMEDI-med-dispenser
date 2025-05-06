const CALENDAR_API = "https://section-three.it313communityprojects.website/src/routes/calendar/render.php";

const weekDaysEl = document.getElementById("week-days");
const calendarBody = document.getElementById("calendar-body");
const reminderListEl = document.getElementById("reminderList");
const reminderDateLabel = document.getElementById("reminderDateLabel");
const reminderSide = document.getElementById("reminderSide");
const monthDisplay = document.getElementById("monthDisplay");

let currentView = 'week';
let selectedDate = null;
let reminders = {}; // use let not const so we can reassign

function switchView(view) {
    currentView = view;
    if (view === 'month') {
        const now = new Date();
        fetchMonthlyEvents(now.getFullYear(), now.getMonth() + 1);
    } else {
        renderCalendar(); // weekly
    }
}

function renderCalendar() {
    const wrapper = document.getElementById("calendarWrapper");

    if (currentView === 'week') {
        reminderSide.classList.add("hidden");
        wrapper.classList.remove("monthly-view");
        wrapper.classList.add("weekly-view");
        monthDisplay.classList.add("hidden");
        renderWeeklyView();
        loadCalendarEvents();
    } else {
        reminderSide.classList.remove("hidden");
        wrapper.classList.remove("weekly-view");
        wrapper.classList.add("monthly-view");
        monthDisplay.classList.remove("hidden");
        renderMonthlyView();
    }
}

function renderWeeklyView() {
    weekDaysEl.innerHTML = `<div></div>`;
    calendarBody.innerHTML = "";

    const hours = Array.from({
        length: 12
    }, (_, i) => `${8 + i}:00`);
    const weekStart = getMonday(new Date());
    const dayNames = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

    for (let i = 0; i < 7; i++) {
        const date = new Date(weekStart);
        date.setDate(date.getDate() + i);
        const label = `${dayNames[i]}<br>${date.getMonth() + 1}/${date.getDate()}`;
        weekDaysEl.innerHTML += `<div>${label}</div>`;
    }

    hours.forEach(hour => {
        calendarBody.innerHTML += `<div class="time-slot time-label">${hour}</div>`;
        for (let i = 0; i < 7; i++) {
            calendarBody.innerHTML += `<div class="time-slot day-column" data-day="${i}" data-hour="${hour}"></div>`;
        }
    });
}

function renderMonthlyView() {
    const now = new Date();
    const year = now.getFullYear();
    const month = now.getMonth();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const firstDay = new Date(year, month, 1).getDay();
    const offset = (firstDay + 6) % 7;

    const monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];

    monthDisplay.textContent = `${monthNames[month]} ${year}`;
    weekDaysEl.innerHTML = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
        .map(day => `<div>${day}</div>`).join('');

    calendarBody.innerHTML = "";

    const totalCells = offset + daysInMonth;
    const totalGrid = Math.ceil(totalCells / 7) * 7;

    for (let i = 0; i < totalGrid; i++) {
        if (i < offset || i >= offset + daysInMonth) {
            calendarBody.innerHTML += `<div class="time-slot day-column"></div>`;
        } else {
            const day = i - offset + 1;
            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const isSelected = selectedDate === dateStr;
            const hasReminder = reminders[dateStr] && reminders[dateStr].length > 0;
            const reminderDot = hasReminder ? `<img src="pill2.png" alt="REMEDI Pill" class="reminder-dot">` : "";
            const dayNumber = isSelected ?
                `<span class="selected-day">${day}</span>${reminderDot}` :
                `<span class="day-number">${day}</span>${reminderDot}`;

            calendarBody.innerHTML += `
        <div class="time-slot day-column" onclick="selectDay('${dateStr}')">
          ${dayNumber}
        </div>`;
        }
    }

    if (!selectedDate) {
        selectedDate = `${year}-${String(month + 1).padStart(2, '0')}-01`;
    }

    updateReminderPanel(selectedDate);
}

function selectDay(dateStr) {
    selectedDate = dateStr;
    renderCalendar();
}

function updateReminderPanel(dateStr) {
    const [year, month, day] = dateStr.split('-').map(Number);
    const date = new Date(year, month - 1, day);
    const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };
    reminderDateLabel.textContent = date.toLocaleDateString(undefined, options);

    reminderListEl.innerHTML = "";

    if (reminders[dateStr]) {
        reminders[dateStr].forEach(reminder => {
            const li = document.createElement("li");
            li.textContent = reminder;
            reminderListEl.appendChild(li);
        });
    } else {
        reminderListEl.innerHTML = "<li>No reminders.</li>";
    }
}

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

function fetchMonthlyEvents(year, month) {
    const startDate = `${year}-${String(month).padStart(2, '0')}-01`;
    const endDate = new Date(year, month, 0).toISOString().split('T')[0];

    fetch(`${CALENDAR_API}?start_date=${startDate}&end_date=${endDate}`, {
            method: "GET",
            credentials: "include"
        })
        .then(res => res.json())
        .then(json => {
            if (json.status === "success") {
                const events = json.data;
                reminders = {};

                events.forEach(ev => {
                    const date = ev.event_datetime.split("T")[0];
                    if (!reminders[date]) reminders[date] = [];
                    const time = new Date(ev.event_datetime).toLocaleTimeString([], {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    reminders[date].push(`${ev.med_name} at ${time}`);
                });

                renderCalendar(); // re-render with updated reminders
            }
        })
        .catch(err => console.error("Failed to fetch monthly events:", err));
}

function deleteCalendarEvent(eventId) {
    const formData = new URLSearchParams();
    formData.append("event_id", eventId);

    fetch(CALENDAR_API, {
            method: "DELETE",
            credentials: "include",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === "success") {
                renderCalendar();
            } else {
                console.error("Failed to delete event:", data.message);
            }
        })
        .catch(err => console.error("Delete request failed:", err));
}

function getMonday(date) {
    const day = date.getDay();
    const diff = date.getDate() - day + (day === 0 ? -6 : 1);
    return new Date(date.setDate(diff));
}

function toggleNotifications() {
    const dropdown = document.getElementById("notificationDropdown");
    dropdown.classList.toggle("show");
}

window.onload = () => {
    renderCalendar();
};