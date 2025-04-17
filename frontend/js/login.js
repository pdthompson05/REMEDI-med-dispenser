function login() {
    let email = document.getElementById('email').value.trim();
    let password = document.getElementById('password').value.trim();

    const messageBox = document.getElementById('message');
    messageBox.textContent = ""; // clear any previous message

    if (!email || !password) {
        messageBox.textContent = "Please enter email and password.";
        return;
    }

    let formData = new FormData();
    formData.append('email', email);
    formData.append('password', password);

    fetch('https://section-three.it313communityprojects.website/src/auth/user/login.php', {
        method: 'POST',
        body: formData,
        credentials: 'include'
    })
        .then(async response => {
            const raw = await response.text();       // get raw response
            console.log("Raw Response Text:", raw);  // log for debugging

            try {
                const jsonData = JSON.parse(raw);    // try to parse JSON
                console.log("Parsed JSON:", jsonData);

                if (jsonData.message) {
                    messageBox.textContent = jsonData.message;
                }

                if (jsonData.status === "success" && jsonData.redirect) {
                    window.location.href = jsonData.redirect;
                }
            } catch (err) {
                console.error("JSON parse failed:", err);
                messageBox.textContent = "Server error: Invalid response.";
            }
        })
        .catch(error => {
            console.error("Fetch failed:", error);
            messageBox.textContent = "Network error: Could not connect to server.";
        });
}