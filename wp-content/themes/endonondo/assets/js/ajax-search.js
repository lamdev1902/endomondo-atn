jQuery(document).ready(function ($) {

    let isSearch = false;


    function searchExercise(searchQuery) {
        if (isSearch) return;
        isSearch = true;

        if (!searchQuery) return;
        $.ajax({
            url: exerciseSearch.ajaxurl,
            type: 'POST',
            data: {
                action: 'search_exercise',
                nonce: exerciseSearch.nonce,
                data: searchQuery
            },
            beforeSend: function () {
                $('.ex-section').empty();
                $('.ex,#loader').show();
                $('.loader-rs').hide();
            },
            success: function (response) {
                if (response.success) {
                    if (!response.data) {
                        $('.loader-rs').show();
                        $('.ex-section').removeClass('grid-ex');
                    } else {
                        $('.loader-rs').hide();
                        $('.ex-section').addClass('grid-ex');
                        $('.ex-section').html(response.data);
                    }
                }

                $('.ex,#loader').hide();
            },
            error: function (xhr, status, error) {
                $('.loader-rs').show();
                $('.ex-section').removeClass('grid-ex');
            },
            complete: function () {
                isSearch = false;
            }
        });
    }

    $(document).on('keypress', function (e) {
        if (e.which === 13) {

            var pr = collectData();

            if (Object.keys(pr).length === 0) {
                return false;
            }

            searchExercise(pr);

            e.preventDefault();
        }
    });

    $('.exIcon, .applyAction').after().on('click', function () {
        var inp = collectData();

        if (Object.keys(inp).length === 0) {
            return false;
        }

        searchExercise(inp);

    });

    $('.clear').on('click', function () {
        clearFilter();
    })

    function clearFilter() {
        $('#exerciseSearch').val('');

        $('#mt').val([]);
        $('#eq').val([]);

        $("#mt, #eq").multiselect('reset')
    }

    function collectData() {

        var exerciseSearchValue = $('#exerciseSearch').val() || '';

        var mt = $('#mt').val() || [];
        var eq = $('#eq').val() || [];

        var jsonData = {};

        if (!exerciseSearchValue && mt.length === 0 && eq.length === 0) {
            return {};
        }

        jsonData.name = exerciseSearchValue;
        jsonData.mt = mt;
        jsonData.eq = eq;

        return jsonData;
    }

});
