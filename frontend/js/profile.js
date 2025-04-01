document.addEventListener("DOMContentLoaded", loadProfile);

function loadProfile() {
    fetch('https://section-three.it313communityprojects.website/center/php/profile_load.php', {
        method: "GET",
        credentials: "include"
    })
    .then(response => {
        console.log("Raw response:", response); // Log the raw response
        return response.json();
    })
    .then(jsonData => {
        console.log("Parsed JSON data:", jsonData); // Log the parsed JSON data
        if (jsonData.status === "success") {
            console.log("Profile loaded successfully:", jsonData.data); // Log success
            document.getElementById("first-name-text").innerText = jsonData.data.first_name || "Not provided";
            document.getElementById("last-name-text").innerText = jsonData.data.last_name || "Not provided";
            document.getElementById("dob-text").innerText = jsonData.data.date_of_birth || "Not provided";
            document.getElementById("contact-text").innerText = jsonData.data.email || "Not provided";
            document.getElementById("caretaker-name-text").innerText = jsonData.data.caretaker_name || "N/A";
            document.getElementById("caretaker-contact-text").innerText = jsonData.data.caretaker_email || "N/A";

            document.getElementById("first-name-input").value = jsonData.data.first_name || "";
            document.getElementById("last-name-input").value = jsonData.data.last_name || "";
            document.getElementById("dob-input").value = jsonData.data.date_of_birth || "";
            document.getElementById("contact-input").value = jsonData.data.email || "";
            document.getElementById("caretaker-name-input").value = jsonData.data.caretaker_name || "";
            document.getElementById("caretaker-contact-input").value = jsonData.data.caretaker_email || "";

            // Update profile picture
            const profileImage = document.getElementById("profile-image");
            profileImage.src = jsonData.data.profile_picture || "https://via.placeholder.com/200";
        } else {
            console.error("Error in response:", jsonData.message); // Log the error message
            window.location.href = "login.html"; // Redirect to login.html
        }
    })
    .catch(error => {
        console.error("Fetch error:", error); // Log fetch errors
        console.log("Redirecting to login.html due to fetch error");
        window.location.href = "login.html"; // Redirect to login.html
    });
}

function toggleEdit() {
    const inputs = document.querySelectorAll(".profile-info input");
    const spans = document.querySelectorAll(".profile-info span");
    const editButton = document.querySelector(".edit-btn");

    if (editButton.innerText === "Edit") {
        inputs.forEach(input => input.style.display = "inline-block");
        spans.forEach(span => span.style.display = "none");
        editButton.innerText = "Save";
    } else {
        updateProfile();
    }
}

function previewProfileImage() {
    const file = document.getElementById('profile-image-upload').files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Update profile image preview
            document.getElementById('profile-image').src = e.target.result;
            // Update profile image in profile-info section
            document.getElementById('profile-image-info').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}

function updateProfile() {
    const formData = new FormData();
    formData.append("first_name", document.getElementById("first-name-input").value);
    formData.append("last_name", document.getElementById("last-name-input").value);
    formData.append("date_of_birth", document.getElementById("dob-input").value);
    formData.append("email", document.getElementById("contact-input").value);
    formData.append("caretaker_name", document.getElementById("caretaker-name-input").value);
    formData.append("caretaker_email", document.getElementById("caretaker-contact-input").value);

    fetch('https://section-three.it313communityprojects.website/center/php/profile_update.php', {
        method: "POST",
        body: formData,
        credentials: "include"
    })
    .then(response => response.json())
    .then(jsonData => {
        alert(jsonData.message);
        if (jsonData.status === "success") {
            loadProfile();
            document.querySelectorAll(".profile-info input").forEach(input => input.style.display = "none");
            document.querySelectorAll(".profile-info span").forEach(span => span.style.display = "inline-block");
            document.querySelector(".edit-btn").innerText = "Edit";
        }
    })
    .catch(error => {
        console.error("Error updating profile:", error);
    });
}