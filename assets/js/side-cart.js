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
        data.forEach(item => {
          htmlContent += 
            `<article class="sideCart-item side-cart-product">
                <div class="item-containers">
                    <div class="item-container">
                      <img class="product-pic" src="${item.picture}" alt="Photo du produit">
                      <p><span class="productName">${item.productName} -</span>
                      <span>${item.type ? item.type : item.gender} -</span>
                      <span>${item.volume ? item.volume : item.size}</span> </p>
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
