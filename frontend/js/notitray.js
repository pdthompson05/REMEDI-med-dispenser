function toggleNotifications() {
  const dropdown = document.getElementById("notificationDropdown");
  dropdown.classList.toggle("show");

  const notificationList = document.getElementById("notificationList");
  if (!notificationList) return;

  notificationList.innerHTML = "<p>Loading...</p>";

  fetch("https://section-three.it313communityprojects.website/src/routes/reminder/upcoming.php", {
    method: "GET",
    credentials: "include"
  })
    .then(res => res.json())
    .then(json => {
      if (json.status === "success") {
        const items = json.data;
        notificationList.innerHTML = "";

        if (items.length === 0) {
          notificationList.innerHTML = "<div class='notif-item'>No upcoming reminders.</div>";
        } else {
          items.forEach(item => {
            const time = new Date(item.event_datetime).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            notificationList.innerHTML += `<div class='notif-item'><strong>${item.med_name}</strong> at ${time}</div>`;
          });
        }
      } else {
        notificationList.innerHTML = "<div class='notif-item'>Failed to load reminders.</div>";
      }
    })
    .catch(err => {
      console.error("Notification fetch failed:", err);
      notificationList.innerHTML = "<div class='notif-item'>Error loading reminders.</div>";
    });
}
