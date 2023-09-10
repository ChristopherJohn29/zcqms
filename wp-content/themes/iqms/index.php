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
  <div class="container-fluid">
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
  
  if( !is_home() ): 

    $post_id = get_the_ID();

$term = get_the_terms($post_id, 'services');

var_dump($term);

$service = "";

foreach ($term as $key => $value) {
    $service .= $value->name.', ';
}

$service .= 'asd12312asd';
$service = str_replace(', asd12312asd','', $service);
  
  ?>
  
    <div class="ip-banner-new">
        <h1><?=$service?></h1> 
    </div>
    <div class="container">

        

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
    <div class="container-fluid" style="display:flex;">
        <div class="col-md-8" style="">
            <div class="mission-vision">
                <img style="width:100%;" src="<?=get_stylesheet_directory_uri()?>/images/mission-vision.jpg" alt="Mission Vision" title="Zamboanga City Medical Center" style="text-align:left;">
            </div>

        </div>
        <div class="col-md-4">
            <div class="calendar-section">
                <h1 class="global-title" >
                    <strong>QMS ACTIVITIES</strong>
                </h1>
                <div class="calendar-wrapper">
                    <iframe src="https://calendar.google.com/calendar/embed?height=300&wkst=1&bgcolor=%23ffffff&ctz=Asia%2FManila&showTitle=0&showNav=0&showTabs=0&showCalendars=1&showPrint=0&src=Y2hyaXN0b3BoZXJqb2huZ2Ftb0BnbWFpbC5jb20&src=YWRkcmVzc2Jvb2sjY29udGFjdHNAZ3JvdXAudi5jYWxlbmRhci5nb29nbGUuY29t&src=ZW4ucGhpbGlwcGluZXMjaG9saWRheUBncm91cC52LmNhbGVuZGFyLmdvb2dsZS5jb20&color=%23039BE5&color=%2333B679&color=%230B8043" style="border:solid 1px #777" width="400" height="300" frameborder="0" scrolling="no"></iframe>
                </div>
            </div>
            <div class="announcement-section">
                <h1 class="global-title" >
                    <strong>ANNOUNCEMENTS</strong>
                </h1>
                <div class="announcement-right bootstrap-extend-right announcement-carousel">
                <?php
                    echo do_shortcode('[hp_announcements]');
                ?> 
                </div>
            </div> 

            <div class="announcement-section">
              
                    <h1 class="global-title" >
                            <strong>REQUEST FOR PRINTING <br> OF QMS DOCUMENTS</strong>
                    </h1>

                    <div class="printing-wrapper">
                        <?=do_shortcode('[contact-form-7 id="eadc0bf" title="request file"]')?>
                    </div>
            </div>
            
        </div>
    </div>

    <div class="container">


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

      
        

    </div>
    
    <!-- End Blogs -->
    <?php endif; ?>
  <!-- End Content Tabs -->
  

    <a href="#qr-container" class="hidden open-popup-qr">Feedback QR</a>

    <div id="qr-container" class="mfp-hide">
        <img src="<?=get_stylesheet_directory_uri()?>/images/feedback-qr-v2.png" width="250" height="250" alt="QR">
    </div>


 <?php get_footer(); ?>
