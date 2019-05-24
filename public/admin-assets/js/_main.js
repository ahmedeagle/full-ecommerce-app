/*Side Menu
==============================*/
$(document).ready(function() {
    "use strict";
    $(".side-menu-links .sub-menu > a").click(function(e) {
    $(".side-menu-links ul").slideUp(),
        $(this).next().is(":visible") || $(this).next().slideDown(),
    e.stopPropagation()
    })
 });

/* Profile
==============================*/
$(document).ready(function() {
     "use strict";
     $(".top-header-links li .prof-icon").click(function(){
         $(".profile-dropdown").addClass("top-links-dropdown-active");
     });
     $(".profile-dropdown").mouseleave(function(){
         $(this).removeClass("top-links-dropdown-active");
     });
    
    
    $(".top-header-links li .notify-icon").click(function(){
         $(".notfication-dropdown").addClass("top-links-dropdown-active");
     });
     $(".notfication-dropdown").mouseleave(function(){
         $(this).removeClass("top-links-dropdown-active");
     });
    
        
    
    $(".top-header-links li .messg-icon").click(function(){
         $(".message-dropdown").addClass("top-links-dropdown-active");
     });    
     $(".message-dropdown").mouseleave(function(){
         $(this).removeClass("top-links-dropdown-active");
     });
   
    
 });

/* Toggle Icon
==============================*/
$(document).ready(function() {
     "use strict";
     $(".toggle-icon").click(function(){
         $(".side-menu").toggleClass("side-menu-move");
         $(".page-content").toggleClass("page-content-move");
         $(".top-header").toggleClass("page-content-move");
     });
    $(".lock-icon").click(function(){
        $(".lock-screen").addClass("lock-screen-apper");
    });
     $(".open-lock").click(function(){
        $(".lock-screen").removeClass("lock-screen-apper");
     });
 });

/* tooltip
==============================*/
$(document).ready(function() {
     "use strict";
     $('[data-toggle="tooltip"]').tooltip(); 
    $('a[title]').tooltip();
 });

/* DataTable
==============================*/
 $(document).ready(function() {
     "use strict";
     $('#datatable').DataTable();
 });

/* DataTable Buttons
==============================*/
$(document).ready(function() {
    "use strict";
    $('#datatable-buttons').DataTable({
       dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
 });

/* DataTable Icon
==============================*/
$(document).ready(function() {
     "use strict";
     $('#datatable-icon').DataTable({
         dom: 'Bfrtip',
         buttons: [
            {
                extend:    'copyHtml5',
                text:      '<i class="fa fa-files-o"></i>',
                titleAttr: 'Copy'
            },
            {
                extend:    'excelHtml5',
                text:      '<i class="fa fa-file-excel-o"></i>',
                titleAttr: 'Excel'
            },
            {
                extend:    'csvHtml5',
                text:      '<i class="fa fa-file-text-o"></i>',
                titleAttr: 'CSV'
            },
            {
                extend:    'pdfHtml5',
                text:      '<i class="fa fa-file-pdf-o"></i>',
                titleAttr: 'PDF'
            }
        ]
     } );
 });

/* Date Picker
=============================*/
$(document).ready(function () {
    "use strict";
    $('.form_datetime').datetimepicker({
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		forceParse: 0,
        showMeridian: 1
    });
    $('.form_date').datetimepicker({
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		minView: 2,
		forceParse: 0
    });
	$('.form_time').datetimepicker({
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 1,
		minView: 0,
		maxView: 1,
		forceParse: 0
    });
});

/* color Picker
=============================*/
$(document).ready(function () {
    
    "use strict";
    $('#colorpicker').minicolors();  
 });   

/*Select With Search
============================*/
$(document).ready(function () {
    "use strict";
    $(".js-example-basic-single").select2();
    $('.tags').select2({
        tags: true,
        tokenSeparators: [',']
    });
 });   

/*Upload-Btn
==============================*/
$(function() {
  var countFiles = 5,
    $body = $('body'),
    typeFileArea = ['txt', 'doc', 'docx', 'ods'],
    coutnTypeFiles = typeFileArea.length;

  //create new element
  $body.on('click', '.files-wr label', function() {
    var wrapFiles = $('.files-wr'),
      newFileInput;

    countFiles = wrapFiles.data('count-files') + 1;
    wrapFiles.data('count-files', countFiles);

    newFileInput = '<div class="one-file"><label for="file-' + countFiles + '">Attach file</label>' +
      '<input type="file" name="file-' + countFiles + '" id="file-' + countFiles + '"><div class="file-item hide-btn">' +
      '<span class="file-name"></span><span class="btn btn-del-file">x</span></div></div>';
    wrapFiles.prepend(newFileInput);
  });

  //show text file and check type file
  $body.on('change', 'input[type="file"]', function() {
    var $this = $(this),
      valText = $this.val(),
      fileName = valText.split(/(\\|\/)/g).pop(),
      fileItem = $this.siblings('.file-item'),
      beginSlice = fileName.lastIndexOf('.') + 1,
      typeFile = fileName.slice(beginSlice);

    fileItem.find('.file-name').text(fileName);
    if (valText != '') {
      fileItem.removeClass('hide-btn');

      for (var i = 0; i < coutnTypeFiles; i++) {

        if (typeFile == typeFileArea[i]) {
          $this.parent().addClass('has-mach');
        }
      }
    } else {
      fileItem.addClass('hide-btn');
    }

    if (!$this.parent().hasClass('has-mach')) {
      $this.parent().addClass('error');
    }
  });

  //remove file
  $body.on('click', '.btn-del-file', function() {
    var elem = $(this).closest('.one-file');
    elem.fadeOut(400);
    setTimeout(function() {
      elem.remove();
    }, 400);
  });
});

/*Valdation form
==============================*/
$(document).ready(function() {
     "use strict";
    $('form').form({
        fields: {
            name: {
                identifier: 'name',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'name can not be empty'
                    }
                ]
            },
            gender: {
                 identifier: 'gender',
                 rules: [
                     {
                         type   : 'empty',
                         prompt : 'Please select a gender'
                     }
                 ]
             } ,
            url: {
                 identifier  : 'url',
                 rules: [
                     {
                         type   : 'url',
                         prompt : 'Please enter a valid email url'
                     }
                 ]
             },
            minLength: {
                 identifier  : 'minLength',
                 rules: [
                     {
                         type   : 'minLength[100]',
                         prompt : 'Please enter at least 100 characters'
                     }
                 ]
             },
            maxLength: {
                identifier  : 'maxLength',
                rules: [
                    {
                        type   : 'maxLength[100]',
                        prompt : 'Please enter at most 100 characters'
                    }
                ]
            },
            password: {
                identifier: 'password',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'Please enter a valid password '
                    }
                ]
            },
            passwordcon: {
                identifier  : 'passwordcon',
                rules: [
                    {
                        type   : 'match[password]',
                        prompt : 'password do not match'
                    }
                ]
            },
            email: {
                identifier: 'email',
                rules: [
                    {
                        type: 'email',
                        prompt: 'Please enter a valid email address.'
                    }
                ]
            }
        },
        onSuccess: function(e) { 
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    });
});

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

/* Gallery
=======================================*/
$(document).ready(function () {
    "use strict";
   $('.img-popup-link').magnificPopup({
       type: 'image'
   });
    
});

/* Timer Counter
===============================*/ 
$(document).ready(function () {
    "use strict";
    var v_count = '0';  
    $('.timer').each(function () {
        var imagePos = $(this).offset().top,
            topOfWindow = $(window).scrollTop();
        if (imagePos > topOfWindow + 0 && v_count === '0') {
            $(function ($) {
                function count(options) {
                    v_count = '1';
                    options = $.extend({}, options || {}, $(this).data('countToOptions') || {});
                    $(this).countTo(options);
                }
                
                // start all the timers
                $('.timer').each(count);
            });
        }
    });
    
});











