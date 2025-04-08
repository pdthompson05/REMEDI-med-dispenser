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
                        // Medication list
                        const li = document.createElement("li");
                        li.innerHTML = `
                            <strong>${med.med_name}</strong> - ${med.strength} | RX: ${med.rx_number} | Qty: ${med.quantity}
                            <button class="delete-med" onclick="deleteMedication(${med.med_id}, this)">X</button>
                        `;
                        list.appendChild(li);

                        // Reminder dropdown
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

function toggleEdit() {
    const inputs = document.querySelectorAll(".profile-info input");
    const editBtn = document.querySelector(".edit-btn");
    const saveBtn = document.querySelector(".save-btn");

    inputs.forEach(input => {
        input.style.display = "block";
        input.removeAttribute("disabled");
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
                document.querySelectorAll(".profile-info input").forEach(i => {
                    i.setAttribute("disabled", "true");
                    i.style.display = "none";
                });
                document.querySelector(".save-btn").style.display = "none";
                document.querySelector(".edit-btn").style.display = "inline-block";
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
  timeInput.classList.add("specific-time");

  const removeBtn = document.createElement("button");
  removeBtn.type = "button";
  removeBtn.innerText = "✖";
  removeBtn.classList.add("delete-time-btn");
  removeBtn.onclick = () => wrapper.remove();

  wrapper.appendChild(timeInput);
  wrapper.appendChild(removeBtn);
  timeContainer.appendChild(wrapper);
}

function toggleReminderInputs() {
  const reminderType = document.getElementById("reminder-type").value;
  const timeContainer = document.getElementById("time-input-container");

  timeContainer.innerHTML = "";

  if (reminderType === "specific-time") {
    const timeInput = document.createElement("input");
    timeInput.type = "time";
    timeInput.classList.add("specific-time");

    const addTimeBtn = document.createElement("button");
    addTimeBtn.innerText = "+";
    addTimeBtn.type = "button";
    addTimeBtn.classList.add("add-time-btn");
    addTimeBtn.onclick = addTimeInput;

    timeContainer.appendChild(timeInput);
    timeContainer.appendChild(addTimeBtn);
    timeContainer.style.display = "block";
  } else if (reminderType === "interval") {
    const intervalLabel = document.createElement("label");
    intervalLabel.innerText = "Repeat Every:";

    const intervalDropdown = document.createElement("select");
    intervalDropdown.id = "interval-dropdown";

    [1, 2, 4, 6, 8, 12].forEach(hour => {
      const option = document.createElement("option");
      option.value = hour;
      option.textContent = `${hour} hours`;
      intervalDropdown.appendChild(option);
    });

    timeContainer.appendChild(intervalLabel);
    timeContainer.appendChild(intervalDropdown);
    timeContainer.style.display = "block";
  } else {
    timeContainer.style.display = "none";
  }
}