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