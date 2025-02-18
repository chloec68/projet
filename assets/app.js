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


// INCREMENT QUANTITY (PRODUCTS)

let incrementButtons = document.querySelectorAll('.increment');
incrementButtons.forEach(button => {
  button.addEventListener('click', function(){
    let product = button.getAttribute('data-product');
    let input = document.querySelector(`input[data-product="${product}"]`);
    let currentQuantity = parseInt(input.value);
    input.value = currentQuantity + 1 ; 

        updateCartTotal();
        updateCartSubTotals();
  });
}); 

// INCREMENT QUANTITY + ADD TO CART (CART)

let incrementButtonsCart = document.querySelectorAll('.cart-increment');
incrementButtonsCart.forEach(button => {
  button.addEventListener('click', function(){
    let product = button.getAttribute('cart-product');
    let input = document.querySelector(`input[cart-product="${product}"]`);
    let currentQuantity = parseInt(input.value);
    input.value = currentQuantity + 1 ; 

        updateCartTotal();
        updateCartSubTotals();
        addToCart(product, 1);
  });
}); 

// DECREMENT QUANTITY
let decrementButtons = document.querySelectorAll('.cart-decrement');

decrementButtons.forEach(button => {
  button.addEventListener('click', function(){
    let product = button.getAttribute('cart-product');
    let input = document.querySelector(`input[cart-product="${product}"]`);
    let currentQuantity = parseInt(input.value);
    if(currentQuantity > 0) {
      input.value = currentQuantity -1;
      updateCartTotals()
      updateCartSubTotals()
    }
  });
});

// ADD TO CART FROM PRODUCTS PAGE VIA ADD TO CART BUTTON
let addToCartButtons = document.querySelectorAll(".add-to-cart");
addToCartButtons.forEach(button => {
  button.addEventListener('click', function() {
    let product = button.getAttribute('data-product');
    let quantity = document.querySelector(`input[data-product="${product}"]`).value;

    if (parseInt(quantity) > 0) {
      addToCart(product, quantity);
    }
  });
});

// UPDATE CART TOTALS (NB ITEMS + TOTAL PRICE)
function updateCartTotal(){

  let totalPrice = 0; 
  let cartItems = document.querySelectorAll('.cart-item');
  cartItems.forEach(item => {
    let input = item.querySelector('input[cart-product]');
    let quantity = parseInt(input.value);
    let price = parseFloat(item.querySelector('.product-price').textContent);
    totalPrice += price*quantity;
    document.querySelector('.total-price').textContent = totalPrice.toFixed(2) + " €";
  })
}

// UPDATE CART SUBTOTALS (NB ITEMS + TOTAL PRICE)
function updateCartSubTotals(){
  let cartItems = document.querySelectorAll('.cart-item');
  let subTotal = 0; 
  cartItems.forEach(item => {
    let quantity = item.querySelector('input[type="number"].input-box').value;
    let itemPriceElement = item.querySelector('.product-price');
    let itemPrice = parseFloat(itemPriceElement.textContent);
    subTotal = itemPrice * quantity 
    let subTotalElement = item.querySelector('.sub-total');
    subTotalElement.textContent = subTotal.toFixed(2) + " €";
  })
}


// ADD TO CART 
function addToCart(product,quantity){

  fetch('/cart/add/{id}',{
    method : 'POST',
    headers :{
        'Content-Type':'application/json'
    } ,
    body: JSON.stringify({
    product:product,
    quantity:quantity
    })
  })

  .then(response => response.json())

    .then(data => {
        if(data.nbItems == 1){
            document.querySelector('.nbItems').textContent = data.nbItems + " article";
            alert('produit ajouté au panier');
        }else{
            document.querySelector('.nbItems').textContent = data.nbItems + " articles";
            alert('produit ajouté au panier');
        }
      })
    .catch(error => {
      console.error('Erreur',error);
    });
  }


 // DELETE ITEM FROM CART 

  let removeItemButton = document.querySelectorAll('.remove-item');

  removeItemButton.forEach(button => {
    button.addEventListener('click',function(){
      let product = button.getAttribute('cart-product');
      let url = `/cart/remove/${product}`;

      fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type':'application/json',
        },
      })
      .then(response=>response.json())
      .then(data => {
        if(data.success){
          let productTable = document.querySelector(`table[cart-item="${product}"]`);
          if (productTable){
            productTable.remove();
          }
        }else{
          console.error('erreur lors de la suppression'); // à modifier ultérieurement !!!!!!!!!!!!!!!
        }
      })
      .catch(error => console.error('Erreur:', error));
    });
  });

 

