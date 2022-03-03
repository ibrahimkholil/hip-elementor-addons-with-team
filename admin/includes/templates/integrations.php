<!-- API Key area of admin area-->
<?php

if ( ! defined( 'ABSPATH' ) ) exit;

use HipAddons\Includes\Helper_Functions;

//Get settings
$settings = self::get_integrations_settings();

$locales = self::get_google_maps_prefixes();

?>

<div class="pa-section-content">
	<div class="row">
		<div class="col-full">
			<form action="" method="POST" id="hip-integrations" name="hip-integrations" class="hip-settings-form">
			<div id="pa-integrations-settings" class="pa-settings-tab">

				<div class="pa-section-info-wrap">
                    <div class="pa-section-info">
                        <h4><?php echo __('Google API Keys', 'premium-addons-for-elementor'); ?></h4>
                        <p><?php echo sprintf( __('Google APIs are used in Google Maps. If you don\'t have one, click %1$shere%2$s to get your key.', 'premium-addons-for-elementor'), '<a href="https://premiumaddons.com/docs/getting-your-api-key-for-google-reviews/" target="_blank">', '</a>'); ?></p>
					</div>
				</div>

				<table class="pa-maps-table">
					<tr>
						<td>
							<span class="pa-maps-circle-icon"></span>
							<h4 class="pa-api-title"><?php echo __('Google Maps API Key:', 'premium-addons-for-elementor'); ?></h4>
						</td>
						<td>
							<input name="hip-map-api" id="hip-map-api" type="text" placeholder="Maps API Key" value="<?php echo esc_attr( $settings['hip-map-api'] ); ?>">
						</td>
					</tr>
					<tr>
						<td>
							<span class="pa-maps-circle-icon"></span>
							<h4 class="pa-api-disable-title"><?php echo __('Google Maps Localization Language:', 'premium-addons-for-elementor'); ?></h4>
						</td>
						<td>
							<select name="hip-map-locale" id="hip-map-locale" class="placeholder placeholder-active">
									<option value=""><?php _e( 'Default', 'premium-addons-for-elementor' ); ?></option>
								<?php foreach ( $locales as $key => $value ) {
									$selected = '';
									if ( $key === $settings['hip-map-locale'] ) {
										$selected = 'selected="selected" ';
									}
								?>
								<option value="<?php echo esc_attr( $key ); ?>" <?php echo $selected; ?>><?php echo esc_attr( $value ); ?></option>
								<?php } ?>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<span class="pa-maps-circle-icon"></span>
							<h4 class="pa-api-disable-title"><?php echo __('Load Maps API JS File:','premium-addons-for-elementor'); ?></h4>
						</td>
						<td>
                            <input name="hip-map-disable-api" id="hip-map-disable-api" type="checkbox" <?php checked(1, $settings['hip-map-disable-api'], true) ?>>
                            <label for="hip-map-disable-api"></label>
                            <span>
                                <?php echo __('This will load API JS file if it\'s not loaded by another theme or plugin.', 'premium-addons-for-elementor'); ?>
                            </span>
						</td>
					</tr>
					<tr>
						<td>
							<span class="pa-maps-circle-icon"></span>
							<h4 class="pa-api-disable-title">
                                <?php echo __('Load Markers Clustering JS File:','premium-addons-for-elementor'); ?>
                            </h4>
						</td>
						<td>
                            <input name="hip-map-cluster" id="hip-map-cluster" type="checkbox" <?php checked(1, $settings['hip-map-cluster'], true) ?>>
                            <label for="hip-map-cluster"></label>
                            <span><?php echo __('This will load the JS file for markers clusters.', 'premium-addons-for-elementor'); ?></span>
						</td>
					</tr>
				</table>

				<input type="submit" value="<?php echo __('Save Settings', 'premium-addons-for-elementor'); ?>" class="button pa-btn pa-save-button">
				<p class="result"></p>
			</div>
			</form> <!-- End Form -->
		</div>
	</div>
</div> <!-- End Section Content -->