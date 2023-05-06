var app = {
    initToggle:function(){
        jQuery('.toggle-nav').click(function(e) {
            jQuery('.nav-toggle-wrapper').slideToggle(500);
     
            e.preventDefault();
        });

        jQuery('.nav-toggle-menu li').click(function(e){
            var $this = jQuery(this);
            var submenu = $this.find('.sub-menu');
            
            if( submenu.length > 0 && !$this.hasClass('open') ){
                submenu.fadeIn(500);
                $this.addClass('open');
                e.preventDefault();
            }
            
        })
        
        jQuery('.nav-toggle-menu li a').click(function(e){
            var $this = jQuery(this);
            var parent = jQuery(this).parent();
            var submenu = $this.siblings('.sub-menu');
            
            if( submenu.length > 0 && !parent.hasClass('open') ){
                submenu.fadeIn(500);
                parent.addClass('open');
                e.preventDefault();
            }
            
        })

        jQuery('.nav-toggle-menu li').click(function(e){
            var $this = jQuery(this);
            var submenu = $this.find('.grandchild');
            
            if( submenu.length > 0 && !$this.hasClass('open') ){
                submenu.fadeIn(500);
                $this.addClass('open');
                e.preventDefault();
            }
            
        })
    },

    blog:function(){
        jQuery('.blog-list').slick({
            slidesToShow: 3,
            autoplay: true,
            autoplaySpeed: 4000,
            dots: false,
            prevArrow: false,
            nextArrow: false,
            responsive: [
                {
                    breakpoint: 992,
                    settings: {
                        variableWidth: false,
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        swipe: true,
                    }
                }
            ]
       });

        jQuery('.open-popup-link').magnificPopup({
            type:'inline',
            midClick: true // Allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source in href.
        });
    },

    initAnnouncementflickity:function(){
        jQuery('.announcement-carousel').flickity();
    },
    initPopup:function(){
        jQuery('.open-popup-qr').magnificPopup({
            type:'inline',
            midClick: true // allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source.
        });

        setTimeout(function(){
           jQuery('.open-popup-qr').click();
        },5000)
        

    }
}

jQuery(document).ready(function(){
    app.initToggle();
    app.initAnnouncementflickity();
    app.blog();
    app.initPopup();
});