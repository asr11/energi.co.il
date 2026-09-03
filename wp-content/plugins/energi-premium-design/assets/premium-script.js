jQuery(document).ready(function($) {
    // Add fade-in-up class to posts for sequential animation
    $('.wp-block-latest-posts li').each(function(index) {
        $(this).css('animation-delay', (index * 0.1) + 's');
        $(this).addClass('fade-in-up');
    });

    // Smooth scroll to top
    $('footer a[href="#"]').click(function(e) {
        e.preventDefault();
        $('html, body').animate({scrollTop: 0}, 'smooth');
    });
});
