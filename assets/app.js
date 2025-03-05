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
    effect:'slide',
    loop:true,
    speed:600,
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
      lazy:true,
    }
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

// ************************************************************** CART *********************************************************************** 

// PAGE PRODUCTS & DETAIL PRODUCT 

// INCREMENT QUANTITY (PRODUCTS + DETAIL PRODUCT)

const incrementButtons = document.querySelectorAll('.increment');
incrementButtons.forEach(button => {
  button.addEventListener('click', function(){
    let product = button.getAttribute('data-product');
    let input = document.querySelector(`input[data-product="${product}"]`);
    let currentQuantity = parseInt(input.value);
    input.value = currentQuantity + 1 ; 
  });
}); 

// DECREMENT QUANTITY (PRODUCTS + DETAIL)

const decrementButtons = document.querySelectorAll('.decrement');
decrementButtons.forEach(button => {
  button.addEventListener('click', function(){
    let product = button.getAttribute('data-product');
    let input = document.querySelector(`input[data-product="${product}"]`);
    let currentQuantity = parseInt(input.value);
    if(currentQuantity > 0){
      input.value = currentQuantity - 1 ; 
    }else{
      input.value = 0;
    }
  });
}); 

// ADD TO CART (PRODUCTS + DETAIL)

const alertBox = document.querySelector(".alertBox");
let alertMessage = document.querySelector(".alertMessage");
const closeAlert = document.querySelector(".closeAlert");
const successMsg = document.getElementById("success-msg");

let addToCartButtons = document.querySelectorAll(".add-to-cart");
addToCartButtons.forEach(button => {
  button.addEventListener('click', function() {
    let product = button.getAttribute('data-product');
    let quantity = document.querySelector(`input[data-product="${product}"]`).value;

    if(alertBox && alertMessage && closeAlert){
      if(quantity > 1){
        alertMessage.innerHTML = "Produits ajoutés au panier";
        alertBox.style.display = "block";
      }else if(quantity == 1) {
        alertMessage.innerHTML = "Produit ajouté au panier";
        alertBox.style.display = "block";
      }else{
        alertBox.style.display = "none";
      }
  
      closeAlert.addEventListener('click',function(){
        alertBox.style.display = "none";
      })

    }
    if (parseInt(quantity) > 0) {
      updateCart(product, quantity);
      // updateSideCartQuantity(product,quantity);
    }
  });
});


// PAGE PANIER

// INCREMENT QUANTITY (CART)

const incrementButtonsCart = document.querySelectorAll('.cart-increment');
incrementButtonsCart.forEach(button => {
  button.addEventListener('click', function(){
    let product = button.getAttribute('cart-product');
    let input = document.querySelector(`input[cart-product="${product}"]`);
    let currentQuantity = parseInt(input.value);
    input.value = currentQuantity + 1 ; 

    updatePriceTotal();
    updateCartSubTotals();
    updateCart(product, 1);

    // updateSideCart()

  });
}); 

// DECREMENT QUANTITY (CART)
const decrementButtonsCart = document.querySelectorAll('.cart-decrement');

decrementButtonsCart.forEach(button => {
  button.addEventListener('click', function(){
    let product = button.getAttribute('cart-product');
    let input = document.querySelector(`input[cart-product="${product}"]`);
    let currentQuantity = parseInt(input.value);
    if(currentQuantity > 0) {
      input.value = currentQuantity -1;
      updatePriceTotal()
      updateCartSubTotals()
      updateCart(product, -1);
    }
  });
});

// UPDATE TOTAL ITEMS (CART)
function updateCart(product,quantity){

  let nbItemsElements = document.querySelectorAll('.nbItems')

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
      nbItemsElements.forEach(nbItemsElement => {
        if(data.nbItems == 1){
          nbItemsElement.textContent = data.nbItems + " article";
        }else{
          nbItemsElement.textContent = data.nbItems + " articles";
        }
      })
    })

    .catch(error => {
      console.error('Erreur',error);
    });
  }


// UPDATE TOTAL (CART)
function updatePriceTotal(){

  let totalPrice = 0; 
  const cartItems = document.querySelectorAll('.cart-item');
  if(cartItems.length>0){
    cartItems.forEach(item => {
      let input = item.querySelector('input[cart-product]');
      let quantity = parseInt(input.value);
      let price = parseFloat(item.querySelector('.product-price').textContent);
      totalPrice += price*quantity;
      document.querySelector('.total-price').textContent = totalPrice.toFixed(2) + " €";
      })
  }else{
    document.querySelector('.total-price').textContent = "0 €";
  }

}


// UPDATE SUBTOTALS (CART)
function updateCartSubTotals(){
  const cartItems = document.querySelectorAll('.cart-item');
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

 // DELETE ITEM (CART)

  const removeItemButton = document.querySelectorAll('.remove-item');
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
        .then(response => response.json())
        .then(data => {
          if(data.success){
            let productTable = document.querySelector(`table[cart-item="${product}"]`);
            let nbItemsElement = document.querySelector('.nbItems'); 
              if (productTable){
        
                if (data.nbItems === 1) {
                  nbItemsElement.textContent = `${data.nbItems} article`;
              } else {
                  nbItemsElement.textContent = `${data.nbItems} articles`;
              }
              productTable.remove();   
              updatePriceTotal();
            }
          }else{
            console.error('erreur lors de la suppression');
          }
        })
        .catch(error => console.error('Erreur:', error));
    });
  });


// PANIER LATERAL - TOGGLE  
const sideCart = document.querySelector('.cart-summary'); 
const cartIcon = document.querySelector('.fa-basket-shopping');

cartIcon.addEventListener('click', function() {
  // updateSideCart()
  sideCart.classList.toggle('visible');
});

document.addEventListener('click', function(event) {
  if (!sideCart.contains(event.target) && !cartIcon.contains(event.target)) {
    sideCart.classList.remove('visible');
  }
  // => event.target = référence à l'élément cliqué 
// => sideCart.contains(event.target) => si l'élément sideCart contient l'élément cliqué 
// => "!" la condition précédente est "vraie" si clique à l'extérieur du panier, sinon elle est fausse 
});


//  function updateSideCart(){
//   const cartItems = document.querySelectorAll(".sideCart-item");
//   cartItems.forEach((cartItem, index) => {
    
//     const url = '/cart/side-cart';

//     fetch(url, {
//       headers:{
//         'Content-Type':'application/json',
//       }
//     })
//     .then(response => {
//       console.log(response);
//       return response.json();
//     } )
//     .then(data => {
//       console.log('data',data);
//       console.log('hello');
//       if(data.length >0){
//         data.forEach(item => {
//           let item = data[index];
//           cartItem.querySelector('.productName').textContent = item.productName;
//           cartItem.querySelector('.typeOrColor').textContent = item.productType ? item.productType : item.productColor; 
//           cartItem.querySelector('.volumeOrSize').textContent = item.productVolume ? item.productVolume : item.size;
//           cartItem.querySelector('.counter-display').value = item.quantity;
//           cartItem.querySelector('.product-price').textContent = item.VATprice ;
//           cartItem.querySelector('.sub-total__side-cart').textContent = item.VATprice * item.quantity + ' €' ;
//           // document.querySelector('price-total').textContent = total ;
//         });

//       }
//     })
//     .catch(error => console.error('Erreur lors de la mise à jour du panier:', error))
//   });
// }










// INCREMENT QUANTITY (SIDE CART)

// const incrementButtonsSideCart = document.querySelectorAll('.side-cart-increment');
// incrementButtonsSideCart.forEach(button => {
//   button.addEventListener('click', function(){
//     let product = button.getAttribute('side-cart-product');
//     let input = document.querySelector(`input[side-cart-product="${product}"]`);
//     let currentQuantity = parseInt(input.value);
//     input.value = currentQuantity + 1 ; 

//   });
// }); 

// DECREMENT QUANTITY (SIDE CART)
// const decrementButtonsSideCart = document.querySelectorAll('.side-cart-decrement');

// decrementButtonsSideCart.forEach(button => {
//   button.addEventListener('click', function(){
//     let product = button.getAttribute('side-cart-product');
//     let input = document.querySelector(`input[side-cart-product="${product}"]`);
//     let currentQuantity = parseInt(input.value);
//     if(currentQuantity > 0) {
//       input.value = currentQuantity -1;
//       updatePriceTotal()
//       updateCartSubTotals()
//       updateCart(product, -1);
//     }
//   });
// });






