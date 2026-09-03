<form role="search" method="get" id="searchform" class="hub-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
  <input type="text" value="<?php echo get_search_query(); ?>" name="s" id="s" placeholder="חיפוש בפורטל אנרגי..." required />
  <button type="submit" id="searchsubmit" class="hub-search-btn">
    <span>חיפוש</span>
  </button>
</form>
