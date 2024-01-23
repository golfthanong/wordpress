<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/* Select pre-built website step */

$demos = us_get_demo_import_config();

if ( empty( $demos ) ) {
	echo '<h2 style="text-align: center;">' . us_translate( 'Connection lost or the server is busy. Please try again later.' ) . '</h2>';
	return;
}

// Deactivate WP importer plugin to avoid conflicts
if ( is_plugin_active( 'wordpress-importer/wordpress-importer.php' ) ) {
	deactivate_plugins( 'wordpress-importer/wordpress-importer.php' );
}

// Show the notification if the theme has new version to update
$theme_update_notification = '';
if (
	$update_themes = get_site_transient( 'update_themes' )
	AND ! empty( $update_themes->response )
	AND isset( $update_themes->response[ US_THEMENAME ] )
) {
	$theme_update_notification = '<div class="us-wizard-notification"><span>';
	$theme_update_notification .= sprintf(
		__( 'Some of demo data may be imported incorrectly, because you are using outdated %s version. %sUpdate the theme%s to import demos without possible issues.', 'us' ),
		US_THEMENAME,
		'<strong><a href="' . admin_url( 'themes.php' ) . '">',
		'</a></strong>'
	);
	$theme_update_notification .= '</span></div>';
}

global $help_portal_url;
$help_portal_preview_url = $help_portal_url . '/uploads/demos/';
$help_portal_preview_url .= ( defined( 'US_ACTIVATION_THEMENAME' ) )
	? trailingslashit( strtolower( US_ACTIVATION_THEMENAME ) )
	: trailingslashit( strtolower( US_THEMENAME ) );

// Get data for filters
$filters = array();
foreach ( $demos as $name => $demo_data ) {
	$tags = $demo_data['tags'];
	if ( ! empty( $tags ) ) {
		$tags = trim( strtolower( ucwords( $tags ) ) );
		$demos[ $name ]['tags'] = $tags;

		$tags = explode( ',', $tags );
		$tags = array_map( 'trim', $tags );

		$filters = array_merge( $filters, $tags );
	}
}

// Get array with count
$filters = array_count_values( $filters );

// Sort alphabetically
ksort( $filters );
?>
<div class="us-wizard-step prebuilt_site">
	<?php
	if ( ! empty( $filters ) ) {
		?>
		<div class="us-wizard-demos-filters">
			<label class="us-wizard-demos-filter" data-name="all">
				<input class="screen-reader-text" type="radio" name="filter" value="all" checked>
				<span><?= __( 'All websites', 'us' ) ?> <i><?= count( $demos ) ?></i></span>
			</label>
			<?php foreach ( $filters as $title => $count ) { ?>
				<label class="us-wizard-demos-filter" data-name="<?= $title ?>">
					<input class="screen-reader-text" type="radio" name="filter" value="<?= $title ?>">
					<span><?= $title ?> <i><?= $count ?></i></span>
				</label>
			<?php } ?>
		</div>
		<?php
	}
	?>
	<div class="us-wizard-demos">
		<?= $theme_update_notification ?>
		<div class="us-wizard-step-title"><?= __( 'Select pre-built website', 'us' ) ?></div>
		<div class="us-wizard-demos-list">
		<?php
		foreach ( $demos as $name => $demo_data ) {
			?>
			<div class="us-wizard-demos-item" data-demo-id="<?= $name ?>" data-tags="<?= $demo_data['tags'] ?>" role="button" title="<?= us_translate( 'Select' ) ?>">
				<h2 class="us-wizard-demos-item-title"><?= $demo_data['title'] ?></h2>
				<div id="demo_preview_<?= $name ?>">
					<img src="<?= $help_portal_preview_url . $name . '/preview.jpg' ?>" width="1070" height="730" loading="lazy" alt="">
				</div>
				<a class="fas" href="<?= $demo_data['preview_url']; ?>" target="_blank" title="<?= us_translate( 'Preview' ) ?>"></a>
				<div class="us-wizard-demos-item-options hidden" id="demo_content_<?= $name ?>">
					<label class="content">
						<input type="checkbox" value="ON" name="content_all" checked="checked" class="parent_checkbox">
						<span class="title"><?= us_translate( 'All content' ) ?></span>
						<span class="us-tooltip">
							<i class="fas"></i>
							<span><?= __( 'The images used in live demos will be replaced by placeholders due to copyright/license reasons.', 'us' ) ?></span>
						</span>
					</label>
					<?php if ( in_array( 'pages', $demo_data['content'] ) ) { ?>
						<label class="child">
							<input type="checkbox" value="ON" name="content_pages" checked class="child_checkbox">
							<span class="title"><?= us_translate( 'Pages' ) ?></span>
						</label>
					<?php } ?>
					<?php if ( in_array( 'posts', $demo_data['content'] ) ) { ?>
						<label class="child">
							<input type="checkbox" value="ON" name="content_posts" checked class="child_checkbox">
							<span class="title"><?= us_translate( 'Posts' ) ?></span>
						</label>
					<?php } ?>
					<?php if ( in_array( 'portfolio_items', $demo_data['content'] ) ) { ?>
						<label class="child">
							<input type="checkbox" value="ON" name="content_portfolio" checked class="child_checkbox">
							<span class="title"><?= __( 'Portfolio', 'us' ) ?></span>
						</label>
					<?php } ?>
					<?php if ( in_array( 'testimonials', $demo_data['content'] ) ) { ?>
						<label class="child">
							<input type="checkbox" value="ON" name="content_testimonials" checked class="child_checkbox">
							<span class="title"><?= __( 'Testimonials', 'us' ) ?></span>
						</label>
					<?php } ?>
					<?php if ( in_array( 'headers', $demo_data['content'] ) ) { ?>
						<label class="child">
							<input type="checkbox" value="ON" name="content_headers" checked class="child_checkbox">
							<span class="title"><?= _x( 'Headers', 'site top area', 'us' ) ?></span>
						</label>
					<?php } ?>
					<?php if ( in_array( 'grid_layouts', $demo_data['content'] ) ) { ?>
						<label class="child">
							<input type="checkbox" value="ON" name="content_grid_layouts" checked class="child_checkbox">
							<span class="title"><?= __( 'Grid Layouts', 'us' ) ?></span>
						</label>
					<?php } ?>
					<?php if ( in_array( 'content_templates', $demo_data['content'] ) ) { ?>
						<label class="child">
							<input type="checkbox" value="ON" name="content_content_templates" checked class="child_checkbox">
							<span class="title"><?= __( 'Page Templates', 'us' ) ?></span>
						</label>
					<?php } ?>
					<?php if ( in_array( 'page_blocks', $demo_data['content'] ) ) { ?>
						<label class="child">
							<input type="checkbox" value="ON" name="content_page_blocks" checked class="child_checkbox">
							<span class="title"><?= __( 'Reusable Blocks', 'us' ) ?></span>
						</label>
					<?php } ?>

					<label class="theme-options">
						<input type="checkbox" value="ON" name="content_theme_options" checked class="parent_checkbox">
						<span class="title"><?= __( 'Theme Options', 'us' ) ?></span>
					</label>

					<?php if ( in_array( 'products', $demo_data['content'] ) ) { ?>
						<label class="woocommerce">
							<input type="checkbox" value="ON" name="content_woocommerce" checked class="parent_checkbox">
							<span class="title"><?= __( 'Shop Products', 'us' ) ?></span>
						</label>
					<?php } ?>
					<?php if ( in_array( 'acf_fields', $demo_data['content'] ) ) { ?>
						<label class="acf_fields">
							<input type="checkbox" value="ON" name="content_acf" checked class="parent_checkbox">
							<span class="title"><?= us_translate( 'Custom Fields' ) ?></span>
						</label>
					<?php } ?>
				</div>
			</div>
			<?php
		}
		?>
		</div>
	</div>
</div>

<?php /* Select pre-built content step */ ?>
<div class="us-wizard-step prebuilt_content">
	<?= $theme_update_notification ?>
	<div class="us-wizard-step-title"><?= __( 'Select pre-built content', 'us' ) ?></div>
	<div class="us-wizard-step-row">
		<div class="us-wizard-preview-image"></div>
		<div class="us-wizard-content-options"></div>
	</div>
	<div class="us-wizard-step-footer">
		<button class="button button-primary action-select-content">
			<?= __( 'Next Step', 'us' ) ?>
		</button>
	</div>
</div>
