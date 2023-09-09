<?php
if ( !is_user_logged_in() ) {
    wp_redirect(get_site_url().'/wp-admin'); 
}
get_header(); ?>

<?php 
    $get_current_object = get_queried_object();
    $image = !is_tax() ? get_stylesheet_directory_uri().'/images/single-banner.jpg' :  get_field('tax-services-image', $get_current_object);

    if(is_home()){
?>
  <!-- Slider -->
  <div class="container">
    <div class="carousel" data-flickity='{ "fullscreen": true, "lazyLoad": 1, "autoPlay": 3500, "freeScroll": true }'>

        <?php
            echo do_shortcode('[hp_banner]');
        ?>
         
        </div>
  </div>
  <!-- End Slider -->

  <!-- Content Tabs -->
  <?php 

  }
  
  if( !is_home() ): ?>
    <div class="container-fluid">

        <ul class="nav nav-tabs mb-3" id="ex1" role="tablist">

            <?php
        
                $terms = get_terms( array(
                    'taxonomy' => 'document_type',
                    'hide_empty' => false,
                ) );

                foreach ($terms as $term) {
                    echo '<li class="nav-item" role="presentation"><a class="nav-link '.( ( $term->slug == 'qms' ) ? 'active show' : '' ).'" data-mdb-toggle="tab" href="#'.$term->slug.'" role="tab" >'.$term->name.'</a> </li>';
                }
                
                ?>        
        </ul> 
        <div class="tab-content" id="ex1-content">
        <?php 
            
            foreach ($terms as $term) {
            
                echo ' <div class="tab-pane fade '.( ( $term->slug == 'qms' ) ? 'active show' : '' ).' " id="'.$term->slug.'" role="tabpanel">';
           
                $args = array(
                    'post_type' => 'qms-documents',
                    'posts_per_page' => -1,
                    'orderby' => 'menu_order',
                    'order' => 'asc',
                    'tax_query' => array(
                        'relation' => 'AND',
                        array(
                            'taxonomy' => 'services',
                            'field'    => 'slug',
                            'terms'    => $get_current_object->slug,
                            'include_children' => false,
                        ),
                        array(
                            'taxonomy' => 'document_type',
                            'field'    => 'slug',
                            'terms'    => $term->slug,
                            'include_children' => false,
                        ),
                    ),
                );
                $tax_array = array();
                $query = new WP_Query($args);

                $data_arr = array();
                if( $query->have_posts() ):
                    while( $query->have_posts() ): $query->the_post();

                    if(in_array(get_the_ID(), $tax_array)){
                        continue;
                    }
                    
                    $upload_document = get_field('upload_document');
                    $document_entry = get_field('document_entry');

                    $label = wp_get_post_terms( get_the_ID(), 'documents_label', array( 'fields' => 'all') );
                    
                    
                    if( $label[0] ){

                        foreach ($label as $labelkey => $labelvalue) {

                            echo '<p>'.$labelvalue->name.'</p>';

                            $tax_args = array(
                                'post_type' => 'qms-documents',
                                'tax_query' => array(
                                    'relation' => 'AND',
                                    array(
                                        'taxonomy' => 'services',
                                        'field'    => 'slug',
                                        'terms'    => $get_current_object->slug,
                                    ),
                                    array(
                                        'taxonomy' => 'document_type',
                                        'field'    => 'slug',
                                        'terms'    => $term->slug,
                                    ),
                                    array(
                                        'taxonomy' => 'documents_label',
                                        'field'    => 'slug',
                                        'terms'    => $labelvalue->slug,
                                    ),
                                )
                            );


                            $tax_query = new WP_Query($tax_args);

                            if($tax_query->have_posts()) : 
                                echo '<ul>';
                                while($tax_query->have_posts()) : 
                                $tax_query->the_post();

                                $tax_array[] = get_the_ID();
                                echo '<li><a href="'. get_the_permalink().'" target="_blank">'.get_the_title().'</a></li>';

                                
                            endwhile;
                                echo '</ul>';
                            endif;
                            wp_reset_postdata();
                            
                        }
                        
                        
                        
                    }else{
                        echo '<p><a href="'. get_the_permalink().'" target="_blank">'.get_the_title().'</a></p>';
                    }

                    
                
                    ?>
                    
                    <?php 
                    
                    endwhile;
                    
                endif;
    
                wp_reset_postdata();
                echo '</div>';
            }
        
        ?>
        </div>
        <?php 
            
         
        ?>
        
    </div>
    

    <?php else: ?>
    <!-- Blogs -->
    <div class="container-fluid">

        <section id="hp-welcome" class="hp-welcome">
            <div class="container">
                <div class="welcome-wrap">
                    
                    <div class="row welcome-inner">
                        <div class="col-md-6">
                            <div class="quality-policy-wrapper">
                                <h2>Quality Policy</h2>
                                <?php
                                    echo do_shortcode('[hp_mission_vision_quality_policy to_get="quality_policy"]');
                                ?>
                            </div>
                            <div class="mission-wrapper">
                                <h2>Mission</h2>
                                <?php
                                    echo do_shortcode('[hp_mission_vision_quality_policy to_get="mission"]');
                                ?>
                            </div>
                            <div class="vission-wrapper">
                                <h2>Vision</h2>
                                <?php
                                    echo do_shortcode('[hp_mission_vision_quality_policy to_get="vision"]');
                                ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="welcome-right bootstrap-extend-right">
                                <div class="welcome-img">
                                    <img src="<?=get_stylesheet_directory_uri()?>/images/welcome.jpg" alt="Welcome">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </section>

        <section id="hp-announcement" class="hp-announcement">
            <div class="container">
                <div class="announcement-wrap">
                        
                    <div class="row announcement-inner">
                        <div class="col-md-6">
                            <div class="announcement-img">
                                    <canvas style="background-image: url(<?=get_stylesheet_directory_uri()?>/images/announcements-logo.png); background-size: 100% 100%;background-repeat: no-repeat;"></canvas>
                                </div>
                        </div>
                        <div class="col-md-6">
                            <h1 class="global-title" >
                            <strong>Announcement</strong>
                            </h1>
                            <div class="announcement-right bootstrap-extend-right announcement-carousel">
                                
                            <?php
                                echo do_shortcode('[hp_announcements]');
                            ?>
                                
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </section>

        <section id="hp-blog" class="hp-blog">
            <div class="container">
                <div class="blog-wrap">
                        <h1 class="global-title" >
                            <strong>News & Events</strong>
                        </h1>
                    <div class="blog-list-wrap">
                        <div class="blog-list">

                            <?=do_shortcode('[hp_newsevents type="main"]')?>
                        
                        </div>

                        <?=do_shortcode('[hp_newsevents type="popup"]')?>
                    </div>
                </div>
            </div>
        </section>

      

        <section id="hp-cuentoy-salud" class="hp-cuentoy-salud">
            <div class="container">

                <div class="row">
                    <div class="col-md-8">
                        <div class="cuentoy-salud-wrap">
                            <h1 class="global-title" >
                            <strong>Cuento'y Salud</strong>
                            </h1>
                            <div class="welcome-img">
                                <img src="<?=get_stylesheet_directory_uri()?>/images/salud.jpg" alt="Salud">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="calendar-wrapper">
                            <iframe src="https://calendar.google.com/calendar/embed?src=rennzzzublasquillo%40gmail.com&ctz=Asia%2FManila" style="border: 0" width="400" height="200" frameborder="0" scrolling="no"></iframe>
                        </div>

                        <h3 style="margin-top:10px;" >
                            <strong>Request for document printing</strong>
                        </h3>
                        <div class="calendar-wrapper">
                           <?=do_shortcode('[contact-form-7 id="eadc0bf" title="request file"]')?>
                        </div>
                    </div>
                    
                    
                </div>

            </div>
        </section>

        

    </div>
    
    <!-- End Blogs -->
    <?php endif; ?>
  <!-- End Content Tabs -->
  

    <a href="#qr-container" class="hidden open-popup-qr">Feedback QR</a>

    <div id="qr-container" class="mfp-hide">
        <img src="<?=get_stylesheet_directory_uri()?>/images/feedback-qr-v2.png" width="250" height="250" alt="QR">
    </div>


 <?php get_footer(); ?>
