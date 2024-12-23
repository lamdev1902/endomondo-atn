jQuery(function($) {
    var canBeLoaded = true, 
        bottomOffset = $(window).height() * 1.5, 
        lastScrollTop = 0; 

    $(document).on('scroll', function() {
        var scrollTop = $(document).scrollTop();
        var scrollPosition = scrollTop + $(window).height();
        var documentHeight = $(document).height();

        if (scrollPosition > documentHeight - bottomOffset && canBeLoaded) {
            if (scrollTop > lastScrollTop) {
                var data = {
                    'action': 'load_more_posts',
                    'query_vars': infinite_scroll_params.query_vars,
                    'post__not_in': infinite_scroll_params.query_vars.post__not_in,
                    'page': infinite_scroll_params.current_page,
                    'type': 1
                };

                $.ajax({
                    url: infinite_scroll_params.ajaxurl,
                    data: data,
                    type: 'POST',
                    beforeSend: function(xhr) {
                        canBeLoaded = false; 
                        $('#loading').show(); 
                    },
                    success: function(response) {
                        if (response) {
                            $('.cate-list').append(response); 
                            infinite_scroll_params.current_page++;
                            canBeLoaded = true; 
                            $('#loading').hide(); 
                        } else {
                            $('#loading').hide(); 
                        }
                    }
                });
            }
        }

        lastScrollTop = scrollTop; // Cập nhật vị trí cuộn cuối cùng
    });
});
