function loadSensorConfig() {
    fetch("https://section-three.it313communityprojects.website/src/routes/device/config_sensor.php", {
        method: "GET",
        credentials: "include"
    })
        .then(res => res.json())
        .then(json => {
            if (json.status === "success") {
                console.log("[Sensor Config] Data received:", json);
                document.getElementById("sensor-config").style.display = "block";

                const form = document.getElementById("sensor-config-form");
                form.innerHTML = "";

                for (let i = 1; i <= 4; i++) {
                    const medId = json.slots[i]?.med_id || "";
                    const medName = json.slots[i]?.med_name || "";
                    const qty = json.slots[i]?.med_count || "";

                    form.innerHTML += `
                    <div class="info-group">
                        <label>Slot ${i}</label>
                        <select class="sensor-med" data-slot="${i}">
                            ${json.meds.map(med => `
                                <option value="${med.med_id}" ${med.med_id == medId ? 'selected' : ''}>${med.med_name}</option>
                            `).join('')}
                        </select>
                        <input type="number" class="sensor-count" data-slot="${i}" placeholder="Initial Count" value="${qty}">
                    </div>`;
                }
            } else {
                console.error("[Sensor Config] Failed:", json.message);
                alert("Failed to load sensor config: " + json.message);
            }
        })
        .catch(err => {
            console.error("Sensor config load failed:", err);
            alert("Error loading sensor configuration.");
        });
}

function submitSensorConfig() {
    const formData = new FormData();

    const sensors = document.querySelectorAll(".sensor-med");
    const counts = document.querySelectorAll(".sensor-count");

    sensors.forEach((select, i) => {
        const slot = select.dataset.slot;
        const med_id = select.value;
        const count = counts[i].value;
        formData.append(`slot_${slot}_med_id`, med_id);
        formData.append(`slot_${slot}_count`, count);
    });

    fetch("https://section-three.it313communityprojects.website/src/routes/device/submit_sensor.php", {
        method: "POST",
        body: formData,
        credentials: "include"
    })
        .then(res => res.json())
        .then(json => {
            if (json.status === "success") {
                console.log("[Sensor Config] Saved successfully.");
                alert("Sensor configuration saved.");
            } else {
                console.error("[Sensor Config] Save failed:", json.message);
                alert("Failed to save configuration: " + json.message);
            }
        })
        .catch(err => {
            console.error("Submit config failed:", err);
            alert("Error saving sensor configuration.");
        });
}
