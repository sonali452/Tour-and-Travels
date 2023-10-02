<?php
use WPTravelEngine\Packages;

$pricing_categories = Packages\get_packages_pricing_categories();

$options = array();

foreach ( $pricing_categories as $pricing_category ) {
	$options[ $pricing_category->term_id ] = array(
		'label'      => $pricing_category->name,
		'attributes' => array(
			'selected' => '{{primaryCategory == ' . $pricing_category->term_id . " ? ' selected ' : ''}}",
		),
	);
}
?>

<script type="text/html" id="tmpl-wte-package-general">
	<#
	var tripPackage = data.tripPackage
	var idSuffix = '_' + tripPackage.id
	var index = +tripPackage.id
	var primaryCategory = tripPackage.primary_pricing_category

	var categories = {}
	console.debug(data.categories)
	#>
	<div class="wpte-block-content">
		<?php
		$field_builder = new WTE_Field_Builder_Admin(
			array(
				array(
					'label'   => __( 'Primary Category', 'wp-travel-engine' ),
					'name'    => 'packages_primary_category[{{tripPackage.id}}]',
					'type'    => 'select',
					'options' => $options,
				),
			)
		);

		$field_builder->render();
		?>
	</div>
</script>
