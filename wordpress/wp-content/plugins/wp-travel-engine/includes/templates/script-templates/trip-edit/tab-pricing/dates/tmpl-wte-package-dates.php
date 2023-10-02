<script type="text/html" id="tmpl-wte-package-dates">
	<div class="wpte-block-content">
		<div class="wpte-block-heading">
			<h4><?php esc_html_e( 'Fixed Departure Dates', 'wp-travel-engine' ); ?></h4>
		</div>
		<div class="wpte-info-block">
		<p>
		<?php
		echo wp_kses(
			sprintf(
				__( 'By default, this trip can be booked throughout the year. Do you have trips with fixed departure dates and want them booked only on these days? Trip Fixed Starting Dates extension allows you to set specific dates when the trips can be booked. %1$sGet Trip Fixed Starting Dates extension now%2$s.', 'wp-travel-engine' ),
				'<a target="_blank" href="https://wptravelengine.com/downloads/trip-fixed-starting-dates/?utm_source=setting&amp;utm_medium=customer_site&amp;utm_campaign=setting_addon">',
				'</a>'
			),
			array(
				'a' => array(
					'href'   => array(),
					'target' => array(),
				),
			)
		);
		?>
		</p>
		</div>
	</div>
</script>
