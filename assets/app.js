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


  // SCROLL UP BUTTON
//   document.addEventListener('scroll',function(){
//     if(window.scrollY > 300){
//     document.getElementById('top-button').style.display = 'block';
//    }else{
//      document.getElementById('top-button').style.display = 'none';
//  }})



  // Fonction qui gère l'affichage du bouton
  function buttonVisibility() {
    if (window.scrollY > 300) {
      document.getElementById('top-button').style.display = 'block';
    } else {
      document.getElementById('top-button').style.display = 'none';
    }
  }

  // Écouteur d'événement scroll
  document.addEventListener('scroll', buttonVisibility);

  // Vérifier l'état au moment du chargement de la page
  window.addEventListener('load', buttonVisibility);
