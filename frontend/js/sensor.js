function loadSensorConfig() {
    fetch("/src/routes/device/slot_config.php", {
        method: "GET",
        credentials: "include"
    })
        .then(res => res.json())
        .then(json => {
            if (json.status === "success") {
                document.getElementById("sensor-config").style.display = "block";
                document.getElementById("device-name-input").value = json.device_name || "";
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
                alert("Failed to load sensor config: " + json.message);
            }
        })
        .catch(err => console.error("Sensor config load failed:", err));
}

function submitSensorConfig() {
    const formData = new FormData();
    formData.append("device_name", document.getElementById("device-name-input").value.trim());

    const sensors = document.querySelectorAll(".sensor-med");
    const counts = document.querySelectorAll(".sensor-count");

    sensors.forEach((select, i) => {
        const slot = select.dataset.slot;
        const med_id = select.value;
        const count = counts[i].value;
        formData.append(`slot_${slot}_med_id`, med_id);
        formData.append(`slot_${slot}_count`, count);
    });

    fetch("/src/routes/device/sensor.php", {
        method: "POST",
        body: formData,
        credentials: "include"
    })
        .then(res => res.json())
        .then(json => {
            if (json.status === "success") {
                alert("Sensor configuration saved.");
            } else {
                alert("Failed to save configuration: " + json.message);
            }
        })
        .catch(err => console.error("Submit config failed:", err));
}
