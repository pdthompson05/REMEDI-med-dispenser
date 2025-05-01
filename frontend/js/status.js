function checkDeviceStatus() {
    fetch('https://section-three.it313communityprojects.website/src/routes/device/status.php', {
            method: 'GET',
            credentials: 'include'
        })
        .then(res => res.json())
        .then(json => {
            const statusEl = document.getElementById("device-status");

            if (json.status === "success") {
                const {
                    connected,
                    updated_at
                } = json.data;
                const lastSeen = new Date(updated_at);
                const now = new Date();
                const minutesAgo = (now - lastSeen) / 60000;

                if (connected && minutesAgo < 5) {
                    statusEl.innerText = "Device connected";
                    statusEl.style.color = "green";
                } else {
                    statusEl.innerText = "Device offline";
                    statusEl.style.color = "red";
                }
            } else {
                statusEl.innerText = "No device paired";
                statusEl.style.color = "gray";
            }
        })
        .catch(err => {
            console.error("Device status error:", err);
        });
}

document.addEventListener("DOMContentLoaded", checkDeviceStatus);