jQuery(document).ready(function ($) {
    $('#seeCmt').on('click', function (e) {
        e.preventDefault();

        let button = $(this);
        let postId = button.data('post-id');
        let page = button.data('page');

        const displayedCommentIds = [];
        jQuery('.commentlist li.comment').each(function () {
            const commentId = jQuery(this).attr('id').replace('comment-', '');
            displayedCommentIds.push(commentId);
        });

        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'load_more_comments',
                post_id: postId,
                page: page,
                displayed_ids: displayedCommentIds
            },
            beforeSend: function () {
                button.text('Loading...');
            },
            success: function (response) {
                if (response.success) {
                    $('.commentlist').append(response.data);
                    button.data('page', page + 1);
                    button.text('See all comments');
                }
                button.remove();
            },
            error: function () {
                button.text('Error loading comments');
            }
        });
    });
});
