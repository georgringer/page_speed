define(['jquery', 'TYPO3/CMS/Backend/Tooltip'], function ($) {


    $(document).ready(function () {
        var calc = 1.422222222;

        jQuery(".single-rule .title").click(function () {
            jQuery(this).children('i').toggleClass('fa-minus');
            jQuery(this).next('.description').toggle();
        });

        jQuery(".screenshot-toggle").click(function () {
            if (jQuery(this).hasClass('active')) {
                jQuery('.snapshot-rect').remove();
            } else {
                addOverlay(this, 'rects', '');
                addOverlay(this, 'rects-secondary', 'secondary');
            }
            jQuery(this).toggleClass('active');

        });

        function addOverlay(link, attribute, additionalClass) {
            jQuery('.snapshot-rect').remove();
            var areas = jQuery(link).data(attribute).split(';');
            for (var i = 0; i < areas.length; i++) {
                var item = areas[i].split(',');
                jQuery("<div>")
                    .addClass('snapshot-rect ' + additionalClass)
                    .css({
                        left: item[0] / calc,
                        top: item[1] / calc,
                        width: item[2] / calc,
                        height: item[3] / calc
                    })
                    .insertAfter(jQuery('#screenshot-' + jQuery(link).data('strategy')));
            }
        }
    });

});
