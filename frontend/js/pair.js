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
            } else {
                alert("Pairing failed: " + json.message);
            }
        })
        .catch(err => {
            console.error("Pairing error:", err);
            alert("Failed to pair device.");
        });
}