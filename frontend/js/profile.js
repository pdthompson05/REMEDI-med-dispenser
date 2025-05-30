document.addEventListener("DOMContentLoaded", loadProfile);

function loadProfile() {
    // Load profile
    fetch('https://section-three.it313communityprojects.website/src/routes/profile/load.php', {
            method: "GET",
            credentials: "include"
        })
        .then(async response => {
            const raw = await response.text();
            try {
                const jsonData = JSON.parse(raw);
                if (jsonData.status === "success") {
                    const data = jsonData.data;
                    document.getElementById("first-name-input").value = data.first_name;
                    document.getElementById("last-name-input").value = data.last_name;
                    document.getElementById("dob-input").value = data.date_of_birth;
                    document.getElementById("contact-input").value = data.email;
                    document.getElementById("caretaker-name-input").value = data.caretaker_name || "";
                    document.getElementById("caretaker-contact-input").value = data.caretaker_email || "";

                    document.getElementById("profile-name").innerText = `${data.first_name} ${data.last_name}`;
                    document.getElementById("profile-email").innerText = data.email;

                    disableProfileEditing();
                } else {
                    window.location.href = "login.html";
                }
            } catch (err) {
                console.error("Invalid JSON from load.php:", raw);
                window.location.href = "login.html";
            }
        })
        .catch(error => {
            console.error("Profile fetch error:", error);
            window.location.href = "login.html";
        });

    // Load medications
    fetch("https://section-three.it313communityprojects.website/src/routes/med/get.php", {
            method: "GET",
            credentials: "include"
        })
        .then(async res => {
            const raw = await res.text();
            try {
                const json = JSON.parse(raw);
                if (json.status === "success") {
                    const list = document.getElementById("medication-list");
                    list.innerHTML = "";

                    const dropdown = document.getElementById("medication-select");
                    dropdown.innerHTML = '<option value="" disabled selected>Select Medication</option>';

                    json.data.forEach(med => {
                        const li = document.createElement("li");
                        li.innerHTML = `
              <strong>${med.med_name}</strong> - ${med.strength} | RX: ${med.rx_number} | Qty: ${med.quantity}
              <button class="delete-med" onclick="deleteMedication(${med.med_id}, this)">X</button>
            `;
                        list.appendChild(li);

                        const option = document.createElement("option");
                        option.value = med.med_id;
                        option.textContent = med.med_name;
                        dropdown.appendChild(option);
                    });
                } else {
                    console.warn("Could not load medications:", json.message);
                }
            } catch (err) {
                console.error("JSON parse error from get.php:", raw);
            }
        })
        .catch(err => {
            console.error("Error loading medications:", err);
        });
}

function loadDeviceStatus() {
    fetch("https://section-three.it313communityprojects.website/src/routes/device/frontend/fetch_status.php", {
        method: "GET",
        credentials: "include"
    })
    .then(res => res.json())
    .then(json => {
        const statusEl = document.getElementById("device-status");
        const unpairBtn = document.getElementById("unpair-button");

        if (json.status === "success" && json.device) {
            const d = json.device;
            statusEl.innerText = `Device ${d.device_id}: ${d.connected ? "Connected" : "Disconnected"}, Temp: ${d.temperature ?? "N/A"}°C`;
            if (unpairBtn) unpairBtn.style.display = "inline-block";
            loadSensorConfig(d.device_id);
        } else {
            statusEl.innerText = "No device found.";
            if (unpairBtn) unpairBtn.style.display = "none";
        }
    })
    .catch(err => {
        console.error("Status fetch failed:", err);
        document.getElementById("device-status").innerText = "Device status error.";
    });
}
window.addEventListener("DOMContentLoaded", loadDeviceStatus);


function disableProfileEditing() {
    const inputs = document.querySelectorAll(".profile-info input");
    const editBtn = document.querySelector(".edit-btn");
    const saveBtn = document.querySelector(".save-btn");

    inputs.forEach(input => {
        input.disabled = true;
    });

    editBtn.style.display = "inline-block";
    saveBtn.style.display = "none";
}

function toggleEdit() {
    const inputs = document.querySelectorAll(".profile-info input");
    const editBtn = document.querySelector(".edit-btn");
    const saveBtn = document.querySelector(".save-btn");

    inputs.forEach(input => {
        input.disabled = false;
    });

    editBtn.style.display = "none";
    saveBtn.style.display = "inline-block";
}

function saveProfile() {
    const formData = new FormData();
    formData.append("first_name", document.getElementById("first-name-input").value);
    formData.append("last_name", document.getElementById("last-name-input").value);
    formData.append("date_of_birth", document.getElementById("dob-input").value);
    formData.append("email", document.getElementById("contact-input").value);
    formData.append("caretaker_name", document.getElementById("caretaker-name-input").value);
    formData.append("caretaker_email", document.getElementById("caretaker-contact-input").value);

    fetch('https://section-three.it313communityprojects.website/src/routes/profile/update.php', {
            method: "POST",
            body: formData,
            credentials: "include"
        })
        .then(res => res.json())
        .then(json => {
            alert(json.message);
            if (json.status === "success") {
                loadProfile();
            }
        })
        .catch(err => {
            console.error("Profile update error:", err);
        });
}

function addTimeInput() {
    const timeContainer = document.getElementById("time-input-container");

    const wrapper = document.createElement("div");
    wrapper.classList.add("time-input-wrapper");

    const timeInput = document.createElement("input");
    timeInput.type = "time";
    timeInput.classList.add("specific");
    timeInput.name = "times[]";

    const removeBtn = document.createElement("button");
    removeBtn.type = "button";
    removeBtn.innerText = "✖";
    removeBtn.classList.add("delete-time-btn");
    removeBtn.onclick = () => wrapper.remove();

    wrapper.appendChild(timeInput);
    wrapper.appendChild(removeBtn);
    timeContainer.appendChild(wrapper);
}

function logout() {
    fetch("https://section-three.it313communityprojects.website/src/auth/user/logout.php", {
      method: "POST",
      credentials: "include"
    })
      .then(res => res.json())
      .then(json => {
        if (json.status === "success") {
          alert("Logged out successfully.");
          window.location.href = "/frontend/html/login.html";
          alert("Logout failed.");
        }
      })
      .catch(err => {
        console.error("Logout error:", err);
        alert("Something went wrong.");
      });
  }
  