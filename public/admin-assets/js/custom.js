function getCountryCities(url, country_id, lang, elem, code, phone, flag){
	var posted = {'country_id': country_id, 'lang': lang, 'flag':flag};
	$.ajax({
        url:url,
        type:"POST",
        dataType:"JSON",
        data:posted,
        scriptCharset:"application/x-www-form-urlencoded; charset=UTF-8",
        success: function(result){
	        elem.html(result.select);
            code.val(result.country_code);
            // elem.find('.city').select2();
            phone.val("");
        },
        error: function(){
          alert("Something is wrong, please try again later");
        }
    });
}