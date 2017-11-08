<?php if(!is_page(get_the_ID())) : ?>
    <div class="legend">
		
        <a href="<?php echo get_day_link(get_the_time('Y'), get_the_time('m'), get_the_time('d')); ?>" class="time"><?php echo get_the_date(); ?></a>
                
        <?php
            if(comments_open())
            {
                ?><a href="<?php comments_link(); ?>" class="comments"><?php comments_number('0', '1', '%'); ?></a> <?php
            }
        ?>
    </div>
<?php endif; ?>