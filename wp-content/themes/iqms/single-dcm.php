<?php get_header(); ?>
<?php 
$upload_document = get_field('upload_document');
$file_url = get_field('file_url');
$document_type = get_field('document_type');
$revision = get_field('revision');
$document_id = get_field('document_id');
$date_of_effectivity = get_field('date_of_effectivity');
if( $document_type == 'file' ){
    if( strpos($upload_document['url'],'.doc') !== false || strpos($upload_document['url'],'.pptx') !== false || strpos($upload_document['url'],'.ppsx') !== false ){
        $iframe_src = 'https://view.officeapps.live.com/op/embed.aspx?src='.$upload_document['url'];
    }
    else{
        $iframe_src = $upload_document['url'].'#toolbar=0&navpanes=0';
    }
    $iframe_html ='<iframe src="'.$iframe_src.'" width="100%" height="800" frameborder="0" style="border:1px solid black;">
    </iframe>';
}else{
    $iframe_src = $file_url;
    if( strpos($file_url,'drive.google') !== false ){
        $url = str_replace('view?usp=sharing', 'preview', $file_url);
        $iframe_src = $url;
        $iframe_html ='<iframe sandbox="allow-same-origin allow-scripts" src="'.$iframe_src.'" width="100%" height="800" frameborder="0" style="border:1px solid black;">
                         </iframe>';
    } else {
        $url = str_replace('pub?', 'embed?', $file_url);
        $iframe_src = $url;
        $iframe_html ='<iframe sandbox="allow-same-origin allow-scripts allow-popups allow-forms" src="'.$iframe_src.'" width="100%" height="800" frameborder="0" style="border:1px solid black;">
                         </iframe>';
    }
}

$reviewed_by = get_post_meta( get_the_ID(), '_user_reviewed', true );
$reviewed_by_user = get_user_by('ID', $reviewed_by);
$reviewed_by_name = $reviewed_by_user->data->display_name;
$reviewed_by_role = ( ($reviewed_by_user->roles[0] ? $reviewed_by_user->roles[0] : '') );
$reviewed_by_position = get_field('user_position', 'user_'.$reviewed_by_user->ID);


$approved_by = get_post_meta( get_the_ID(), '_user_approved', true );
$approved_by_user = get_user_by('ID', $approved_by);
$approved_by_name = $approved_by_user->data->display_name;
$approved_by_role = ( ($approved_by_user->roles[0] ? $approved_by_user->roles[0] : '') );
$approved_by_position = get_field('user_position', 'user_'.$approved_by_user->ID);

$author_id = get_post_field ('post_author', $post_id);
$display_name = get_the_author_meta( 'display_name' , $author_id ); 
$prepared_by_user = get_user_by('ID', $author_id);
$prepared_by_position = get_field('user_position', 'user_'.$author_id);

$users = get_field( 'users' );
$display_name = ( $users[0]['user_firstname'] ? $users[0]['user_firstname'] . ' ' . $users[0]['user_lastname'] : $display_name );

?>
<style type="text/css">
    header {
        display: none;
    }
</style>

<div class="container">

    
    <div class="document-container">
    <?=$iframe_html?>
    </div>

</div>

<?php get_footer(); ?>