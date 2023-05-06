<?php 

class HomepageSettingsClass
{
    function __construct(){
        add_action( 'acf/init', array($this, 'homepage_settings_menu_page') );
        add_shortcode( 'hp_banner', array($this, 'get_banner') );
        add_shortcode( 'hp_mission_vision_quality_policy', array($this, 'get_mission_vision_quality_policy') );
        add_shortcode( 'hp_announcements', array($this, 'get_announcements') );
    }

    function get_announcements(){
        $response = '';
        $announcement = get_field('announcement','hp-settings-options');

        foreach( $announcement as $item ){
            $response .= '<div class="announcement-container">'.wpautop($item['entry']).'</div>';
        }

        return $response;
    }

    function get_mission_vision_quality_policy($atts){

        $data = get_field($atts['to_get'],'hp-settings-options');

        return '<p>'.$data.'</p>';
    }


    function get_banner(){
        $response = '';
        $banner = get_field('banner','hp-settings-options');

        foreach( $banner as $item ){
            $response .= ' <div class="carousel-cell">
                                <img class="carousel-cell-image"
                                data-flickity-lazyload="'.$item.'" />
                            </div>';
        }

        return $response;
    }

    function homepage_settings_menu_page(){

        // Add a top menu page
        acf_add_options_page(
            array(
                'page_title' => 'Homepage Settings',
                'menu_title' => 'Homepage Settings',
                'menu_slug'  => 'hp-settings-page',
                'redirect'   => false,
                'capability' => 'administrator',
                'position'   => 5.4,
                'icon_url'   => 'dashicons-info',
                'post_id' => 'hp-settings-options',
                'autoload' => true,
                'updated_message' => __("HP Updated", 'acf'),
            )
        );

        if( function_exists('acf_add_local_field_group') ):

            acf_add_local_field_group(array(
                'key' => 'group_639df300a0b08',
                'title' => 'Homepage Information',
                'fields' => array(
                    array(
                        'key' => 'field_639df33baa320',
                        'label' => 'Banner',
                        'name' => 'banner',
                        'type' => 'gallery',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'return_format' => 'url',
                        'preview_size' => 'medium',
                        'insert' => 'append',
                        'library' => 'all',
                        'min' => '',
                        'max' => '',
                        'min_width' => '',
                        'min_height' => '',
                        'min_size' => '',
                        'max_width' => '',
                        'max_height' => '',
                        'max_size' => '',
                        'mime_types' => '',
                    ),
                    array(
                        'key' => 'field_639df30eaa31d',
                        'label' => 'Quality Policy',
                        'name' => 'quality_policy',
                        'type' => 'textarea',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'maxlength' => '',
                        'rows' => '',
                        'new_lines' => '',
                    ),
                    array(
                        'key' => 'field_639df321aa31e',
                        'label' => 'Mission',
                        'name' => 'mission',
                        'type' => 'textarea',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'maxlength' => '',
                        'rows' => '',
                        'new_lines' => '',
                    ),
                    array(
                        'key' => 'field_639df32eaa31f',
                        'label' => 'Vision',
                        'name' => 'vision',
                        'type' => 'textarea',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_639df366aa321',
                        'label' => 'Announcement',
                        'name' => 'announcement',
                        'type' => 'repeater',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'collapsed' => '',
                        'min' => 0,
                        'max' => 0,
                        'layout' => 'table',
                        'button_label' => '',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_639df384aa322',
                                'label' => 'Entry',
                                'name' => 'entry',
                                'type' => 'textarea',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'placeholder' => '',
                                'maxlength' => '',
                                'rows' => '',
                                'new_lines' => '',
                            ),
                        ),
                    ),
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'options_page',
                            'operator' => '==',
                            'value' => 'hp-settings-page',
                        ),
                    ),
                ),
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => true,
                'description' => '',
                'show_in_rest' => 0,
            ));
            
        endif;		

    }
}

new HomepageSettingsClass();