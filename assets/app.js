// import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';



// SWIPER

  var swiper = new Swiper(".mySwiper", {
    observer: true,
    observeParents: true,
    slidesPerView: 3,
    spaceBetween: 10,
    centeredSlides: true,
    grabCursor:true,
    loop:true,
    speed:600,
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
  });


  var swiper = new Swiper(".mySwiperProducts", {
    grabCursor:true,
    pagination: {
      el: ".swiper-pagination",
      dynamicBullets: true,
    },
  });


  // SCROLL UP BUTTON
  function buttonVisibility() {
    let button = document.querySelector(".top-button");
    if(button){
     if(window.scrollY > 300) {
      button.style.display = "block";
      } else {
        button.style.display = "none";
      }
    }
  }
  document.addEventListener('scroll', buttonVisibility);



  // ADD ITEM TO CART + UPDATE TOTAL ITEMS 

let addButtons = document.querySelectorAll(".add-to-cart");

addButtons.forEach(button => {
  button.addEventListener('click', function(){
    let product = button.getAttribute('data-product');
    let quantity = button.getAttribute('data-quantity');
    let inputBox = document.querySelector(`input[data-product="${product}"]`);
    let currentQuantity = parseInt(inputBox.value);

    inputBox.value = currentQuantity + parseInt(quantity);

    fetch('/cart/addAjax',{
        method : 'POST',
        headers :{
            'Content-Type':'application/json'
        } ,
        body: JSON.stringify({
        product:product,
        quantity:quantity
        })
      })

    .then(response =>
    {   
      return response.text();
    })

    .then(text => {
        try {
            const data = JSON.parse(text);  // Essaie de parser la réponse en JSON
              if(data.nbItems == 1){
                  document.getElementById('nbItems').textContent = data.nbItems + " article";
              }else{
                  document.getElementById('nbItems').textContent = data.nbItems + " articles";
              }
        }catch (error) {
            console.error('Erreur de parsing JSON:', error);
        }
    })

    .catch(error => {
        console.error('Erreur',error);
    });
  });
});



  // REMOVE ITEM FROM CART + UPDATE TOTAL ITEMS 


let minusButtons = document.querySelectorAll(".remove-from-cart");

minusButtons.forEach(button => {
  button.addEventListener('click', function(){
    let product = button.getAttribute('data-product');
    let quantity = button.getAttribute('data-quantity');
    let inputBox = document.querySelector(`input[data-product="${product}"]`);
    let currentQuantity = parseInt(inputBox.value);

      if (currentQuantity - parseInt(quantity) >= 1) {
          inputBox.value = currentQuantity - parseInt(quantity);
      } else {
          inputBox.value = 1; 
      }

      fetch('/cart/removeAjax',{
          method : 'POST',
          headers :{
              'Content-Type':'application/json'
          } ,
          body: JSON.stringify({
          product:product,
          quantity:quantity
          })
      })

      .then(response =>
      {   
        return response.text();
      }) 

      .then(text => {
        try {
          const data = JSON.parse(text); 

          if(data.nbItems == 1){
              document.getElementById('nbItems').textContent = data.nbItems + " article";
          }else{
              document.getElementById('nbItems').textContent = data.nbItems + " articles";
          }
        }catch (error) {
          console.error('Erreur de parsing JSON:', error);
        }
      })
      .catch(error => {
          console.error('Erreur',error);
      });
  });
});