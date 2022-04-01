<script src="<?php echo get_bloginfo('template_directory') ?>/js/animate-page.js" charset="utf-8"></script>

<header id="single-page-header">

  <?php if (has_post_thumbnail() ) { ?>
    <img src="<?php echo wp_get_attachment_url( get_post_thumbnail_id($post->ID), 'background' ); ?>" alt="">
  <?php } ?>


  <div class="img-overlay"></div>
  <h1 class="blog-post-title"><?php the_title(); ?></h1>

</header>


<article class="big-post">

  <?php if ( metadata_exists('post', get_the_ID(), 'month') ) : ?>
    <p class="blog-post-category"><?php echo get_post_meta( get_the_ID(), 'month' )[0]; ?></p>
  <?php endif; ?>


  <p class="blog-post-date">Publicerades: <?php echo get_the_date(); ?></p>

  <p class="text-preview"><?php the_content() ?></p>



</article>

<script type="text/javascript">
  window.addEventListener('scroll', function() {
    fillNavigationBar('single-page-header');
  });
</script>
