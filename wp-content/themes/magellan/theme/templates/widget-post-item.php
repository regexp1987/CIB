<div class="item">
    <?php
        $image = magellan_get_thumbnail('magellan_widget_item', true, false);
        if($image)
        {
            ?>
            <div class="image">
                <a href="<?php the_permalink(); ?>"><img src="<?php echo esc_url($image); ?>" alt="<?php the_title(); ?>"></a>
            </div>
            <?php
        }
    ?>
    <div class="text<?php if(!$image) { echo ' no-image'; } ?>">
        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        <div class="legend">
            <span class="time"><?php echo get_the_date(); ?></span>
        </div>
    </div>
</div>

