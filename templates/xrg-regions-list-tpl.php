<?php

use XRG\RD\XrgHelperFunctions;
use XRG\RD\XrgRdKpis;

// Action hook to redirect unauthorized users to login page
do_action('xrg-unauthorized-redirect');

get_header();
?>
<main class="ns-fade" data-target=".m-wrapper > *">
    <?php /* Intro Text */ 
	    $text = get_the_title();
		if ('' != $text) { ?>
		    <section class="page-header">
		        <div class="s-wrapper">
		            <h1><?php echo $text; ?></h1>
				</div>
		    </section>
		<?php } ?>
    <section class="content-module editor">
        <div class="m-wrapper">
            <?php 
            if(XrgHelperFunctions::xrgIsUserAllowed()) :
                while ( have_posts() ) :
                    the_post();
                    XrgRdKpis::instance()->xrgTemplatePageInstance()->xrgTemplatePart('parts/regions', 'list');
                endwhile; // end of the loop.
            else :
                XrgRdKpis::instance()->xrgTemplatePageInstance()->xrgTemplatePart('parts/no', 'access');
            endif;
            ?>
        </div>
	</section>
</main>
<?php
get_footer();
