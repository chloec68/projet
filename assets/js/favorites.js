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
