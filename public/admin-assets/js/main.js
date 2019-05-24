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
     $(".top-header-links li .profile-icon").click(function(){
         $(".profile-dropdown").toggleClass("profile-dropdown-active");
     });
     $(".top-header-links li .notfy-icon").click(function(){
         $(".notfication-dropdown").toggleClass("profile-dropdown-active");
     });
     $(".top-header-links li .mess-icon").click(function(){
         $(".message-dropdown").toggleClass("profile-dropdown-active");
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
    $(".providers-select2").select2();
    $(".users-select2").select2();
    $(".deliveries-select2").select2();
    $('#tags').select2({
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
  $body.on('click', '.files-wr', function() {
    var wrapFiles = $(this),
      newFileInput;

    countFiles = wrapFiles.data('count-files') + 1;
    fileLabel = wrapFiles.data('label-text');
    if(fileLabel)
    {
        var label = fileLabel;
    }else{
        var label = "Attach file";
    }
    wrapFiles.data('count-files', countFiles);

    newFileInput = '<div class="one-file"><label for="file-' + countFiles + '">'+ label +'</label>' +
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

/*CKEDITOR
==============================*/
// $(document).ready(function() {
//      "use strict";
//     CKEDITOR.replace( 'editor1' );
// });

/*Validation form
==============================*/
$(document).ready(function() {
     "use strict";
    $('#form').form({
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

    $('#create-admin').form({
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
            minLength: {
                 identifier  : 'minLength',
                 rules: [
                     {
                         type   : 'minLength[8]',
                         prompt : 'Please enter at least 8 characters'
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
                    },
                    {
                        type: 'minLength[8]',
                        prompt: 'Password can\'t be less than 8 characters'
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
            // e.preventDefault();
            // e.stopPropagation();
            this.submit();
        }
    });
    
    $('#create-provider').form({
        fields: {
            fname: {
                identifier: 'fname',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'First Name can not be empty'
                    }
                ]
            },
            sname: {
                identifier: 'sname',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'Second Name can not be empty'
                    }
                ]
            },
            tname: {
                identifier: 'tname',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'Third Name can not be empty'
                    }
                ]
            },
            lname: {
                identifier: 'lname',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'Last Name can not be empty'
                    }
                ]
            },
            bname: {
                identifier: 'bname',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'Brand Name can not be empty'
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
            },
            phone: {
                identifier: 'phone',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'Phone number can not be empty'
                    }
                ]
            },
            country_code: {
                identifier: 'country_code',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'Phone country code can not be empty'
                    }
                ]
            },
            minLength: {
                 identifier  : 'minLength',
                 rules: [
                     {
                         type   : 'minLength[8]',
                         prompt : 'Please enter at least 8 characters'
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
                    },
                    {
                        type: 'minLength[8]',
                        prompt: 'Password can\'t be less than 8 characters'
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
            countries: {
                identifier  : 'countries',
                rules: [
                    {
                        type   : 'empty',
                        prompt : 'Country can not be empty'
                    }
                ]
            },
            cities: {
                identifier  : 'cities',
                rules: [
                    {
                        type   : 'empty',
                        prompt : 'City can not be empty'
                    }
                ]
            },
            address: {
                identifier  : 'address',
                rules: [
                    {
                        type   : 'empty',
                        prompt : 'Address can not be empty'
                    }
                ]
            },
            categories: {
                identifier  : 'categories',
                rules: [
                    {
                        type   : 'empty',
                        prompt : 'categories can not be empty'
                    }
                ]
            }
        },
        onSuccess: function(e) { 
            // e.preventDefault();
            // e.stopPropagation();
            this.submit();
        }
    });
    
    $('#create-delivery').form({
        fields: {
            fname: {
                identifier: 'fname',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'First Name can not be empty'
                    }
                ]
            },
            sname: {
                identifier: 'sname',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'Second Name can not be empty'
                    }
                ]
            },
            tname: {
                identifier: 'tname',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'Third Name can not be empty'
                    }
                ]
            },
            lname: {
                identifier: 'lname',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'Last Name can not be empty'
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
            },
            phone: {
                identifier: 'phone',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'Phone number can not be empty'
                    }
                ]
            },
            country_code: {
                identifier: 'country_code',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'Phone country code can not be empty'
                    }
                ]
            },
            password: {
                identifier: 'password',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'Please enter a valid password '
                    },
                    {
                        type: 'minLength[8]',
                        prompt: 'Password can\'t be less than 8 characters'
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
            countries: {
                identifier  : 'countries',
                rules: [
                    {
                        type   : 'empty',
                        prompt : 'Country can not be empty'
                    }
                ]
            },
            cities: {
                identifier  : 'cities',
                rules: [
                    {
                        type   : 'empty',
                        prompt : 'City can not be empty'
                    }
                ]
            },
            address: {
                identifier  : 'address',
                rules: [
                    {
                        type   : 'empty',
                        prompt : 'Address can not be empty'
                    }
                ]
            },
            car_type: {
                identifier : 'car_type',
                rules: [
                    {
                        type    : 'empty',
                        prompt  : 'Car type can not be empty'
                    }
                ]
            }
        },
        onSuccess: function(e) { 
            // e.preventDefault();
            // e.stopPropagation();
            this.submit();
        }
    });

    $('#provider-properties').form({
        fields: {
            from: {
                identifier: 'from',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'From filed at Receiving orders times section can not be empty'
                    }
                ]
            },
            to: {
                identifier: 'to',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'To filed at Receiving orders times section can not be empty'
                    }
                ]
            },
            deliveries: {
                identifier: 'deliveries',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'You must select at least one delivery method'
                    }
                ]
            },
            avail_date: {
                identifier: 'avail_date',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'You must select to date'
                    }
                ]
            },
            price: {
              identifier: 'price',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'Delivery price can\'t be empty'
                    }
                ]
            }
        },
        onSuccess: function(e) { 
            // e.preventDefault();
            // e.stopPropagation();
            this.submit();
        }
    });

    $('#user-form').form({
        fields: {
            full_name: {
                identifier: 'full_name',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'full name filed can not be empty'
                    }
                ]
            },
            email: {
                identifier: 'email',
                rules: [
                    {
                        type: 'email',
                        prompt: 'Invalid e-mail address'
                    },
                    {
                      type: 'empty',
                      prompt: 'E-mail filed can not be empty'
                    }
                ]
            },
            country_code: {
                identifier: 'country_code',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'Country code number filed can not be empty'
                    },
                    {
                        type: 'integer',
                        prompt: 'Country code accepted only numbers'
                    },
                ]
            },
            phone: {
                identifier: 'phone',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'Phone number filed can not be empty'
                    }
                ]
            },
            country: {
                identifier: 'country',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'Country filed can not be empty'
                    }
                ]
            },
            city: {
                identifier: 'city',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'City filed can not be empty'
                    }
                ]
            },
            password: {
                identifier: 'password',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'Please enter a valid password'
                    },
                    {
                        type: 'minLength[8]',
                        prompt: 'Password can\'t be less than 8 characters'
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
            passwordcon2: {
                identifier  : 'passwordcon2',
                rules: [
                    {
                        type   : 'match[password2]',
                        prompt : 'password do not match'
                    }
                ]
            },
        },
        onSuccess: function(e) { 
            // e.preventDefault();
            // e.stopPropagation();
            this.submit();
        }
    });
    $('#create-invoice').form({
        fields: {
            name: {
                identifier: 'name',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'Name can not be empty'
                    }
                ]
            },
            phone: {
                identifier: 'phone',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'Phone number can not be empty'
                    }
                ]
            },
            value: {
                identifier: 'value',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'Invoice value can not be empty'
                    }
                ]
            },
            type: {
                identifier  : 'type',
                rules: [
                    {
                        type   : 'empty',
                        prompt : 'Invoice type can not be empty'
                    }
                ]
            },
            desc: {
                identifier  : 'desc',
                rules: [
                    {
                        type   : 'empty',
                        prompt : 'Invoice description can not be empty'
                    }
                ]
            }
        },
        onSuccess: function(e) { 
            // e.preventDefault();
            // e.stopPropagation();
            this.submit();
        }
    });

    $('#setting-form').form({
        fields: {
            hours: {
                identifier: 'hours',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'time in hours can not be empty'
                    },
                    {
                        type: 'number',
                        prompt: 'time in hours must be a number'
                    }
                ]
            },

            minutes: {
                identifier: 'minutes',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'time in minutes can not be empty'
                    },
                    {
                        type: 'number',
                        prompt: 'time in minutes must be a number'
                    }
                ]
            },

            provider: {
                identifier: 'provider',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'provider percentage can not be empty'
                    },
                    {
                        type: 'number',
                        prompt: 'provider percentage must be a number'
                    }
                ]
            },

            delivery: {
                identifier: 'delivery',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'delivery percentage can not be empty'
                    },
                    {
                        type: 'number',
                        prompt: 'delivery percentage must be a number'
                    }
                ]
            },

            marketer: {
                identifier: 'marketer',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'marketer percentage can not be empty'
                    },
                    {
                        type: 'number',
                        prompt: 'marketer percentage must be a number'
                    }
                ]
            },

            kilo: {
                identifier: 'kilo',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'kilo price can not be empty'
                    },
                    {
                        type: 'number',
                        prompt: 'kilo price must be a number'
                    }
                ]
            },

            type: {
                identifier: 'type',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'Please select invitation type'
                    }
                ]
            },

            inPoints: {
                identifier: 'inPoints',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'inviter points can not be empty'
                    },
                    {
                        type: 'number',
                        prompt: 'inviter points must be a number'
                    }
                ]
            },

            outPoints: {
                identifier: 'outPoints',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'invited points can not be empty'
                    },
                    {
                        type: 'number',
                        prompt: 'invited points must be a number'
                    }
                ]
            }
        },
        onSuccess: function(e) { 
            // e.preventDefault();
            // e.stopPropagation();
            this.submit();
        }
    });

    $('#create-category').form({
        fields: {
            en_name: {
                identifier: 'en_name',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'English name can not be empty'
                    }
                ]
            },

            ar_name: {
                identifier: 'ar_name',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'Arabic name can not be empty'
                    }
                ]
            }
        },
        onSuccess: function(e) { 
            // e.preventDefault();
            // e.stopPropagation();
            this.submit();
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




























