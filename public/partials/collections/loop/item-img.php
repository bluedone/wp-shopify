<?php if (property_exists($collection, 'image') && $collection->image) { ?>

  <img src="<?php echo $collection->image; ?>" alt="<?php echo $collection->title . ' collection ' . $collection->collection_id; ?>" class="<?php echo apply_filters( 'wps_collections_img_class', '' ); ?>" />

<?php } ?>
