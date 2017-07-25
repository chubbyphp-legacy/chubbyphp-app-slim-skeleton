(function($){
    var selectSelector = 'select:not([data-ajax])';
    var ajaxSelectSelector = 'select[data-ajax]';

    var addAjaxSelect = function($selector){
        $selector.select2({
            multiple: $selector.attr('multiple'),
            ajax: {
                url: $selector.attr('data-route'),
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            minimumInputLength: 1,
            templateSelection: function formatRepoSelection (result) {
                var $formGroup = $selector.closest('div[class="form-groups"]');
                if($formGroup.length == 1) {
                    for (var prop in result) {
                        $formGroup.find('input[id*="' + prop + '"]').val(result[prop]);
                    }
                }

                return result.text;
            }
        });
    };
    var addSelect = function($selector) {
        $selector.select2({
            multiple: $selector.attr('multiple')
        });
    };
    $(document).ready(function(){
        $(selectSelector).each(function(i, element){
            addSelect($(element));
        });
        $(ajaxSelectSelector).each(function(i, element){
            addAjaxSelect($(element));
        });
    });
})(jQuery);
