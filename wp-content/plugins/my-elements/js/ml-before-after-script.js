jQuery(document).ready(function ($) {
    $('.ml-before-after-widget').each(function () {
        const container = $(this);
        const before = container.find('.before-layer');
        const handle = container.find('.slider-handle');
        const divider = container.find('.slider-divider');
        const labelBefore = container.find('.label-before');
        const labelAfter = container.find('.label-after');
        const defaultPercent = parseFloat(container.data('default-percent')) / 100;

        const containerWidth = container.width();
        const setPosition = (xPercent) => {
            const x = containerWidth * xPercent;
            before.css('clip', `rect(auto, ${x}px, auto, auto)`);
            handle.css('left', `${x}px`);
            divider.css('left', `${x}px`);
            labelBefore.css('left', `10px`);
            labelAfter.css('right', `10px`);
        };

        setPosition(defaultPercent);

        container.on('mousedown touchstart', function (e) {
            e.preventDefault();
            $(document).on('mousemove touchmove', drag).on('mouseup touchend', () => {
                $(document).off('mousemove touchmove', drag);
            });
        });

        function drag(e) {
            const x = (e.pageX || e.originalEvent.touches[0].pageX) - container.offset().left;
            const percent = Math.max(0, Math.min(1, x / container.width()));
            setPosition(percent);
        }
    });
});
