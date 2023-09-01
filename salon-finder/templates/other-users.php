<?php //echo 'aa'; die;?>
<?php $author = get_user_by( 'slug', get_query_var( 'author_name' ) );
							//var_dump( $author) ;
							?>
<div id="main-content">
	<div class="bg-white">
		<div class="container nosidebar">
			<div id="content-area" class="clearfix">
				<div class="author-block">
					<div class="author-intro">
						<div class="author-image-block">
							<div class="author-image-border ls"></div>
							<div class="author-image-border ls vr"></div>                 
							<img src="<?php echo wp_get_attachment_image_url( get_the_author_meta( 'image', $author->ID ) ); ?>"
							     alt=""
							     class="img-circle img">
							<div class="author-image-border rs"></div>
							<div class="author-image-border rs vr"></div>
						</div>
						<div class="author-text-block">
							<h5><?php echo $author->display_name; ?></h5>
							<?php echo get_the_author_meta( 'description', $author->ID ); ?>
						</div>
					</div>
				</div>
			</div> <!-- #content-area -->
		</div> <!-- .container -->
	</div>
	<div class="bg-light-grey">
		<div class="container p-t-15 p-b-15">
			<div class="author-block posts-grid">
				<div class="author-posts"> 
					<!--div class="fieldset">	
					</div-->
                    <h5 class="text-uppercase base-color fancy-text">Written By</h5>
					<div class="fieldset">
						<!--<div class="border ls"></div>-->
						<p class="fieldset-title tg-fieldset"><?php echo get_the_author() ?></p>
						<!--<div class="border rs"></div>-->
					</div>
					<?php

					$author = get_user_by( 'slug', get_query_var( 'author_name' ) )	;
					$length = 9;
					if ( isset( $_GET['load'] ) ) {
						$length = -1;
					}
					query_posts( array( 
                         'post_type' => 'post',
                         'posts_per_page' => $length,
                         'author'        => $author->ID,
                         'orderby'       => 'post_date',
						 'order'         => 'DESC', ) 
      				);
					$total_post = count_user_posts( $author->ID, 'post' );

					?>

<?php if(($author->ID!=3)&&($author->ID!=64768)):{ ?>
<style>
.fieldset {
    display: none;
}
h5.text-uppercase.base-color.fancy-text {
    display: none;
}
</style>
<?php }endif; ?>

					<div class="flex-grid">
						<?php 
							$count = 1;
							if(have_posts()){
							while ( have_posts() ) {the_post();
								if($count == 3){
								echo '<div class="flex-grid-item list-ads" style="cursor: pointer;">';
			                    echo " ";
			                    echo '</div>';

	                		}
							?>
							<div class="flex-grid-item">
								<div class="img">
									<a href="<?php the_permalink(); ?>"><img src="<?php echo get_the_post_thumbnail_url( get_the_ID() ) ?>" alt="<?php the_title(); ?>"></a>
								</div>
								<div class="flex-grid-meta">
									<?php
									$cats = wp_get_post_categories( get_the_ID() );
									$cat_name = array();
									foreach ( $cats as $cat ) {
										$cat_name[] = get_cat_name( $cat );
									}
									$category = implode( ", ", $cat_name );
									?> 
									<h5 class="base-color ls15"><?php echo $category; ?></h5>
									<div class="p-l-25 p-r-25">
										<a href="<?php the_permalink(); ?>" style="color: #000;"><?php the_title(); ?></a>
									</div>
									<div class="clearfix m-t-70">
										<div class="pull-left p-l-15 p-b-25 text-left text-small text-light-grey text-uppercase"><?php echo get_the_author() ?></div>
										<div class="pull-right p-r-15 p-b-25 text-right text-small text-light-grey text-uppercase"><?php echo get_the_date(); ?></div>
									</div>
								</div>
							</div>
							<?php
						$count++;} 
						}?>
					</div>

				</div>
			</div>
			<div class="text-center clearfix m-b-25 m-t-25">
				<?php if(-1 !== $length && $total_post > $length ){ ?>
				<a href="Javascript:void();" data-offset="10" data-max="<?php echo $total_post;?>" id="load_author" class="btn text-uppercase">Load More</a>
				<?php } ?>
			</div>
		</div>
	</div>
</div> <!-- #main-content -->
<script type="text/javascript">
      jQuery( document ).ready(function($) {
         
         
         $(document).on('click','#load_author', function(event){
            event.preventDefault();
            var postCount = $(this).data("offset");
            var elem = $(this);
            var max = elem.data("max");
            if(postCount>=max)
            {
            	elem.hide();
            }
            /*console.log(postCount);*/
            
            $.ajax({
                url: '<?php echo admin_url( 'admin-ajax.php' );?>',
                type: 'POST',
                data: {
                    'offset': postCount,
                    "action": "author_list_posts",
                    "author": <?php echo $author->ID;?>
                },
                success: function(data){
                	var res = JSON.parse(data);
                  if($.trim(res.html)!='')
                  {
                    $( ".flex-grid" ).append( res.html );
                    elem.data("offset",(postCount+9));
                    if((postCount+9) >=max || res.next == "n")
                    {
                    	elem.hide();
                    }
                  }
                  else{
                    elem.hide();
                  }
                  
                }
            });
         });
      });
    </script>