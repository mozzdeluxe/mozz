// register.js
document.getElementById('registerButton').addEventListener('click', function(event) {
    event.preventDefault();
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    if (password !== confirmPassword) {
        alert('Passwords do not match!');
        return;
    }

    // Send OTP request to server
    fetch('send_otp.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `email=${encodeURIComponent(email)}`,
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert(`An OTP has been sent to ${email}. Please enter the OTP to complete registration.`);
            
            // Show OTP input and verify button
            document.getElementById('otpLabel').style.display = 'block';
            document.getElementById('otp').style.display = 'block';
            document.getElementById('verifyButton').style.display = 'block';
            document.getElementById('registerButton').style.display = 'none';
        } else {
            alert(`Failed to send OTP: ${data.message}`);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while sending OTP.');
    });
});

document.getElementById('verifyButton').addEventListener('click', function(event) {
    event.preventDefault();
    
    const otp = document.getElementById('otp').value;

    if (otp) {
        // Here you would handle OTP verification
        alert('OTP verified! You are now registered.');
        // Submit the form or handle registration completion
    } else {
        alert('Please enter the OTP.');
    }
});
