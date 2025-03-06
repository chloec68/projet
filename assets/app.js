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

  // ******************************************************************************** SIDE CART********************************************************* 

// SIDE CART - DISPLAY / TOGGLE  
const sideCart = document.querySelector('.cart-summary'); 
const cartIcon = document.querySelector('.fa-basket-shopping');

cartIcon.addEventListener('click', function() {
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


// SIDE CART - CONTENT UPDATE
const basketButton = document.querySelector('.fa-basket-shopping');
basketButton.addEventListener('click',function(){

  const url = '/cart/side-cart';
  fetch(url, {
    headers:{
      'Content-Type':'application/json',
    }
  })
  .then(response => {
    return response.json();
  } )
  .then(data => {
    const cartSummary = document.querySelector('.cart-summary');
    let htmlContent = "<h2>Votre panier</h2><a href='/cart'>Voir le panier</a>";
      if(data.length > 0){
        data.forEach(item => {
          htmlContent += 
            `<article class="sideCart-item side-cart-product">
                <div class="item-containers">
                    <div class="item-container">
                      <img class="product-pic" src="${item.picture}" alt="Photo du produit">
                      <p><span class="productName">${item.productName} -</span> <span>${item.type ? item.type : item.color} -</span> <span>${item.volume ? item.volume : item.size}</span> </p>
                    </div>
                    <div class="item-container middle">
                      <div class="counter">
                        <input class="counter-display input-box" type="number" value="${item.quantity}" min="0" />
                      </div>
                      <p class="product-price">x ${item.VATprice} €</p>
                    </div>
                    <div class="item-container right bold">
                      <p class="sub-total__side-cart">Sous-total : ${item.VATprice * item.quantity} €</p>
                    </div>
                </div>
              </article>
            `;
        })
        htmlContent += 
        '<p class="side-cart__nbItems">  Nombre d\'articles : '+ data[data.length-1].nbItems + '</p> <div class="total bold"><p>Total ttc :</p><p class="bold side-cart-total">' + data[data.length-1].total + '€</p></div>';

      }else{
        htmlContent = "<h2>Votre panier</h2><a href='/cart'>Voir le panier</a> <p>Le panier est vide</p>";
      }
    cartSummary.innerHTML = htmlContent;
  })
})






