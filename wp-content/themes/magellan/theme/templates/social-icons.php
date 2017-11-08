<?php
if(magellan_gs('social_facebook') != '')
{
    echo '<a href="' . esc_url(magellan_gs('social_facebook')) . '" target="_blank"><i class="fa fa-facebook-square"></i></a> ';
}
if(magellan_gs('social_twitter') != '')
{
    echo '<a href="' . esc_url(magellan_gs('social_twitter')) . '" target="_blank"><i class="fa fa-twitter-square"></i></a> ';
}
if(magellan_gs('social_youtube') != '')
{
    echo '<a href="' . esc_url(magellan_gs('social_youtube')) . '" target="_blank"><i class="fa fa-youtube-square"></i></a> ';
}
if(magellan_gs('social_pinterest') != '')
{
    echo '<a href="' . esc_url(magellan_gs('social_pinterest')) . '" target="_blank"><i class="fa fa-pinterest-square"></i></a> ';
}
if(magellan_gs('social_gplus') != '')
{
    echo '<a href="' . esc_url(magellan_gs('social_gplus')) . '" target="_blank"><i class="fa fa-google-plus-square"></i></a> ';
}
if(magellan_gs('social_instagram') != '')
{
    echo '<a href="' . esc_url(magellan_gs('social_instagram')) . '" target="_blank"><i class="fa fa-instagram"></i></a>';
}
/*if(magellan_gs('social_rss') != '')
{
    echo '<a href="' . esc_url(magellan_gs('social_rss')) . '" target="_blank"><i class="fa fa-rss"></i></a>';
}*/
?>