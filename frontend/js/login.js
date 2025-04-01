function login() {
    let email = document.getElementById('email').value.trim();
    let password = document.getElementById('password').value.trim();
    
    if (!email || !password) {
        document.getElementById('message').textContent = "Please enter email and password.";
        return;
    }

    let formData = new FormData();
    formData.append('email', email);
    formData.append('password', password);

    fetch('https://section-three.it313communityprojects.website/center/php/login.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(jsonData => {
            console.log("Raw Response:", jsonData);
            document.getElementById('message').textContent = jsonData.message;
            if (jsonData.status === "success") {
                // Use the redirect field from the response
                window.location.href = jsonData.redirect;
            }
        })
        .catch(error => {
            console.error('Detailed Error:', error);
            document.getElementById('message').textContent = "Login fail - Server error";
        });
}