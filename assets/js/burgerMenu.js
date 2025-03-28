//Transform burger menu from lines to cross 
const burger = document.querySelector('.burger');
if(burger){
  burger.addEventListener('click', () => {
    burger.classList.toggle('active');
  });
}

   
  // Display burger menu
  window.burgerMenuMobile = function burgerMenuMobile(){
    const nav = document.querySelector('.nav');
        if(nav.style.display === "block"){
            nav.style.display = "none";
        } else {
            nav.style.display = "block";
        }
  }     
