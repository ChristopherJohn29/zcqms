<?php


add_shortcode('hp_newsevents', 'hp_newsevents');

function hp_newsevents($atts){
	$args = array(
	  'post_type' => 'news-events',
	  'posts_per_page' => -1,
	  'orderby' => 'date',
	  'order' => 'desc'
	);

	$query = new WP_Query($args);
	$response = '';
	if( $query->have_posts() ){
	  while( $query->have_posts() ){
	    $query->the_post();
	    // Repeated
        $content = strip_tags(get_the_content());
        $description = ( strlen($content) > 105 ) ? substr($content, 0, 105) : $content ;

        if( $atts['type'] == 'main' ){
            $response .= '<div class="blog-col">
                            <div class="blog-img">
                                <a href="#blog-popup'.get_the_ID().'" class="open-popup-link">
                                <canvas width="293" height="185" style="background-image: url('.get_the_post_thumbnail_url(get_the_ID(), 'full').'); background-size: 100% 100%;background-repeat: no-repeat; "></canvas>
                                </a>
                            </div>
                            <div class="blog-info">
                                <div class="blog-name">
                                <a href="#blog-popup'.get_the_ID().'" class="open-popup-link">'.get_the_title().'</a>
                                </div>
                                <div class="blog-content">
                                <p>'.$description.'</p>
                                </div>
                                <div class="blog-link">
                                <a href="#blog-popup'.get_the_ID().'" class="open-popup-link">Read More</a>
                                </div>
                            </div>
                    </div>';
        }else{
            $response .='<div class="mfp-hide blog-popup-container" id="blog-popup'.get_the_ID().'">
                        <div class="image-container">
                            <canvas width="800" height="600" style="background-image: url('.get_the_post_thumbnail_url(get_the_ID(), 'full').'); background-size: 100% 100%;background-repeat: no-repeat; "></canvas>
                        </div>
                        <div class="content-container">
                            '.$content.'
                        </div>
                    </div>';
        }
	    

        
	    
	  }
	}else{
	  return '<h2>No post found.</h2>';
	}

	return $response;
}

