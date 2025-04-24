function pairDevice() {
    const deviceId = document.getElementById("device-id").value.trim();
    const pairingCode = document.getElementById("pairing-code").value.trim();

    if (!deviceId || !pairingCode) {
        alert("Please enter both the device ID and pairing code.");
        return;
    }

    const formData = new FormData();
    formData.append("device_id", deviceId);
    formData.append("pairing_code", pairingCode);

    fetch(
            "https://section-three.it313communityprojects.website/src/routes/device/pair.php", {
                method: "POST",
                body: formData,
                credentials: "include",
            },
        )
        .then((res) => res.json())
        .then((json) => {
            alert(json.message);
            if (json.status === "success") {
                // need to refresh UI
                console.log("Device paired successfully.");
            }
        })
        .catch((err) => {
            console.error("Pairing request failed:", err);
            alert("Pairing failed. Try again.");
        });
}