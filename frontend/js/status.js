function fetchDeviceStatus() {
    fetch("/src/routes/device/status.php", {
        method: "GET",
        credentials: "include"
    })
        .then(res => res.json())
        .then(json => {
            const statusEl = document.getElementById("device-status");
            if (json.status === "success") {
                const d = json.device;
                statusEl.innerText = `Device ${d.device_id}: ${d.connected ? "Connected" : "Disconnected"}, Temp: ${d.temperature ?? "N/A"}°C`;
            } else {
                statusEl.innerText = "No device found.";
            }
        })
        .catch(err => {
            console.error("Status fetch failed:", err);
            document.getElementById("device-status").innerText = "Device status error.";
        });
}

document.addEventListener("DOMContentLoaded", fetchDeviceStatus);
