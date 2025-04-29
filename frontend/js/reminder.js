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
        const timeInputs = document.querySelectorAll(".specific");
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
        const intervalDropdown = document.getElementById("interval-dropdown");
        if (!intervalDropdown) {
            alert("Interval dropdown not found.");
            return;
        }

        const interval = intervalDropdown.value;
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
                document.getElementById("medication-select").value = "";
                document.getElementById("dosage").value = "";
                document.getElementById("reminder-type").value = "";
                document.getElementById("start-date").value = "";
                document.getElementById("end-date").value = "";
                loadProfile();
            } else {
                alert("Reminder error: " + json.message);
            }
        })
        .catch(err => {
            console.error("Reminder request failed:", err);
            alert("Failed to create reminder.");
        });
}

function toggleReminderInputs() {
    const reminderType = document.getElementById("reminder-type").value;
    const timeContainer = document.getElementById("time-input-container");

    timeContainer.innerHTML = "";

    if (reminderType === "specific") {
        const timeInput = document.createElement("input");
        timeInput.type = "time";
        timeInput.classList.add("specific");

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