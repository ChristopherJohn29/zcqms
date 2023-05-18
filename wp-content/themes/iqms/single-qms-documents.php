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


$prepared_by = get_field( 'users' );
$prepared_by_user = get_user_by('ID', $prepared_by[0]['ID']);
$prepared_by_name = $prepared_by_user->data->display_name;
$prepared_by_role = ( ($prepared_by_user->roles[0] ? $prepared_by_user->roles[0] : '') );
$prepared_by_position = get_field('user_position', 'user_'.$prepared_by_user->ID);

$author_id = get_post_field ('post_author', $post_id);

$term = get_the_terms($post_idm, 'services');

echo "<pre>";
var_dump($term);
echo "</pre>";

// var_dump($prepared_by);
// $prepared_by_position = get_field('user_position', 'user_'.$author_id);

// $users = get_field( 'users' );
// $display_name = ( $users[0]['user_firstname'] ? $users[0]['user_firstname'] . ' ' . $users[0]['user_lastname'] : $display_name );

?>
<div class="ip-banner">
    <canvas width="1600" height="350" style="display: block; position: relative; z-index: 0; width: 100%; min-height: 350px; background-color: #999; background-position: center center; background-repeat: no-repeat; background-size: cover; background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url(<?=get_stylesheet_directory_uri()?>/images/single-banner.jpg);"></canvas>
    <h1><?=get_the_title()?></h1> 
</div>
<div class="container">
    <div class="document-info-container row">
        <div class="doc-id-wrapper col">
            <div>Document ID</div>
            <span><?=$document_id?></span>
        </div>
        <div class="doc-title-wrapper col">
            <span><?=get_the_title()?></span>
        </div>
        <div class="doc-date-wrapper col">
            <div>Effectivity Date</div>
            <span><?=$date_of_effectivity?></span>
        </div>
    </div>
    
    <div class="document-container">
    <?=$iframe_html?>
    </div>

    <div class="document-info-wrapper row">
        <div class="col">
            <div>Prepared by:</div>
            <div class="document-info-name"><?=$prepared_by_name?></div>
            <div><?=$prepared_by_position?></div>
        </div>
        <div class="col">
            <div>Reviewed by:</div>
            <div class="document-info-name"><?=$reviewed_by_name?></div>
            <div><?=$reviewed_by_position?></div>
        </div>
        <div class="col">
            <div>Approved by:</div>
            <div class="document-info-name"><?=$approved_by_name?></div>
            <div><?=$approved_by_position?></div>
        </div>
    </div>
    
    <?php 
        $ctr = 1;
    ?>
    <?php if($revision): ?>
    <div class="revision-info-container">
        <?php foreach( $revision as $item ): ?>
        <div class="row">
            <div class="col revision-number">
                <div>
                    Revision No.
                </div>
                <span><?=$ctr?></span>
            </div>

            <div class="col revision-made">
                <div> 
                    Revision Made
                </div>
                <span><?=$item['revision_made']?></span>
            </div>

            <div class="col revision-date">
                <div> 
                Date of Revision
                </div> 
                <span><?=$item['date_of_revision']?></span>
            </div>
            <?php $ctr++; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php get_footer(); ?>