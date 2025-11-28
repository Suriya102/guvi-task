$(document).ready(function () {
  // Registration AJAX
  $('#registerForm').on('submit', function (e) {
    e.preventDefault();
    if (!this.checkValidity()) {
      this.classList.add('was-validated');
      return;
    }
    const email = $('#email').val();
    const password = $('#password').val();

    $.ajax({
      url: 'register.php',
      method: 'POST',
      dataType: 'json',
      data: { email, password },
      success: function (res) {
        if (res.success) {
          $('#registerMessage').text('Registration successful. Redirecting to login...');
          setTimeout(() => window.location.href = 'login.html', 1500);
        } else {
          $('#registerMessage').text('Error: ' + res.message);
        }
      }
    });
  });

  // Login AJAX
  $('#loginForm').on('submit', function (e) {
    e.preventDefault();
    if (!this.checkValidity()) {
      this.classList.add('was-validated');
      return;
    }
    const email = $('#loginEmail').val();
    const password = $('#loginPassword').val();

    $.ajax({
      url: 'login.php',
      method: 'POST',
      dataType: 'json',
      data: { email, password },
      success: function (res) {
        if (res.success) {
          // Store session token in localStorage
          localStorage.setItem('sessionToken', res.sessionToken);
          window.location.href = 'profile.html';
        } else {
          $('#loginMessage').text('Login failed: ' + res.message);
        }
      }
    });
  });

  // Profile page logic if on profile.html
  if (window.location.pathname.endsWith('profile.html')) {
    const sessionToken = localStorage.getItem('sessionToken');
    if (!sessionToken) {
      window.location.href = 'login.html';
    }
    // Fetch profile info
    $.ajax({
      url: 'profile.php',
      method: 'GET',
      dataType: 'json',
      headers: { 'Authorization': 'Bearer ' + sessionToken },
      success: function (res) {
        if (res.success) {
          $('#age').val(res.profile.age || '');
          $('#dob').val(res.profile.dob || '');
          $('#contact').val(res.profile.contact || '');
        } else {
          alert('Session expired or invalid. Please login again.');
          localStorage.removeItem('sessionToken');
          window.location.href = 'login.html';
        }
      }
    });

    // Profile update AJAX
    $('#profileForm').on('submit', function (e) {
      e.preventDefault();
      if (!this.checkValidity()) {
        this.classList.add('was-validated');
        return;
      }
      const age = $('#age').val();
      const dob = $('#dob').val();
      const contact = $('#contact').val();

      $.ajax({
        url: 'profile.php',
        method: 'POST',
        dataType: 'json',
        headers: { 'Authorization': 'Bearer ' + sessionToken },
        data: { age, dob, contact },
        success: function (res) {
          if (res.success) {
            $('#profileMessage').text('Profile updated successfully.');
          } else {
            $('#profileMessage').text('Error updating profile: ' + res.message);
          }
        }
      });
    });

    // Logout button clears localStorage and redirects to login
    $('#logoutBtn').on('click', function () {
      localStorage.removeItem('sessionToken');
      window.location.href = 'login.html';
    });
  }
});
