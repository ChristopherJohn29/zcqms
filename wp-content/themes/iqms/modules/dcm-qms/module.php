<?php 

new TransferDCM();

class TransferDCM{

    function __construct(){
        
        add_action( 'acf/save_post', array($this, 'check_post'), 10, 3 );
        add_filter( 'wp_insert_post_data' , array($this, 'filter_post_data') , '99', 2 );
    }

    function filter_post_data($data , $postarr){

        
        if($data['post_type'] == 'dcm') {
           $postID = $postarr['ID'];
        }

        if($postID){
            $is_approved = get_field( 'approval_status', $postID );
            $is_reviewed = get_field( 'review_status', $postID );

            $dco_emailed = get_post_meta(  $postID, 'dco_emailed', true);

            $is_reviewed_new = $postarr['acf']['field_63d6812dd0c68'];
            $is_approved_new = $postarr['acf']['field_632c62e991029'];
            $owner = get_userdata($data['post_author'])->data;
            
            if($is_reviewed_new != $is_reviewed){
                if($is_reviewed_new == 'yes'){
                    $this->sendEmail($owner->user_email, 'QMS Document review status', $data['post_author'].' - QMS Document accepted ');
                }

                if($is_reviewed_new == 'no'){
                    $this->sendEmail($owner->user_email, 'QMS Document review status', $data['post_author'].' - QMS Document to disapproved');
                }
            }

            if($is_approved_new != $is_approved){
                if($is_approved_new == 'yes'){
                    $this->sendEmail($owner->user_email, 'QMS Document status', $data['post_author'].' -  QMS Document approved');
                }

                if($is_approved_new == 'no'){
                    $this->sendEmail($owner->user_email, 'QMS Document  status', $data['post_author'].' -  QMS Document denied');
                }
            }



            $dcoreviewedby = $postarr['acf']['field_63d67a4f766a4'];


            if(empty($dco_emailed)){
                $dco_emailed = array();
                foreach ($dcoreviewedby as $key => $value) {

                    $dco = get_userdata($value)->data;
                 
                    $this->sendEmail($dco->user_email, 'New QMS Document to review', 'There is new QMS Document to review');
                    $dco_emailed[] = $value;
                
                }
            } else {
                if ( is_array( $dcoreviewedby ) ) {
                    foreach ($dcoreviewedby as $key => $value) {

                        if(!in_array($value, $dco_emailed)){    
                            $dco = get_userdata($value)->data;

                            $this->sendEmail($dco->user_email, 'New QMS Document to review', 'There is new QMS Document to review');
                            $dco_emailed[] = $value;
                        }
                    
                    }
                }
            }

            update_post_meta( $postID, 'dco_emailed', $dco_emailed );

    
        }
        
        return $data;
    }


    public function sendEmail($toemail = '', $subject = '', $message = ''){



        $sent = wp_mail($to, $subject, strip_tags($message), $headers);
            
        return $sent;
    }

    function transfer_post( $data, $dcm_id){

        $ids = array();

        $args = array(
            'post_type' => 'qms-documents'
        ); 

        $query = new WP_Query($args);

        if($query->have_posts()): while($query->have_posts()) :
            $query->the_post();

             $dcm_id_entry = get_post_meta($post_ID, 'dcm_id', true);

             if($dcm_id_entry) {
                $ids[] = $dcm_id_entry;
             }
            
        endwhile;
            wp_reset_postdata();
        endif;

        $post_data = array(
            'post_title' => $data['title'],
            'post_type' => 'qms-documents',
            'post_status' => 'publish'
        );

        if(in_array($dcm_id, $ids)){
            // no insert
            wp_delete_post($dcm_id, true);

        } else {

            $post_id = wp_insert_post( $post_data );
            update_field('upload_document', $data['document']['ID'], $post_id);
            update_field('document_entry', $data['document_entry'], $post_id);
            update_field('users', $data['users'], $post_id);

            update_field('document_type', $data['_document_type'], $post_id);
            update_field('date_of_effectivity', $data['date_of_effectivity'], $post_id);
            update_field('file_url', $data['file_url'], $post_id);

            update_field('document_id', $data['document_id'], $post_id);
            update_field('revision', $data['revision'], $post_id);

            add_post_meta($post_id, 'dcm_id', $dcm_id);
    
            wp_set_post_terms( $post_id, $data['services'], 'services' );
            wp_set_post_terms( $post_id, $data['document_type'], 'document_type' );
            wp_set_post_terms( $post_id, $data['documents_label'], 'documents_label' );

            $_user_approved = get_post_meta( $dcm_id, '_user_approved', true );
            $_user_reviewed = get_post_meta( $dcm_id, '_user_reviewed', true );

            add_post_meta( $post_id, '_user_approved', $_user_approved );
            add_post_meta( $post_id, '_user_reviewed', $_user_reviewed );
            wp_delete_post($dcm_id, true);


        }
        wp_redirect( get_site_url() . '/wp-admin/edit.php?post_type=qms-documents&orderby=date&order=desc&new_id='.$post_id );
        exit;
    }

    function check_post( $post_ID ){
        
        if ( get_post_type( $post_ID ) == 'dcm' ) {
            /*get user*/
            $this_user = wp_get_current_user();
            $user_id = $this_user->ID;

            $is_approved = get_field( 'approval_status', $post_ID );
            $is_reviewed = get_field( 'review_status', $post_ID );
            $document = get_field('upload_document' , $post_ID );
            $document_entry = get_field('document_entry');
            $users = get_field('users' , $post_ID );
            $date_of_effectivity = get_field('date_of_effectivity' , $post_ID );

            $_document_type = get_field('document_type' , $post_ID );
            $file_url = get_field('file_url' , $post_ID );

            $services = wp_get_post_terms($post_ID, 'services', array( 'fields' => 'ids' ));
            $document_type = wp_get_post_terms($post_ID, 'document_type', array( 'fields' => 'ids' ));
            $documents_label = wp_get_post_terms($post_ID, 'documents_label', array( 'fields' => 'ids' ));

            /*revision*/
            $document_id = get_field('document_id' , $post_ID );
            $revision = get_field('revision' , $post_ID );

            $data['title'] = get_the_title( $post_ID );
            $data['document'] = $document;
            $data['document_entry'] = $document_entry;
            $data['services'] = $services;
            $data['document_type'] = $document_type;
            $data['_document_type'] = $_document_type;
            $data['file_url'] = $file_url;
            $data['documents_label'] = $documents_label;
            $data['users'] = $users;
            $data['date_of_effectivity'] = $date_of_effectivity;

            $data['document_id'] = $document_id;
            $data['revision'] = $revision;

            /*user meta*/
            $approved_by = get_post_meta( $post_id, '_user_approved', true );

            if ( (!$approved_by) && $is_approved ) {
                add_post_meta( $post_ID, '_user_approved', $user_id );
            }
            
            $reviewed_by = get_post_meta( $post_id, '_user_reviewed', true );
            if ( (!$reviewed_by) && $is_reviewed ) {
                add_post_meta( $post_ID, '_user_reviewed', $user_id );
            }

            /*auto aprove*/
            $auto_approve = get_field('auto_approve' , $post_ID );
            $is_auto_approved = false;
            if ( $auto_approve[0] == 'Yes' ) {
                $_approved_by = get_field( 'approved_by', $post_ID );
                $_review_by = get_field( 'review_by', $post_ID );

                if ( $_approved_by[0]['ID'] && $_review_by[0]['ID'] ) {

                    add_post_meta( $post_ID, '_user_approved', $_approved_by[0]['ID'] );
                    add_post_meta( $post_ID, '_user_reviewed', $_review_by[0]['ID'] );
                    $is_auto_approved = true;
                }

            }

            if( ($is_approved  == 'yes' && $is_reviewed == 'yes') || $is_auto_approved ){

                // add email 
                $this->transfer_post($data, $post_ID);
            } else {
            	wp_redirect( get_site_url() . '/wp-admin/edit.php?post_type=dcm' );
            	exit;
            }
        }

    }

}