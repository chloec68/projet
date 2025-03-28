
document.addEventListener('DOMContentLoaded', function() {  
    const darkModeButton = document.getElementById("darkModeButton");
    const darkModeText = document.getElementById("darkModeText");  
    const darkModeIcon =  document.getElementById("darkModeIcon");
    const logo2 = document.querySelectorAll('#logo2');  

    if (darkModeButton && darkModeText && darkModeIcon) {
        darkModeButton.onclick = function() {
          document.body.classList.toggle('dark-mode');

            if (document.body.classList.contains("dark-mode")) {
              localStorage.setItem("dark-mode", "enabled"); 
            } else {
              localStorage.setItem("dark-mode", "disabled"); 
            }
            if (document.body.classList.contains("dark-mode")) {
              darkModeIcon.classList.replace('fa-moon', 'fa-lightbulb');
              darkModeText.textContent = "Light Mode";
              if(logo2){
                logo2.forEach(logo => {
                  logo.src = "/logo/logo-1.webp"
                });
              }
      
          } else {
            darkModeIcon.classList.replace('fa-lightbulb', 'fa-moon');
              darkModeText.textContent = "Dark Mode";
              if(logo2){
                logo2.forEach(logo => {
                  logo.src = "/logo/logo-2.webp"
                });
              }
          }
        }
    }
});