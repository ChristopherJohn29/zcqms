<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="/docs/4.1/assets/img/favicons/favicon.ico">
    <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>">
    <title>IQMS Portal</title>
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        rel="stylesheet"
    />
    <link
        href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap"
        rel="stylesheet"
    />
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/5.0.0/mdb.min.css"
        rel="stylesheet"
    />
    <?php wp_head(); ?>
  </head>

<body>

    <header>
        <div id="masthead" style="background: #b9d1a5 url(/)  center center; ">
            <div id="logo" class="row">
                
                    <a href="/">
                        <img src="<?=get_stylesheet_directory_uri()?>/images/site-logo.jpg" alt="Site Logo" title="Zamboanga City Medical Center" style="text-align:left;">
                    </a>
                
            </div>
        </div>
        <div class="fixed-header">
            <div class="container-fluid">
              
                    <div class="col-md-12">
                        <div class="fixed-header-inner">
                            <div class="header-logo">
                                <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Fixed Header Logo") ) : ?>
                                <?php endif ?>

                                <div id="logo"> 
                                    <div class="toggle-menu">
                                        <a class="toggle-nav" href="#">
                                            <span class="icon-bar"></span>
                                            <span class="icon-bar"></span>
                                            <span class="icon-bar"></span>
                                        </a>
                                    </div>
                                </div>

                            </div>
                            <?php
                            $main_nav = '';
                            $taxonomy_name = 'services';
                            $terms = get_terms(array('taxonomy' => $taxonomy_name, 'hide_empty' => false));
                            $services = array();
                            sort_hierarchical($terms,$services) ;
                                
                            foreach( $services as $q ){

                                $main_nav .= '<li class="menu-item menu-item-type-taxonomy menu-item-object-services"><a href="'.get_term_link( $q->term_id, $taxonomy_name ) .'">'.$q->name.'</a>';
                                if( count($q->children) > 0 ){
                                    $main_nav .= '<ul class="sub-menu">';
                                    foreach( $q->children as $w ){
                                        $main_nav .= '<li class="menu-item menu-item-type-taxonomy menu-item-object-services children"><a href="'.get_term_link( $w->term_id, $taxonomy_name ) .'">'.$w->name.'</a>';

                                        if( count($w->children) > 0 ){
                                            $main_nav .= '<ul class="grandchild">';

                                            foreach( $w->children as $e ){
                                                $main_nav .= '<li class="menu-item menu-item-type-taxonomy menu-item-object-services"><a href="'.get_term_link( $e->term_id, $taxonomy_name ) .'">'.$e->name.'</a></li>';
                                            }

                                            $main_nav .= '</ul>';
                                        }
                                        $main_nav .= '</li>';

                                    }

                                    $main_nav .= '</ul>';
                                }
                                $main_nav .= '</li>';
                            }
                           
                            ?>
                            
                            <nav class="nav">
                               <div class="main-nav">
                                  <ul id="menu-services" class="menu">
                                    <?= $main_nav ?>
                                  </ul>
                               </div>
                            </nav>

                            <div class="nav-toggle-wrapper">
                               <div class="nav-toggle-menu">
                                  <ul id="menu-services" class="menu">
                                     <?= $main_nav ?>
                                  </ul>
                               </div>
                            </div>

                        </div>
                    </div>
                
            </div>
        </div>

        
    </header>