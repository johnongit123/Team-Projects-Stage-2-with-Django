const usernameInput = document.getElementById('user-name');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const re_passwordInput = document.getElementById('re-password');


// regex for input sanitisation
const format = /^[a-zA-Z0-9]+$/;
const password_format = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/;
const email_format = /^[a-zA-Z0-9]+@example\.com$/;


//use fetch api to check if username or email exists
async function checkUserExists() {
    username = usernameInput.value;
    email = emailInput.value;

    const formData = new FormData();
    formData.append('username', username);
    formData.append('email', email);
  
    try {
      const response = await fetch('/php-pages/other/user_check.php', { 
        method: 'POST',
        body: formData
      });
  
      if (!response.ok) {
        throw new Error('Network response was not ok'); 
      }
  
      const data = await response.json();  
      return [data.userExists, data.emailExsits]; 
    } catch (error) {
      console.error('Error checking username:', error);
    }
  }


// set a field to invalid
function setInvalid(field, reason){
    input = document.getElementById(field + "-input");
    text = document.getElementById(field + "-error-text");

    text.innerHTML = reason;
    text.classList.add("invalid");

    input.classList.add("invalid");
}

// set a field valid
function setValid(field){
    input = document.getElementById(field + "-input");
    text = document.getElementById(field + "-error-text");

    text.innerHTML = '';
    text.classList.remove("invalid");

    input.classList.remove("invalid");
}
  

usernameInput.addEventListener('input', async () => {
    var check = await checkUserExists();
    console.log(check[0]);
    if(check[0]){
        setInvalid("user", "User Exists");
    }else{
        setValid("user");
    }

    if(!(format.test(usernameInput.value) || usernameInput.value == "")){
        setInvalid("user", "User cant contain specical characters");
    }else{
        setValid("user");
    }
});

emailInput.addEventListener('input', async () => {
    emailInput.value += ""
    var check = await checkUserExists();
    if(check[1]){
        setInvalid("email", "Email already signed up");
    }else{
        setValid("email");
    }

    if(!(email_format.test(emailInput.value) || emailInput.value == "")){
        setInvalid("email", "Email must be in format (a-z, 0-9)@example.com");
    }else{
        setValid("email");
    }
});


passwordInput.addEventListener('input', async () => {
    if(!(password_format.test(passwordInput.value) || passwordInput.value == "")){
        setInvalid("password", "Password requirements not met")
    }else{
        setValid("password");
    }
});


re_passwordInput.addEventListener('input', async () => {
    if(!re_passwordInput.value == passwordInput.value){
        setInvalid("re-password", "Passwords must match");
    }else{
        setValid("re-password");
    }
 
});