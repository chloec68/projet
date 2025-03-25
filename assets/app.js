// import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

// ************************************************************** DARK MODE *********************************************************************

document.addEventListener('DOMContentLoaded', function() {  
    const darkModeButton = document.getElementById("darkModeButton");
    const darkModeText = document.getElementById("darkModeText");  
    const darkModeIcon =  document.getElementById("darkModeIcon");  

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
          } else {
            darkModeIcon.classList.replace('fa-lightbulb', 'fa-moon');
              darkModeText.textContent = "Dark Mode";
          }
        }
        
    }
});


  // ************************************************************** SCROLL UP BUTTON ************************************************************

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

// ************************************************************** CART RELATED ACTIONS ************************************************************************** 
const cartHeader = document.getElementById('nbItems');
//************************** */ ON PRODUCTS PAGE & DETAIL PRODUCT PAGE

// INCREMENT QUANTITY ACTION

const incrementButtons = document.querySelectorAll('.increment');
incrementButtons.forEach(button => {
  button.addEventListener('click', function(){
    let product = button.getAttribute('data-product');
    let input = document.querySelector(`input[data-product="${product}"]`);
    let currentQuantity = parseInt(input.value);
    input.value = currentQuantity + 1 ; 
  });
}); 

// DECREMENT QUANTITY ACTION

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

// ADD TO CART ACTION

const alertBox = document.querySelector(".alertBox");
let alertMessage = document.querySelector(".alertMessage");
const closeAlert = document.querySelector(".closeAlert");
const successMsg = document.getElementById("success-msg");
let addToCartButtons = document.querySelectorAll(".add-to-cart");
// const stockElements = document.querySelectorAll('.stock-info');

// stockElements.forEach(element => {
//   let stockQuantity = parseInt(element.getAttribute('data-stock'));
//     if (stockQuantity > 10){
      addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
          let product = button.getAttribute('data-product');
          let quantity = document.querySelector(`input[data-product="${product}"]`).value;

          // let sizeButton = document.querySelector(`button[product-size]`);
          // let size = sizeButton ? sizeButton.getAttribute('product-size') : null;
      
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
            updateCart(product,quantity); 
            // ,size
          }
        });
      });
  // }else{
  //   addToCartButtons.forEach(button => {
  //       button.disabled = true;
  //   })
   
  // }
// });



//************************** */ ON CART PAGE 

// INCREMENT QUANTITY ACTION

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

// DECREMENT QUANTITY ACTION
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

// UPDATE TOTAL ITEMS 
function updateCart(product,quantity,size){

  const nbItemsElements = document.querySelectorAll('.nbItems');
  

  fetch('/cart/add/{id}',{
    method : 'POST',
    headers :{
        'Content-Type':'application/json'
    } ,
    body: JSON.stringify({
      product:product,
      quantity:quantity,
      size:size
    })
  })

  .then(response => response.json())

    .then(data => {
      if(data.nbItems > 1){
        cartHeader.textContent = `${data.nbItems} articles`;
      }else if(data.nbItems == 1){
        cartHeader.textContent = `${data.nbItems} article`;
      }else{
        cartHeader.textContent = "Pas d'article";
      }
      
      nbItemsElements.forEach(nbItemsElement => {
        if(data.nbItems < 1){
          const cart = document.querySelector('.main-container');
          cart.innerHTML = '<div class="emptyCart__container"><p class="emptyCart">Le panier est vide</p><a class="redirection-link" href="/product/beers">nos bières <i class="fa-solid fa-circle-arrow-right"></i></a></div>';
        }
        else if(data.nbItems == 1){
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

// UPDATE TOTAL 
function updatePriceTotal(){

  let totalPrice = 0; 
  const cartItems = document.querySelectorAll('.cart-item');
  if(cartItems && cartItems.length>0){
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

// UPDATE SUBTOTALS 
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

 // DELETE ITEM
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
          if(data && data.success){
            let productTable = document.querySelector(`tr[cart-item="${product}"]`);
            let nbItemsElement = document.querySelector('.nbItems'); 
              if (productTable){
                if(data.nbItems < 1){
                  const cart = document.querySelector('.main-container');
                  cart.innerHTML = '<div class="emptyCart__container"><p class="emptyCart">Le panier est vide</p><a class="redirection-link" href="/product/beers">nos bières <i class="fa-solid fa-circle-arrow-right"></i></a></div>';
                  cartHeader.textContent = "Pas d'article";
                }
                else if (data.nbItems == 1) {
                  nbItemsElement.textContent = `${data.nbItems} article`;
                  cartHeader.textContent = `${data.nbItems} article`;
              } else {
                  nbItemsElement.textContent = `${data.nbItems} articles`;
                  cartHeader.textContent = `${data.nbItems} articles`;
              }
              updatePriceTotal();
              productTable.remove();   
            }
          }else{
            console.error('erreur lors de la suppression');
          }
        })
        .catch(error => console.error('Erreur:', error));
    });
  });

  // ******************************************************************************** SIDE CART ********************************************************* 

// SIDE CART - DISPLAY / TOGGLE  
const sideCart = document.querySelector('.cart-summary'); 
const cartIcon = document.querySelector('.cart__container');

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
const basketButton = document.querySelector('.cart__container');
basketButton.addEventListener('click',function(){
  const url = '/cart/side-cart';
  fetch(url, {
    headers:{
      'Content-Type':'application/json',
    }
  })
  .then(response => {
    return response.json();
  })
 
  .then(
    data => {
    const cartSummary = document.querySelector('.cart-summary');
    let htmlContent = "<h2>Votre panier</h2><a href='/cart'>Voir le panier</a>";
      if(data && data.length > 0){
        console.log(data);
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
                        <input class="input-box" type="number" value="${item.quantity}" min="0">
                      </div>
                      <p class="product-price">x ${item.VATprice} €</p>
                    </div>
                    <div class="item-container right bold">
                      <p class="sub-total__side-cart">${(item.VATprice * item.quantity).toFixed(2)} €</p>
                    </div>
                </div>
              </article>
            `;
        })
        htmlContent += 
        '<p class="side-cart__nbItems centered">  Nombre d\'articles : '+ data[data.length-1].nbItems + '</p> <div class="total bold"><p>Total ttc :</p><p class="bold side-cart-total">' + (data[data.length-1].total).toFixed(2) + '€</p></div>';
      }else{
        htmlContent = "<h2>Votre panier</h2><a href='/cart'>Voir le panier</a> <p class='empty-sidecart'>Le panier est vide</p>";
      }
    cartSummary.innerHTML = htmlContent;
  })
})


//****************************************************************** ADD/REMOVE TO/FROM FAVORITES **************************************************************

let favoriteButtons = document.querySelectorAll('.fa-heart');
favoriteButtons.forEach(element => {
  element.addEventListener('click',function(){
    let productId = element.getAttribute('data-product-favorite');
    let isFavorite = element.classList.contains('fa-solid');

    if(isFavorite){
      element.classList.replace('fa-solid','fa-regular');
      const url = `/favorite/remove/${productId}`;

      fetch(url, {
        headers:{
          'Content-Type' : 'application/json',
        }
      })
    
      .then(response => {
        if(response.ok){
          console.log('produit retiré des favoris');
        }
      });

    }else{
      element.classList.replace('fa-regular','fa-solid');
      const url = `/favorite/add/${productId}`;

      fetch(url, {
        headers:{
          'Content-Type':'application/json',
        }
      })
    
      .then(response => {
        if(response.ok){
          console.log('produit ajouté aux favoris');
        }
      });
    }
  })
});

//****************************************************************** DELETE USER ACCOUNT ******************************************************************

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



