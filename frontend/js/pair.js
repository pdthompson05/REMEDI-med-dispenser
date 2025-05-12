
// Pairs a device with the user
function pairDevice() {
  const deviceId = document.getElementById("device-id-input").value.trim();
  if (!deviceId) {
    alert("Please enter a device ID.");
    return;
  }

  const formData = new FormData();
  formData.append("device_id", deviceId);

  fetch("https://section-three.it313communityprojects.website/src/routes/device/pair.php", {
    method: "POST",
    body: formData,
    credentials: "include"
  })
    .then(res => res.json())
    .then(json => {
      if (json.status === "success") {
        alert("Device successfully paired!");
        document.getElementById("sensor-config").style.display = "block";
        loadSensorConfig(deviceId);
      } else {
        alert("Pairing failed: " + json.message);
      }
    })
    .catch(err => {
      console.error("Pairing error:", err);
      alert("Failed to pair device.");
    });
}

function loadSensorConfig(deviceId) {
  fetch("https://section-three.it313communityprojects.website/src/routes/med/get.php", {
    method: "GET",
    credentials: "include"
  })
    .then(res => res.json())
    .then(json => {
      if (json.status === "success") {
        const meds = json.data;
        const configContainer = document.getElementById("sensor-config");
        configContainer.innerHTML = "<h3>Configure Sensor Slots</h3>";

        for (let i = 1; i <= 4; i++) {
          const wrapper = document.createElement("div");
          wrapper.className = "sensor-slot";

          const label = document.createElement("label");
          label.textContent = "Slot " + i;

          const select = document.createElement("select");
          select.name = "sensor_slot_" + i;
          select.dataset.slot = i;

          meds.forEach(med => {
            const option = document.createElement("option");
            option.value = med.med_id;
            option.textContent = med.med_name;
            select.appendChild(option);
          });

          const countInput = document.createElement("input");
          countInput.type = "number";
          countInput.placeholder = "Initial Count";
          countInput.name = "sensor_count_" + i;

          wrapper.appendChild(label);
          wrapper.appendChild(select);
          wrapper.appendChild(countInput);
          configContainer.appendChild(wrapper);
        }

        const saveBtn = document.createElement("button");
        saveBtn.textContent = "Save Sensor Configuration";
        saveBtn.onclick = () => saveSensorConfig(deviceId);
        configContainer.appendChild(saveBtn);
      } else {
        alert("Failed to load medications.");
      }
    })
    .catch(err => {
      console.error("Medications fetch error:", err);
    });
}

function saveSensorConfig(deviceId) {
  const formData = new FormData();
  formData.append("device_id", deviceId);

  for (let i = 1; i <= 4; i++) {
    const medId = document.querySelector(`select[name="sensor_slot_${i}"]`).value;
    const count = document.querySelector(`input[name="sensor_count_${i}"]`).value;
    formData.append(`slot_${i}_med`, medId);
    formData.append(`slot_${i}_count`, count);
  }

  fetch("https://section-three.it313communityprojects.website/src/routes/device/configure_sensors.php", {
    method: "POST",
    body: formData,
    credentials: "include"
  })
    .then(res => res.json())
    .then(json => {
      if (json.status === "success") {
        alert("Sensor configuration saved!");
      } else {
        alert("Failed to save configuration: " + json.message);
      }
    })
    .catch(err => {
      console.error("Sensor config error:", err);
      alert("Failed to save sensor config.");
    });
}
