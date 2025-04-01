function createAccount() {
    let first_name = document.getElementById('first-name').value.trim();
    let last_name = document.getElementById('last-name').value.trim();
    let email = document.getElementById('email').value.trim();
    let password = document.getElementById('password').value.trim();

    if (!email || !password || !first_name || !last_name) {
        document.getElementById('message').textContent = "All fields are required";
        return;
    }

    if (password.length < 6) {
        document.getElementById('message').textContent = "Password must be at least 6 characters.";
        return;
    }

    let formData = new FormData();
    formData.append('first_name', first_name);
    formData.append('last_name', last_name);
    formData.append('email', email);
    formData.append('password', password);

    fetch('../../center/php/register.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(jsonData => {
            console.log("Raw Response:", jsonData); // DEBUG
            document.getElementById('message').textContent = jsonData.message;
            if (jsonData.status === "success") {
                window.location.href = 'login.html';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('message').textContent = "Registration failure";
        });
}