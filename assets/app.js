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

