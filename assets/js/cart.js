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
const addToCartButtons = document.querySelectorAll(".add-to-cart");
// const stockElements = document.querySelectorAll('.stock-info');

// stockElements.forEach(element => {
//   let stockQuantity = parseInt(element.getAttribute('data-stock'));
//     if (stockQuantity > 10){
    if(addToCartButtons){
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
            updateCart(product,quantity); 
          }
        });
      });
    }

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
function updateCart(product,quantity){

  const nbItemsElements = document.querySelectorAll('.nbItems');
  
  // envoie une requête AJAX de type POST
  fetch(`/cart/update/${product}`,{
    // définis la méthode HTTP utilisée (POST)
    method : 'POST',
    // définis les en-tête -> indique le format des données envoyées 
    headers :{
        'Content-Type':'application/json'
    } ,
    // définis le corps de la requête 
    body: JSON.stringify({
      //le corps de la requête contient les variables product et quantity 
      product:product,
      quantity:quantity,
    })
  })
  //récupère la réponse JSON
  .then(response => response.json())
    //lorsque la réponsr JSON est disponible, on traite les données contenues dans la réponse
    .then(data => {
      // si la valeur du nombre d'articles est strictement supérieur à 1
      if(data.nbItems > 1){
        //MISE A JOUR DES ELEMENTS TEXTUELS (relatifs au nombre d'articles)
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
  const totalPriceElement = document.querySelector('.total-price');
  if(cartItems && cartItems.length>0){
    cartItems.forEach(item => {
      let input = item.querySelector('input[cart-product]');
      let quantity = parseInt(input.value);
      let price = parseFloat(item.querySelector('.product-price').textContent);
      totalPrice += price*quantity;
      totalPriceElement.textContent = totalPrice.toFixed(2) + " €";
      })
  }else{
    if(totalPriceElement){
      document.querySelector('.total-price').textContent = "0 €";
    }
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
              productTable.remove();   
            }
          }else{
            console.error('erreur lors de la suppression');
          }
          updatePriceTotal();
        })
        .catch(error => console.error('Erreur:', error));
    });
  });