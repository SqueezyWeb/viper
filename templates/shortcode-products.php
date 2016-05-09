<ul class="<?php echo join( ' ', $container_classes ); ?>">
<?php foreach ( $products as $product ) : ?>
  <li class="<?php post_class( '', $product->ID ); ?>">
    <h3><a href="<?php echo get_permalink( $product ); ?>"><?php echo get_the_title( $product ); ?></a></h3>
    <a href="<?php echo get_permalink( $product ); ?>">
      <?php echo get_the_post_thumbnail( $product, $thumbnail_size ); ?>
    </a>
  </li>
<?php endforeach; ?>
</ul>
