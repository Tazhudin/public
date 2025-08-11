document.addEventListener("DOMContentLoaded", readText);
// Params
var mainSliderSelector = '.main-slider',
    navSliderSelector = '.nav-slider',
    interleaveOffset = 1;

// Main Slider
var mainSliderOptions = {
      loop: true,
      speed:2000,
      autoplay:{
        delay:50000
      },
      loopAdditionalSlides: 10,
      grabCursor: true,
      watchSlidesProgress: true,
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },
      on: {
        init: function(){
          this.autoplay.stop();
        },
        imagesReady: function(){
          this.el.classList.remove('loading');
          this.autoplay.start();

        },
        slideChangeTransitionEnd: function(){

            var swiper = this,
              captions = swiper.el.querySelectorAll('.caption');
            let text = swiper.el.querySelector('.swiper-slide-duplicate-active .caption');
            readText(text);
        },
        progress: function(){
          var swiper = this;
          for (var i = 0; i < swiper.slides.length; i++) {
            var slideProgress = swiper.slides[i].progress,
                innerOffset = swiper.width * interleaveOffset,
                innerTranslate = slideProgress * innerOffset;
            swiper.slides[i].querySelector(".slide-bgimg").style.transform =
              "translate3d(" + innerTranslate + "px, 0, 0)";
          }
        },
        touchStart: function() {
          var swiper = this;
          for (var i = 0; i < swiper.slides.length; i++) {
            swiper.slides[i].style.transition = "";
          }
        },
        setTransition: function(speed) {
          var swiper = this;
          for (var i = 0; i < swiper.slides.length; i++) {
            swiper.slides[i].style.transition = speed + "ms";
            swiper.slides[i].querySelector(".slide-bgimg").style.transition =
              speed + "ms";
          }
        }
      }
    };
var mainSlider = new Swiper(mainSliderSelector, mainSliderOptions);

// Navigation Slider
var navSliderOptions = {
      loop: true,
      loopAdditionalSlides: 10,
      speed:1000,
      spaceBetween: 5,
      slidesPerView: 5,
      centeredSlides : true,
      touchRatio: 0.2,
      slideToClickedSlide: true,
      direction: 'vertical',
      on: {
        imagesReady: function(){
          this.el.classList.remove('loading');
        },
        click: function(){
          mainSlider.autoplay.stop();
        }
      }
    };
// var navSlider = new Swiper(navSliderSelector, navSliderOptions);
//
// // Matching sliders
// mainSlider.controller.control = navSlider;
// navSlider.controller.control = mainSlider;


var textIntervalTimer = null;


function readText() {

    let text='',
        textBlock = document.querySelector('.caption'),
        textBlockForAnimation = document.querySelector('.textAnimation'),
        originTextBlock = document.querySelector('.text');

    textBlock.setAttribute('style','display: block');

    textBlockForAnimation.innerText = '';
    text = originTextBlock.innerText;
    originTextBlock.setAttribute('style','display: none');

    clearInterval(textIntervalTimer);

    var i = 0;
    var timer = setInterval(function() {
        if (i==20) {
            textBlockForAnimation.setAttribute('style', 'padding:1%;');
        }
        var texts = document.createTextNode(text[i]);
        var span = document.createElement('span');
        span.appendChild(texts);
        span.classList.add("wave");
        textBlockForAnimation.appendChild(span);
        i++;
        if (i >= text.length) {
            clearInterval(timer);

        }
    }, 55);
    // textBlockForAnimation.innerText = '';
    // originTextBlock.setAttribute('style','display: block');


    textIntervalTimer = timer;

}

function goToNextPage()
{
    var url = document.getElementById('id_Элемента');
    document.location.href = url.value;
}