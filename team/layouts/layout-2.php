<?php

while ( $loop->have_posts() ) : $loop->the_post();

	?>
	<div class="hip-team-member-container with-bio-layout">
		<div class="hip-team-member-box">
			<div class="hip-team-box__layer hip-team-box__front">
				<?php $this->render_post_thumbnails(); ?>
				<div class="team-name-designation">
					<?php $this->render_team_name();?>
					<?php $this->render_team_designation();?>
				</div>

				<?php $this->render_team_bio();?>

			</div>
			<div class="hip-team-box__layer hip-team-box__back">
				<?php  $this->render_team_back_bio();?>
			</div>

		</div>

	</div>

<?php endwhile;

wp_reset_postdata();
