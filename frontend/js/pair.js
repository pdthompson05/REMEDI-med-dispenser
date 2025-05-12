function pairDevice() {
    const deviceId = document.getElementById("device-id").value.trim();
    if (!deviceId) {
        alert("Please enter a device ID.");
        return;
    }

    const formData = new FormData();
    formData.append("device_id", deviceId);

    fetch("/src/routes/device/pair.php", {
            method: "POST",
            body: formData,
            credentials: "include"
        })
        .then(res => res.json())
        .then(json => {
            if (json.status === "success") {
                alert("Device successfully paired!");
                loadSensorConfig(); // Load config UI
                location.reload();
            } else {
                alert("Pairing failed: " + json.message);
            }
        })
        .catch(err => {
            console.error("Pairing error:", err);
            alert("Failed to pair device.");
        });
}

function unpairDevice() {
    fetch("/src/routes/device/unpair.php", {
        method: "POST",
        credentials: "include"
    })
    .then(res => res.json())
    .then(json => {
        if (json.status === "success") {
            alert("Device unpaired successfully.");
            document.getElementById("sensor-config").style.display = "none";
            document.getElementById("device-status").textContent = "No device paired.";
            document.getElementById("device-id").value = ""; // optional if you still have the input
            window.currentDeviceId = null;
        } else {
            alert("Unpair failed: " + json.message);
        }
    })
    .catch(err => {
        console.error("Unpairing error:", err);
        alert("Failed to unpair device.");
    });
}
