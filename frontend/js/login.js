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

    fetch('../../center/php/login.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(jsonData => {
            console.log("Raw Response:", jsonData); // DEBUG
            document.getElementById('message').textContent = jsonData.message;
            if (jsonData.status === "success") {
                window.location.href = 'profile.html';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('message').textContent = "Login fail";
        });
}