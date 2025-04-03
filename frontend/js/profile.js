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

function toggleMedForm() {
    const form = document.getElementById("med-form");
    form.style.display = form.style.display === "none" ? "block" : "none";
}

function addMedication() {
    const medName = document.getElementById("med-name").value.trim();
    const strength = document.getElementById("med-strength").value.trim();
    const rxNumber = document.getElementById("rx-number").value.trim();
    const quantity = parseInt(document.getElementById("quantity").value.trim());

    if (!medName || !strength || !rxNumber || isNaN(quantity) || quantity <= 0) {
        alert("Please fill in all medication fields correctly.");
        return;
    }

    const formData = new FormData();
    formData.append("med_name", medName);
    formData.append("strength", strength);
    formData.append("rx_number", rxNumber);
    formData.append("quantity", quantity);

    fetch("https://section-three.it313communityprojects.website/src/routes/med/add.php", {
        method: "POST",
        body: formData,
        credentials: "include"
    })
        .then(async res => {
            const raw = await res.text();
            try {
                const json = JSON.parse(raw);
                if (json.status === "success") {
                    alert("Medication added!");
                    // Clear inputs
                    document.getElementById("med-name").value = "";
                    document.getElementById("med-strength").value = "";
                    document.getElementById("rx-number").value = "";
                    document.getElementById("quantity").value = "";
                    toggleMedForm();
                    loadProfile(); // reload to fetch new med_id
                } else {
                    alert("Error: " + json.message);
                }
            } catch (err) {
                console.error("JSON parse failed:", err, "Raw:", raw);
                alert("Error adding medication. Check console.");
            }
        })
        .catch(error => {
            console.error("Add medication failed:", error);
            alert("Add medication failed. See console.");
        });
}

function deleteMedication(medId, buttonElement) {
    const formData = new FormData();
    formData.append("med_id", medId);

    fetch("https://section-three.it313communityprojects.website/src/routes/med/delete.php", {
        method: "POST",
        body: formData,
        credentials: "include"
    })
        .then(async res => {
            const raw = await res.text();
            try {
                const json = JSON.parse(raw);
                if (json.status === "success") {
                    // Remove <li> from UI
                    buttonElement.parentElement.remove();
                } else {
                    alert("Failed to delete: " + json.message);
                }
            } catch (err) {
                console.error("Failed to parse delete response:", raw);
                alert("Error deleting medication. Check console.");
            }
        })
        .catch(err => {
            console.error("Delete request failed:", err);
            alert("Error contacting server.");
        });
}

function addReminder() {
    const medId = document.getElementById("medication-select").value;
    const dosage = document.getElementById("dosage").value.trim();
    const type = document.getElementById("reminder-type").value;
    const startDate = document.getElementById("start-date").value;
    const endDate = document.getElementById("end-date").value;

    if (!medId || !type || !startDate || !endDate) {
        alert("Please fill in all required reminder fields.");
        return;
    }

    const formData = new FormData();
    formData.append("med_id", medId);
    formData.append("dosage", dosage);
    formData.append("reminder_type", type);
    formData.append("start_date", startDate);
    formData.append("end_date", endDate);

    if (type === "specific") {
        const timeInputs = document.querySelectorAll(".specific-time");
        const times = [];

        timeInputs.forEach(input => {
            if (input.value) times.push(input.value);
        });

        if (times.length === 0) {
            alert("Please enter at least one reminder time.");
            return;
        }

        times.forEach(time => formData.append("times[]", time));
    } else if (type === "interval") {
        const interval = document.getElementById("interval-dropdown").value;
        if (!interval) {
            alert("Please select an interval.");
            return;
        }
        formData.append("interval_hours", interval);
    }

    fetch("https://section-three.it313communityprojects.website/src/routes/reminder/add.php", {
        method: "POST",
        body: formData,
        credentials: "include"
    })
        .then(res => res.json())
        .then(json => {
            if (json.status === "success") {
                alert("Reminder set!");
                loadProfile(); // optionally reload reminders
            } else {
                alert("Reminder error: " + json.message);
            }
        })
        .catch(err => {
            console.error("Reminder request failed:", err);
            alert("Failed to create reminder.");
        });
}