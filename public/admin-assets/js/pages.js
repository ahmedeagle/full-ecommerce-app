/* Home Section
=============================*/
$(document).ready(function () {
    
    "use strict";
    
    function headerSize() {
        
        var fullH = $(window).innerHeight(),
            
            halfH = $(window).innerHeight() / 2,
            
            div = $(".center-height"),
            
            divHeight = $(".center-height").outerHeight(),
            
            imageHeight = $(".bottom-height").outerHeight(),
            
            divHalfHeight = divHeight / 2,
            
            centerDiv = halfH - divHalfHeight;
        
        $("#welcome-home").css({
            
            height: fullH
            
        });
        $(".center-height").css({
            
            top: centerDiv
            
        });
        $(document).scroll(function (e) {
            
            var scrollPercent = (divHeight - window.scrollY) / divHeight;
            
            if (scrollPercent >= 0) {
                
                div.css('opacity', scrollPercent);
                
            }
            
        });
        
    }
    headerSize();
    
    $(window).resize(function () {
        
        headerSize();
        
    });
    
});

/*Lighbox text
=========================*/
$(document).ready(function () {
    "use strict";
    $('.popup-text').magnificPopup({
        removalDelay: 500,
        closeBtnInside: true,
        callbacks: {
            beforeOpen: function () {
                this.st.mainClass = this.st.el.attr('data-effect');
            }
        },
        midClick: true
    });
});
