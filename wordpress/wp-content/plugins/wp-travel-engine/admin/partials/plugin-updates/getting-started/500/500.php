<?php
/**
 * WP Travel Release Changelog.
 *
 * @package wptravelengine/partials/plugin-updates
 */
$major_version = $major_version . '.0';

$images = array(
	'multiple-packages'            => "{$assets_path_url}multiple-packages.png",
	'pricing-categories'           => "{$assets_path_url}pricing-categories.png",
	'available-pricing-categories' => "{$assets_path_url}available-pricing-categories.png",
	'new-booking-flow'             => "{$assets_path_url}new-booking-flow.gif",
);
?>
<div class="wrap wte_welcome__container">

	<div class="wte_about__header">
		<div class="wte_about__header-title">
			<h1>
				<span><?php echo esc_html( $major_version ); ?></span>
			</h1>
			<span></span>
		</div>
		<div class="wte_about__header-badge">
			<img
				src="https://ps.w.org/wp-travel-engine/assets/icon-256x256.png"
				alt=""
			/>
		</div>

		<div class="wte_about__header-text">
			<p>
				<?php esc_html_e( 'WP Travel Engine - Major Update Release Notes', 'wp-travel-engine' ); ?>
			</p>
		</div>

		<nav
			class="wte_about__header-navigation nav-tab-wrapper wp-clearfix"
			aria-label="Secondary menu"
		>
			<a
				style="pointer-events:none;"
				href="#"
				class="nav-tab nav-tab-active"
				aria-current="page"
			><?php esc_html_e( 'What’s New', 'wp-travel-engine' ); ?></a>
		</nav>
	</div>

	<div class="wte_about__section changelog">
		<div class="column">
			<h2><?php _e( 'Introducing Multiple Pricing Packages', 'wp-travel-engine' ); ?></h2>
			<p>This is one of the most requested features in WP Travel Engine. And we hear you. You can now add unlimited packages with prices irrespective of the pricing categories.</p>
			<p>In the previous versions, users had a level of multiple pricing, but it was limited. One of the WP Travel Engine Extensions, WP Travel Engine - Trip Fixed Starting Dates, has been providing the option to add a single price for each pricing category (adult, child, and infant). So, the user had to opt for a price according to age group.</p>
			<p>But now, this new Multiple Package feature overcomes the limitations of WP Travel Engine - Trip Fixed Starting Dates. So, the user can choose from a variety of packages apart from the existing age-wise pricing categories.</p>
			<p>You can create the packages as per your convenience and market demand. You also have complete control over the customization of these packages.</p>
		</div>
	</div>
	<div class="wte_about__section changelog">
		<div class="column">
			<div class="about__image aligncenter">
				<figure>
					<img
						src="<?php echo esc_url( $images['multiple-packages'] ); ?>"
						alt=""
					/>
					<figcaption>New Multiple Packages UI</figcaption>
				</figure>
			</div>
		</div>
	</div>
	<hr />
	<div class="wte_about__section has-subtle-background-color">
		<div class="column">
			<h2><?php _e( 'Multiple Pricing Categories', 'wp-travel-engine' ); ?></h2>
			<p>The new version of WP Travel Engine is optimized to add unlimited pricing categories for travel booking. This allows you to customize the prices for every package created for any trip.</p>
			<p>Pricing category is a group term to assign prices. In the previous versions, pricing categories were limited to age groups like adult, child, infant, etc., which is no longer the case.</p>
			<p>From WP Travel Engine 5.0 onwards, you are free to include a number of other groupings in pricing categories like Sale Price, Limited Offer, Group Discounts, etc. In addition, these categories can be added and removed as per your requirement, which was not possible prior to this major update.</p>
		</div>
	</div>
	<div class="wte_about__section has-2-columns has-subtle-background-color">
		<div class="column">
			<div class="about__image aligncenter">
				<figure>
					<img
						src="<?php echo esc_url( $images['pricing-categories'] ); ?>"
						alt=""
					/>
					<figcaption>New Pricing Categories UI - Trip Edit</figcaption>
				</figure>
			</div>
		</div>
		<div class="column">
			<div class="about__image aligncenter">
				<figure>
					<img
						src="<?php echo esc_url( $images['available-pricing-categories'] ); ?>"
						alt=""
					/>
					<figcaption>Pricing Catgories Taxonomy</figcaption>
				</figure>
			</div>
		</div>
	</div>
	<hr />
	<div class="wte_about__section">
		<div class="column">
			<h2>Multiple Dates and Times</h2>
			<p>WP Travel Engine 5.0 has gone through an extensive modification regarding the selection of trip dates. We have also added a new feature to choose a particular time slot for starting any trip which was not available in legacy versions.</p>
			<p>Previously, the Dates tab was available on the Trip Edit page, from where the user could select particular dates for their trip while booking. Since each package can hold dates, the Dates tab has been removed from the Trip Edit page. It now appears under Dates inside the tab, Pricings and Dates.</p>
		</div>
	</div>
	<hr />
	<div class="wte_about__section has-subtle-background-color">
		<div class="column">
			<h2><?php _e( 'New Trip Booking User Interface', 'wp-travel-engine' ); ?></h2>
			<p>There are some major changes in the booking flow as well. The Trip Booking User Interface, as a whole, has been upgraded for an advanced booking experience.</p>

			In the previous version, the booking window appeared on the sidebar at the right-hand side of the booking page. The booking flow went as follows :
			<p><i>Select a trip</i> > <i>Select the dates</i> > <i>Add travelers</i> > <i>Add extra services</i> > <i>Book now</i></p>

			<p>In the updated version, the booking window appears on the main screen, whereas the sidebar at the right-hand side is used for displaying the booking summary. The booking procedure now goes as follows :</p>
			<p><i>Select a trip</i> > <i>Select date and time</i> > <i>Add travelers under package type</i> > <i>Add extra services</i> > <i>Proceed to checkout</i></p>

		</div>
	</div>
	<div class="wte_about__section">
		<div class="column">
			<div class="about__image aligncenter">
				<figure>
					<img
						src="<?php echo esc_url( $images['new-booking-flow'] ); ?>"
						alt=""
					/>
					<figcaption>New Trip Booking UI in action</figcaption>
				</figure>
			</div>
		</div>
	</div>
	<hr />

	<div class="wte_about__section changelog">
		<div class="column">
			<h2><?php _e( 'Important Notes:', 'wp-travel-engine' ); ?></h2>
			<p>
				<?php _e( 'If you are using any of the following addons,', 'wp-travel-engine' ); ?>
				<?php _e( 'we strongly recommend you to update the addons to the minimum required version to ensure the productivity with the new booking process and its features.', 'wp-travel-engine' ); ?>
			</p>
			<table>
				<thead>
					<tr>
						<th>Plugin Name</th>
						<th>Compatible Version</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ( array(
						'Trip Fixed Starting Dates' => '2.3.0',
						'Group Discount'            => '2.1.0',
						'Extra Services'            => '2.1.0',
					) as $name => $version ) {
						printf(
							'<tr>'
							. '<td>WP Travel Engine - %1$s</td>'
							. '<td align="center"><code>%2$s</code></td>'
							. '</tr>',
							$name,
							$version
						);
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
	<hr/>
	<div class="wte_about__section">
		<h2 class="is-section-header"><?php _e( 'Need further assistance?', 'wp-travel-engine' ); ?></h2>

		<div class="column">
			<h3><?php _e( 'Contact Support', 'wp-travel-engine' ); ?></h3>
			<p>
				<?php _e( 'As always, if you have any queries regarding the features or any add-ons, just send us an email to', 'wp-travel-engine' ); ?>
				<a
					href="mailto:support@wptravelengine.com"
					target="_blank"
				><?php _e( 'support@wptravelengine.com', 'wp-travel-engine' ); ?></a>
				<?php _e( 'or raise a ticket at', 'wp-travel-engine' ); ?> <a
					href="https://wptravelengine.com/support-ticket/"
					target="_blank"
				><?php _e( 'https://wptravelengine.com/support-ticket/', 'wp-travel-engine' ); ?></a>
			</p>
		</div>
	</div>

	<hr>

	<div class="return-to-dashboard">
		<a href="<?php echo esc_url( admin_url() ); ?>"><?php _e( 'Go to Dashboard → Home', 'wp-travel-engine' ); ?></a>
	</div>
</div>
