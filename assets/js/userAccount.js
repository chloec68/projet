const alertContainer = document.querySelector(".alertContainer");
const confirmMsg = document.querySelector(".alertMsg");
const confirmButton = document.querySelector(".confirmButton");
const deleteButton = document.querySelector(".delete-accountButton");
const cancelButton = document.querySelector(".cancelButton");

if(deleteButton){
  deleteButton.addEventListener('click', function(){
    if(alertContainer && confirmMsg && confirmButton){
      alertContainer.style.display = 'block';
    }else{
      alertContainer.style.display = 'none';
    }
  
    cancelButton.addEventListener('click',function(){
      alertContainer.style.display = "none";
    })
  
    confirmButton.addEventListener('click',function(){
        fetch('/profile/delete-account',{
          method:'POST',
          headers: {
            'Content-Type':'application/json',
          }
        })
        .then(response => response.json())
        .then(data => {
          if(data.success){
            window.location.reload();
          }
        })
        .catch(error => {
          console.error('Error:',error);
        });
    })
  })
}