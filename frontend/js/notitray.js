function toggleNotifications() {
  const dropdown = document.getElementById("notificationDropdown");
  dropdown.classList.toggle("show");

  fetch("https://section-three.it313communityprojects.website/src/routes/reminder/upcoming.php", {
    method: "GET",
    credentials: "include"
  })
    .then(res => res.json())
    .then(json => {
      if (json.status === "success") {
        const items = json.data;
        dropdown.innerHTML = "<p>Notifications</p>";
        if (items.length === 0) {
          dropdown.innerHTML += "<div class='notif-item'>No upcoming reminders.</div>";
        } else {
          items.forEach(item => {
            const time = new Date(item.event_datetime).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            dropdown.innerHTML += `<div class='notif-item'><strong>${item.med_name}</strong> at ${time}</div>`;
          });
        }
      }
    })
    .catch(err => {
      console.error("Notification fetch failed:", err);
    });
}
