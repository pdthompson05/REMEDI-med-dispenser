function toggleMedForm() {
    const form = document.getElementById("med-form");
    form.style.display = form.style.display === "none" ? "block" : "none";
}

function addMedication() {
    const medName = document.getElementById("med-name").value.trim();
    const strength = document.getElementById("med-strength").value.trim();
    const rxNumber = document.getElementById("rx-number").value.trim();
    const quantity = parseInt(document.getElementById("quantity").value.trim());

    if (!medName || !strength || !rxNumber || isNaN(quantity) || quantity <= 0) {
        alert("Please fill in all medication fields correctly.");
        return;
    }

    const formData = new FormData();
    formData.append("med_name", medName);
    formData.append("strength", strength);
    formData.append("rx_number", rxNumber);
    formData.append("quantity", quantity);

    fetch("https://section-three.it313communityprojects.website/src/routes/med/add.php", {
        method: "POST",
        body: formData,
        credentials: "include"
    })
        .then(async res => {
            const raw = await res.text();
            try {
                const json = JSON.parse(raw);
                if (json.status === "success") {
                    alert("Medication added!");
                    // Clear inputs
                    document.getElementById("med-name").value = "";
                    document.getElementById("med-strength").value = "";
                    document.getElementById("rx-number").value = "";
                    document.getElementById("quantity").value = "";
                    toggleMedForm();
                    loadProfile(); // reload to fetch new med_id
                } else {
                    alert("Error: " + json.message);
                }
            } catch (err) {
                console.error("JSON parse failed:", err, "Raw:", raw);
                alert("Error adding medication. Check console.");
            }
        })
        .catch(error => {
            console.error("Add medication failed:", error);
            alert("Add medication failed. See console.");
        });
}

function deleteMedication(medId, buttonElement) {
    const formData = new FormData();
    formData.append("med_id", medId);

    fetch("https://section-three.it313communityprojects.website/src/routes/med/delete.php", {
        method: "POST",
        body: formData,
        credentials: "include"
    })
        .then(async res => {
            const raw = await res.text();
            try {
                const json = JSON.parse(raw);
                if (json.status === "success") {
                    // Remove <li> from UI
                    buttonElement.parentElement.remove();
                } else {
                    alert("Failed to delete: " + json.message);
                }
            } catch (err) {
                console.error("Failed to parse delete response:", raw);
                alert("Error deleting medication. Check console.");
            }
        })
        .catch(err => {
            console.error("Delete request failed:", err);
            alert("Error contacting server.");
        });
}