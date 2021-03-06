(function ($) {
  $(document).on("click", ".activephone-open", function (e) {
    e.preventDefault();
    let classId = document.querySelector('body').className.split(' ').filter(item => item.indexOf('id')>0); //get ID post from class body
    let postId = parseInt(classId[0].match(/\d+/)); 
    let cookie = getCookie('PHPSESSID');
    $('.activephone-open').removeClass('activephone-open')
    $.ajax({
      url: jobsearch_plugin_vars.ajax_url,
      type: "POST",
      data: {
        action: "get_phone_number_ajax",
        postId,
        cookie,
      },
      success: function(response){
        if (response=='') {
          $('.activephone-btn').text('Номер не доступен');
        }else{
          $('.activephone-btn').text(response);
          $('.activephone-btn').attr('href', 'tel:'+response)
        }
      },
      error: function(e){
        $('.activephone-btn').text('Номер не доступен');
      }
    })
  });

  

})(jQuery);

//Get session from cookie
function getCookie(name) {
	var matches = document.cookie.match(new RegExp("(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"));
	return matches ? decodeURIComponent(matches[1]) : undefined;
}
