<?php

new TransferDCM();

class TransferDCM{

    function __construct(){

        add_action( 'acf/save_post', array($this, 'check_post'), 10, 3 );
        add_filter( 'wp_insert_post_data' , array($this, 'filter_post_data') , '99', 2 );
        add_action( 'init', array($this, 'test_email'));
        add_action('save_post', array($this, 'validate_post_title'));
    }

    public function validate_post_title($post_id) {
        if ($this->is_autosave_or_revision($post_id)) {
            return;
        }

        if (!$this->is_required_post_type($post_id)) {
            return;
        }

        if ($this->is_post_title_empty($post_id)) {
            $this->show_error_message();
        }
    }

    private function is_autosave_or_revision($post_id) {
        return defined('DOING_AUTOSAVE') && DOING_AUTOSAVE || wp_is_post_revision($post_id);
    }

    private function is_required_post_type($post_id) {
        $post_type = get_post_type($post_id);
        $required_post_types = array('dcm'); // Add your custom post types here
        return in_array($post_type, $required_post_types);
    }

    private function is_post_title_empty($post_id) {
        $post_title = get_post_field('post_title', $post_id);
        return empty($post_title);
    }

    private function show_error_message() {
        $error_message = __('Post title is required.', 'text-domain');
        wp_die($error_message);
    }

    function test_email(){
        if(isset($_GET['testemail'])){
            $this->sendEmail('christopherjohngamo@gmail.com', 'QMS Document review status', 'test - QMS Document accepted ');

            exit;
        }
    }

    function get_date(){

        date_default_timezone_set('Asia/Shanghai');

        $currentDateTime = date("Y-m-d"). ' at ' .date('h:i A');

        return $currentDateTime;

    }

    function filter_post_data($data , $postarr){


        if($data['post_type'] == 'dcm') {
           $postID = $postarr['ID'];
        }

        if($postID){


            $dco_emailed = get_post_meta(  $postID, 'dco_emailed', true);
            $reviewer_emailed = get_post_meta(  $postID, 'reviewer_emailed', true);
            $approver_emailed = get_post_meta(  $postID, 'approver_emailed', true);
            $process_owner_emailed = get_post_meta(  $postID, 'process_owner_emailed', true);

            $is_approved = get_field( 'approval_status', $postID );
            $is_reviewed = get_field( 'review_status', $postID );

            $is_reviewed_new = $postarr['acf']['field_63d6812dd0c68'];
            $is_final_reviewed_new = $postarr['acf']['field_6331a3f94f0bc'];
            $is_approved_new = $postarr['acf']['field_632c62e991029'];

            $owner = get_userdata($data['post_author'])->data;

            $reviewer_raw = $postarr['acf']['field_6331998b7e607'];

            $approver_raw = $postarr['acf']['field_63319901dcdde'];

            $dcoreviewedby = $postarr['acf']['field_63d67a4f766a4'];
            $process_owner_raw = $postarr['acf']['field_632c70a0da093'];

            $post_title = get_the_title($postID);



            if(is_array($reviewer_raw)){

                if(empty($reviewer_emailed)){

                    $reviewer_emailed = array();

                    if($is_reviewed_new == 'yes'){
                        foreach ($reviewer_raw as $key => $value) {
                            $reviewer = get_userdata($value)->data;

                            if(get_option('notification_'.$reviewer->ID)){
                                $options = get_option('notification_'.$reviewer->ID);
                                $options[] = 'You have a document due for review: "'.$post_title.'"<br><br>'.$this->get_date();
                                update_option( 'notification_'.$reviewer->ID,  $options);
                            } else {
                                add_option( 'notification_'.$reviewer->ID,  ['You have a document due for review: "'.$post_title.'" <br><br>'.$this->get_date()]);
                            }

                            $this->sendEmail($reviewer->user_email, 'DCM Notification', 'You have a document due for review: "'.$post_title.'"');
                            $reviewer_emailed[] = $value;

                        }
                    }


                    if($is_reviewed_new == 'no' || $is_reviewed_new == 'review' ){
                        foreach ($process_owner_raw as $key => $value) {
                            $process_owner = get_userdata($value)->data;

                            if(get_option('notification_'.$process_owner->ID)){
                                $options = get_option('notification_'.$process_owner->ID);
                                $options[] = 'The document "'.$post_title.'" you have uploaded has been disapproved. Please check the remarks. <br><br>'.$this->get_date();
                                update_option( 'notification_'.$process_owner->ID,  $options);
                            } else {
                                add_option( 'notification_'.$process_owner->ID,  ['The document "'.$post_title.'" you have uploaded has been disapproved. Please check the remarks. <br><br>'.$this->get_date()]);
                            }

                            $this->sendEmail($process_owner->user_email, 'DCM Notification', 'The document "'.$post_title.'" you have uploaded has been disapproved. Please check the remarks.');
                            $reviewer_emailed = [];

                        }
                    }




                    update_post_meta( $postID, 'reviewer_emailed', $reviewer_emailed );
                }

            }


            if(is_array($process_owner_raw)){

                if(empty($approver_emailed)){

                    $approver_emailed = array();

                    if($is_final_reviewed_new == 'yes'){

                        foreach ($approver_raw as $key => $value) {
                            $approver = get_userdata($value)->data;

                            if(get_option('notification_'.$approver->ID)){
                                $options = get_option('notification_'.$approver->ID);
                                $options[] = 'The "'.$post_title.'" is due for your final review and approval <br><br>'.$this->get_date();
                                update_option( 'notification_'.$approver->ID,  $options);
                            } else {
                                add_option( 'notification_'.$approver->ID,  ['The "'.$post_title.'" is due for your final review and approval <br><br>'.$this->get_date()]);
                            }

                            $this->sendEmail($approver->user_email, 'DCM Notification', 'The "'.$post_title.'" is due for your final review and approval');
                            $approver_emailed[] = $value;

                        }

                        if(is_array($process_owner_raw)){
                            foreach ($process_owner_raw as $key => $value) {
                                $process_owner = get_userdata($value)->data;

                                if(get_option('notification_'.$process_owner->ID)){
                                    $options = get_option('notification_'.$process_owner->ID);
                                    $options[] = 'The "'.$post_title.'" you have uploaded has been reviewed <br><br>'.$this->get_date();
                                    update_option( 'notification_'.$process_owner->ID,  $options);
                                } else {
                                    add_option( 'notification_'.$process_owner->ID,  ['The "'.$post_title.'" you have uploaded has been reviewed <br><br>'.$this->get_date()]);
                                }

                                $this->sendEmail($process_owner->user_email, 'DCM Notification', 'The "'.$post_title.'" you have uploaded has been reviewed');
                                $approver_emailed[] = $value;
                            }
                        }



                    }


                    if($is_final_reviewed_new == 'no' || $is_final_reviewed_new == 'review'){
                        foreach ($process_owner_raw as $key => $value) {
                            $process_owner = get_userdata($value)->data;

                            if(get_option('notification_'.$process_owner->ID)){
                                $options = get_option('notification_'.$process_owner->ID);
                                $options[] = 'The document "'.$post_title.'" you have uploaded has been disapproved. Please check the remarks. <br><br>'.$this->get_date();
                                update_option( 'notification_'.$process_owner->ID,  $options);
                            } else {
                                add_option( 'notification_'.$process_owner->ID,  ['The document "'.$post_title.'" you have uploaded has been disapproved. Please check the remarks. <br><br>'.$this->get_date()]);
                            }

                            $this->sendEmail($process_owner->user_email, 'DCM Notification', 'The document "'.$post_title.'" you have uploaded has been disapproved. Please check the remarks');
                            $approver_emailed = [];

                        }
                    }


                    update_post_meta( $postID, 'approver_emailed', $approver_emailed );
                }

            }

            if(is_array($approver_raw)){

                if(empty($process_owner_emailed)){

                    $process_owner_emailed = array();

                    if($is_approved_new == 'yes'){

                         if(is_array($process_owner_raw)){
                            foreach ($process_owner_raw as $key => $value) {
                                $process_owner = get_userdata($value)->data;

                                if(get_option('notification_'.$process_owner->ID)){
                                    $options = get_option('notification_'.$process_owner->ID);
                                    $options[] = 'The "'.$post_title.'" you have uploaded has been approved <br><br>'.$this->get_date();
                                    update_option( 'notification_'.$process_owner->ID,  $options);
                                } else {
                                    add_option( 'notification_'.$process_owner->ID,  ['The "'.$post_title.'" you have uploaded has been approved <br><br>'.$this->get_date()]);
                                }

                                $this->sendEmail($process_owner->user_email, 'DCM Notification', 'The "'.$post_title.'" you have uploaded has been approved');
                                $process_owner_emailed[] = $value;
                            }
                        }
                    }


                    if($is_approved_new == 'no' || $is_approved_new == 'review'){
                        if(is_array($process_owner_raw)){
                            foreach ($process_owner_raw as $key => $value) {
                                $process_owner = get_userdata($value)->data;

                                if(get_option('notification_'.$process_owner->ID)){
                                    $options = get_option('notification_'.$process_owner->ID);
                                    $options[] = 'The document "'.$post_title.'" you have uploaded has been disapproved. Please check the remarks. <br><br>'.$this->get_date();
                                    update_option( 'notification_'.$process_owner->ID,  $options);
                                } else {
                                    add_option( 'notification_'.$process_owner->ID,  [ 'The document "'.$post_title.'" you have uploaded has been disapproved. Please check the remarks. <br><br>'.$this->get_date()]);
                                }

                                $this->sendEmail($process_owner->user_email, 'DCM Notification', 'The document "'.$post_title.'" you have uploaded has been disapproved. Please check the remarks.');
                                $process_owner_emailed = [];
                            }
                        }
                    }


                    update_post_meta( $postID, 'process_owner_emailed', $process_owner_emailed );
                }

            }




            if(is_array($dcoreviewedby) || is_array($process_owner_raw)){
                if(empty($dco_emailed)){

                    $dco_emailed = array();


                    if(is_array($dcoreviewedby)){
                        foreach ($dcoreviewedby as $key => $value) {
                            $dco = get_userdata($value)->data;

                            if(get_option('notification_'.$dco->ID)){
                                $options = get_option('notification_'.$dco->ID);
                                $options[] = 'You have a document due for review: "'.$post_title.'" <br><br>'.$this->get_date();
                                update_option( 'notification_'.$dco->ID,  $options);
                            } else {
                                add_option( 'notification_'.$dco->ID,  ['You have a document due for review: "'.$post_title.'" <br><br>'.$this->get_date()]);
                            }

                            $this->sendEmail($dco->user_email, 'DCM Notification', 'You have a document due for review: "'.$post_title.'"');
                            $dco_emailed[] = $value;

                        }
                    }

                    if(is_array($process_owner_raw)){
                        foreach ($process_owner_raw as $key => $value) {
                            $process_owner = get_userdata($value)->data;

                            if(get_option('notification_'.$process_owner->ID)){
                                $options = get_option('notification_'.$process_owner->ID);
                                $options[] = 'Your document has been uploaded: "'.$post_title.'" <br><br>'.$this->get_date();
                                update_option( 'notification_'.$process_owner->ID,  $options);
                            } else {
                                add_option( 'notification_'.$process_owner->ID,  ['Your document has been uploaded: "'.$post_title.'" <br><br>'.$this->get_date()]);
                            }

                            $this->sendEmail($process_owner->user_email, 'DCM Notification', 'Your document has been uploaded: "'.$post_title.'"');
                            $dco_emailed[] = $value;
                        }
                    }




                    update_post_meta( $postID, 'dco_emailed', $dco_emailed );

                }

            }

        }

        return $data;
    }


    public function sendEmail($toemail = '', $subject = '', $message = ''){



        $sent = wp_mail($toemail, $subject, strip_tags($message), $headers);

        return $sent;
    }

    function revision_transfer_post( $data, $qms_id){

        $ids = array();

        $post_data = array(
            'post_title' => $data['title'],
            'post_type' => 'dcm',
            'post_status' => 'publish',
            'post_author' => get_post_field('post_author', $qms_id) // Set the author of DCM document to be the same as QMS document
        );

        $post_id = wp_insert_post( $post_data );
        update_field('upload_document', $data['document']['ID'], $post_id);
        update_field('document_entry', $data['document_entry'], $post_id);
        update_field('users', $data['users'], $post_id);

        update_field('document_type', $data['_document_type'], $post_id);
        update_field('date_of_effectivity', $data['date_of_effectivity'], $post_id);
        update_field('file_url', $data['file_url'], $post_id);

        update_field('document_id', $data['document_id'], $post_id);
        update_field('revision', $data['revision'], $post_id);
        update_field('for_revision', 'yes', $post_id);

        add_post_meta($post_id, 'qms-revision-id', $qms_id);
        add_post_meta($post_id, 'dcm', $qms_id);

        wp_set_post_terms( $post_id, $data['services'], 'services' );
        wp_set_post_terms( $post_id, $data['document_type'], 'document_type' );
        wp_set_post_terms( $post_id, $data['documents_label'], 'documents_label' );


        wp_redirect( get_site_url() . '/wp-admin/edit.php?post_type=dcm&orderby=date&order=desc&new_id='.$post_id );
        exit;
    }

    function revision_update( $data, $dcm_id){

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
            'post_status' => 'publish',
            'post_author' => get_post_field('post_author', $dcm_id) // Set the author of QMS document to be the same as DCM document
        );

        if(in_array($dcm_id, $ids)){
            // no insert
            wp_delete_post($dcm_id, true);

        } else {

            $post_id = get_post_meta($dcm_id, 'qms-revision-id', true);
            update_field('upload_document', $data['document']['ID'], $post_id);
            update_field('document_entry', $data['document_entry'], $post_id);
            update_field('users', $data['users'], $post_id);
            update_field('for_revision', array(), $post_id);

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
            $_user_dco_reviewed = get_post_meta( $dcm_id, '_user_dco_reviewed', true );

            add_post_meta( $post_id, '_user_approved', $_user_approved );
            add_post_meta( $post_id, '_user_reviewed', $_user_reviewed );
            add_post_meta( $post_id, '_user_dco_reviewed', $_user_dco_reviewed );
            wp_delete_post($dcm_id, true);

        }
        wp_redirect( get_site_url() . '/wp-admin/edit.php?post_type=qms-documents&orderby=date&order=desc&new_id='.$post_id );
        exit;
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
            'post_status' => 'publish',
            'post_author' => get_post_field('post_author', $dcm_id) // Set the author of QMS document to be the same as DCM document
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
            $_user_dco_reviewed = get_post_meta( $dcm_id, '_user_dco_reviewed', true );

            add_post_meta( $post_id, '_user_approved', $_user_approved );
            add_post_meta( $post_id, '_user_reviewed', $_user_reviewed );
            add_post_meta( $post_id, '_user_dco_reviewed', $_user_dco_reviewed );
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
            $is_dco_reviewed = get_field( 'dco_review_status', $post_ID );
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

            $dco_reviewed_by = get_post_meta( $post_id, '_user_dco_reviewed', true );
            if ( (!$dco_reviewed_by) && $is_dco_reviewed ) {
                add_post_meta( $post_ID, '_user_dco_reviewed', $user_id );
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
                    add_post_meta( $post_ID, '_user_dco_reviewed', $_review_by[0]['ID'] );
                    $is_auto_approved = true;
                }

            }

            $args = array(
                'role'    => 'dco',
            );

            $users_dco = get_users( $args );

            $new_assigned_dco = [];

            foreach ($users_dco as $key => $value) {
                $new_assigned_dco[] = $value->data->ID;
            }

            update_field('assigned_dco', $new_assigned_dco, $post_ID);

            if( ($is_approved  == 'yes' && $is_reviewed == 'yes') || $is_auto_approved ){

                // add email
                $revision = get_field('for_revision' , $post_ID );

                if(isset($revision[0])){
                    if($revision[0] == 'yes'){
                        $this->revision_update($data, $post_ID);
                    } else {
                        $this->transfer_post($data, $post_ID);
                    }
                } else {
                    $this->transfer_post($data, $post_ID);
                }


            } else {
            	wp_redirect( get_site_url() . '/wp-admin/edit.php?post_type=dcm' );
            	exit;
            }
        } else if(get_post_type( $post_ID ) == 'qms-documents') {
            $for_revision = get_field( 'for_revision', $post_ID, true );


            if($for_revision[0] == 'yes'){


                $this_user = wp_get_current_user();
                $user_id = $this_user->ID;

                $is_approved = get_field( 'approval_status', $post_ID );
                $is_reviewed = get_field( 'review_status', $post_ID );
                $is_dco_reviewed = get_field( 'dco_review_status', $post_ID );
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

                $this->revision_transfer_post($data, $post_ID);
            }
        }

    }

}