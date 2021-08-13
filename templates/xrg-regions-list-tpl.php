<?php

use XRG\RD\XrgRdKpis;

get_header();

echo 'TEST PAHGE';
 while ( have_posts() ) :

    the_post();

    XrgRdKpis::instance()->xrgTemplatePageInstance()->xrgTemplatePart('regions', 'list');

//     wc_get_template_part( 'content', 'single-product' );

 endwhile; // end of the loop.



get_footer();
