

<?php

while ( $loop->have_posts() ) : $loop->the_post();
	$video_url = get_field('video_url', get_the_ID());
	$button_text = get_field('video_button_text', get_the_ID()) ? get_field('video_button_text', get_the_ID()) : '';
	$bio = get_field('bio', get_the_ID());
	?>
	<div class="hip-team-member-container video-modal-layout">
		<div class="hip-team-member-box">
			<div class="hip-team-box__layer hip-team-box__front">
				<?php $this->render_post_thumbnails(); ?>
				<div class="team-name-designation">
					<div class="team-name">
					  <?php $this->render_team_name();?>
					</div>
					<div class="team-designation">
						<?php $this->render_team_designation();?>
					</div>
					<div class="front team-bio">
						<?php echo $bio ;?>
					</div>
<!--                   <div class="video-btn">-->
<!--					   <button type="button" id="video-trigger"  data-video="--><?php //echo $video_url;?><!--">--><?php //echo esc_html($button_text);?><!--</button>-->
<!--				   </div>-->
					<?php if($video_url){ ?>
						<div class="button-area">
							<button class="video-btn" link="<?php echo $video_url ?>">
								<?php echo esc_html($button_text) ?>
							</button>
						</div>
					<?php	} ?>


				</div>

			</div>





		</div>

	</div>

<?php endwhile;

wp_reset_postdata();


?>

<div class="video-popup">
        <div class="iframe-wrapper">
            <span id="iframeHolder"></span>
            <div class="close-video">
                <span class="video-close-icon"></span>
            </div>
        </div>
    </div>



<script>
	(function ($) {
		// $(document).on('click', '#video-trigger', function(){
        //        console.log(attr('data-video'));
		// });

		//Video Popup
		$('.video-btn').click(function(){
			$('.video-popup').fadeIn();
			$('#iframeHolder').html('');
			var link = $(this).attr("link");
			$('#iframeHolder').html('<iframe src="'+link+'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>');
		});
		$('.close-video').click(function(){
			$('.video-popup').fadeOut();
			$('#iframeHolder').html('');
		});

		$('.iframeHolder iframe').after().click(function () {
			alert('clickable!');
		});

	}(jQuery));
</script>


