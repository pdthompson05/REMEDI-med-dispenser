function loadDeviceStatus() {
    fetch("https://section-three.it313communityprojects.website/src/routes/device/status.php", {
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

                // Show unpair button
                if (unpairBtn) unpairBtn.style.display = "inline-block";

                // Load sensor config if needed
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

document.addEventListener("DOMContentLoaded", loadDeviceStatus);