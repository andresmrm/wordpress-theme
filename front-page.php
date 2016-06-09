<?php

/**
 * The template for displaying the front page of the site.
 * 
 * @package OKFNWP
 */
get_header();

?>
<div class="main col-md-8">
  <?php

  // This is currently the most flexible approach for the Featured post category.
  // Get the category object by its path/slug.

  if (!isset($featured_cat)) {
    $featured_cat = get_category_by_path('featured');
  }

  // Check if the Home page is paginated and skip the featured post rendering if
  // any page after the first one is loaded

  if (isset($paged, $featured_cat) && $paged < 2):

    $featured = new WP_Query([
      'cat' => $featured_cat->term_id,
      'posts_per_page' => 1
    ]);

    if ($featured->have_posts()):

      while ($featured->have_posts()) : $featured->the_post();

        get_template_part('content', 'featured');

      endwhile;

      wp_reset_postdata();

    endif;

  else:

    ?>
    <div class="alert alert-warning"><p>
        <?php _e("Sorry, the Featured post category is not available and this content cannot be rendered.", 'okfnwp'); ?>
      </p></div>
  <?php

  endif;

  // Get the most recent post for each of the featured categories defined in
  // functions.php via okfn_global_vars().

  global $frontpage_categories;
  global $rendered_posts_ids;

  if ($frontpage_categories):
    
    ?>
    <div class="row">
      <?php

      $args = [
        'category__in' => $frontpage_categories,
        'posts_per_page' => 10,
        'post_status' => 'publish', // Required! so that no Private posts are listed for logged in users
        'post__not_in' => $rendered_posts_ids
      ];

      // Query the most recent post from each of the featured categories,
      // while checking and remembering already rendered posts.
      $featured_post = new WP_Query($args);

      if ($featured_post->have_posts()):

        while ($featured_post->have_posts()):

          $featured_post->the_post();

          // Check if the current post has already been rendered on the page
          if (!okfn_is_post_rendered($post)):
            get_template_part('content', 'front');
          endif;

        endwhile;

        wp_reset_postdata();

      endif;

      ?>
    </div>
  <?php endif; ?>
</div>
<?php

get_sidebar();
get_footer();
