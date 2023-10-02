<?php
/**
 * Template part for displaying list posts
 *
 * This template can be overridden by copying it to yourtheme/wp-travel-engine/content-list.php.
 *
 * @package Wp_Travel_Engine
 * @subpackage Wp_Travel_Engine/includes/templates
 * @since @release-version //TODO: change after travel muni is live
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$is_featured = wte_is_trip_featured( get_the_ID() );
?>
<div class="category-trips-single<?php echo $is_featured ? ' __featured-trip' : ''; ?>" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
	<div class="category-trips-single-inner-wrap">
		<figure class="category-trip-fig">
			<?php if ( $is_featured ) : ?>
				<div class="category-feat-ribbon">
					<span class="category-feat-ribbon-txt"><?php echo esc_html__( 'Featured', 'wp-travel-engine' ); ?></span>
					<span class="cat-feat-shadow"></span>
				</div>
			<?php endif; ?>

			<a href="<?php the_permalink(); ?>">
				<?php
				$size = apply_filters( 'wp_travel_engine_archive_trip_feat_img_size', 'destination-thumb-trip-size' );
				if ( has_post_thumbnail() ) :
					the_post_thumbnail( $size );
				endif;
				?>
			</a>

			<?php if ( $group_discount ) : ?>
			<div class="category-trip-group-avil">
				<span class="pop-trip-grpavil-icon">
					<svg xmlns="http://www.w3.org/2000/svg" width="17.492" height="14.72" viewBox="0 0 17.492 14.72">
						<g id="Group_898" data-name="Group 898" transform="translate(-452 -737)">
							<g id="Group_757" data-name="Group 757" transform="translate(12.114)">
								<g id="multiple-users-silhouette" transform="translate(439.886 737)">
									<path id="Path_23387" data-name="Path 23387" d="M10.555,8.875a3.178,3.178,0,0,1,1.479,2.361,2.564,2.564,0,1,0-1.479-2.361ZM8.875,14.127a2.565,2.565,0,1,0-2.566-2.565A2.565,2.565,0,0,0,8.875,14.127Zm1.088.175H7.786A3.289,3.289,0,0,0,4.5,17.587v2.662l.007.042.183.057a14.951,14.951,0,0,0,4.466.72,9.168,9.168,0,0,0,3.9-.732l.171-.087h.018V17.587A3.288,3.288,0,0,0,9.963,14.3Zm4.244-2.648h-2.16a3.162,3.162,0,0,1-.976,2.2,3.9,3.9,0,0,1,2.788,3.735v.82a8.839,8.839,0,0,0,3.443-.723l.171-.087h.018V14.938A3.288,3.288,0,0,0,14.207,11.654Zm-9.834-.175a2.548,2.548,0,0,0,1.364-.4A3.175,3.175,0,0,1,6.931,9.058c0-.048.007-.1.007-.144a2.565,2.565,0,1,0-2.565,2.565Zm2.3,2.377a3.163,3.163,0,0,1-.975-2.19c-.08-.006-.159-.012-.241-.012H3.285A3.288,3.288,0,0,0,0,14.938V17.6l.007.041L.19,17.7a15.4,15.4,0,0,0,3.7.7v-.8A3.9,3.9,0,0,1,6.677,13.856Z" transform="translate(0 -6.348)" fill="#fff"/>
								</g>
							</g>
						</g>
					</svg>
				</span>
				<span class="pop-trip-grpavil-txt"><?php echo apply_filters( 'wp_travel_engine_group_discount_available_text', __( 'Group discount Available', 'wp-travel-engine' ) ); ?></span>
			</div>
			<?php endif; ?>
		</figure>
		<div class="category-trip-content-wrap">
			<div class="category-trip-prc-title-wrap">
				<h2 class="category-trip-title" itemprop="name">
					<a itemprop="url" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				</h2>
				<?php if ( ! empty( $j ) ) : ?>
					<meta itemprop="position" content="<?php echo $j; ?>"/>
				<?php endif; ?>
				<?php if ( wp_travel_engine_trip_has_reviews( get_the_ID() ) ) : ?>
					<div class="category-trip-review">
						<div class="rating-rev rating-layout-1 smaller-ver">
							<?php do_action( 'wte_trip_average_rating_star' ); ?>
						</div>
						<span class="category-trip-reviewcount">
							<?php do_action( 'wte_trip_average_rating_based_on_text' ); ?>
						</span>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $j ) ) : ?>
					<meta itemprop="position" content="<?php echo $j; ?>"/>
				<?php endif; ?>
			</div>
			<div class="category-trip-detail-wrap">
				<div class="category-trip-prc-wrap">
					<div class="category-trip-desti">
						<?php if ( ! empty( $destination ) ) : ?>
						<span class="category-trip-loc">
							<i><svg width="12" height="15" viewBox="0 0 12 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 0C4.4087 0 2.88258 0.632141 1.75736 1.75736C0.632141 2.88258 0 4.4087 0 6C0 10.05 5.2875 14.625 5.5125 14.82C5.64835 14.9362 5.82124 15 6 15C6.17877 15 6.35165 14.9362 6.4875 14.82C6.75 14.625 12 10.05 12 6C12 4.4087 11.3679 2.88258 10.2426 1.75736C9.11742 0.632141 7.5913 0 6 0ZM6 13.2375C4.4025 11.7375 1.5 8.505 1.5 6C1.5 4.80653 1.97411 3.66193 2.81802 2.81802C3.66193 1.97411 4.80653 1.5 6 1.5C7.19347 1.5 8.33807 1.97411 9.18198 2.81802C10.0259 3.66193 10.5 4.80653 10.5 6C10.5 8.505 7.5975 11.745 6 13.2375ZM6 3C5.40666 3 4.82664 3.17595 4.33329 3.50559C3.83994 3.83524 3.45542 4.30377 3.22836 4.85195C3.0013 5.40013 2.94189 6.00333 3.05764 6.58527C3.1734 7.16721 3.45912 7.70176 3.87868 8.12132C4.29824 8.54088 4.83279 8.8266 5.41473 8.94236C5.99667 9.05811 6.59987 8.9987 7.14805 8.77164C7.69623 8.54458 8.16477 8.16006 8.49441 7.66671C8.82405 7.17336 9 6.59334 9 6C9 5.20435 8.68393 4.44129 8.12132 3.87868C7.55871 3.31607 6.79565 3 6 3ZM6 7.5C5.70333 7.5 5.41332 7.41203 5.16665 7.2472C4.91997 7.08238 4.72771 6.84811 4.61418 6.57403C4.50065 6.29994 4.47094 5.99834 4.52882 5.70736C4.5867 5.41639 4.72956 5.14912 4.93934 4.93934C5.14912 4.72956 5.41639 4.5867 5.70737 4.52882C5.99834 4.47094 6.29994 4.50065 6.57403 4.61418C6.84811 4.72771 7.08238 4.91997 7.2472 5.16665C7.41203 5.41332 7.5 5.70333 7.5 6C7.5 6.39782 7.34197 6.77936 7.06066 7.06066C6.77936 7.34196 6.39783 7.5 6 7.5Z" fill="white" /></svg></i>
							<span><?php echo wp_kses_post( $destination ); ?></span>
						</span>
						<?php endif; ?>

						<?php if ( $trip_duration ) : ?>
						<span class="category-trip-dur">
							<i><svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.4375 1.625C6.99123 1.625 5.57743 2.05387 4.3749 2.85738C3.17236 3.66089 2.2351 4.80294 1.68163 6.13913C1.12817 7.47531 0.983357 8.94561 1.26551 10.3641C1.54767 11.7826 2.24411 13.0855 3.26678 14.1082C4.28946 15.1309 5.59242 15.8273 7.0109 16.1095C8.42939 16.3916 9.89969 16.2468 11.2359 15.6934C12.5721 15.1399 13.7141 14.2026 14.5176 13.0001C15.3211 11.7976 15.75 10.3838 15.75 8.9375C15.75 7.97721 15.5609 7.02632 15.1934 6.13913C14.8259 5.25193 14.2872 4.44581 13.6082 3.76678C12.9292 3.08775 12.1231 2.54912 11.2359 2.18163C10.3487 1.81414 9.39779 1.625 8.4375 1.625ZM8.4375 14.7875C7.28048 14.7875 6.14944 14.4444 5.18742 13.8016C4.22539 13.1588 3.47558 12.2451 3.03281 11.1762C2.59004 10.1072 2.47419 8.93101 2.69991 7.79622C2.92563 6.66143 3.48279 5.61906 4.30093 4.80092C5.11907 3.98279 6.16144 3.42563 7.29622 3.19991C8.43101 2.97418 9.60725 3.09003 10.6762 3.5328C11.7451 3.97558 12.6588 4.72539 13.3016 5.68741C13.9444 6.64944 14.2875 7.78048 14.2875 8.9375C14.2875 10.489 13.6712 11.977 12.5741 13.0741C11.477 14.1712 9.98902 14.7875 8.4375 14.7875ZM10.7044 9.39819L9.16875 8.51337V5.28125C9.16875 5.08731 9.09171 4.90131 8.95457 4.76418C8.81744 4.62704 8.63144 4.55 8.4375 4.55C8.24356 4.55 8.05757 4.62704 7.92043 4.76418C7.78329 4.90131 7.70625 5.08731 7.70625 5.28125V8.9375C7.70625 8.9375 7.70625 8.996 7.70625 9.02525C7.71058 9.07563 7.72293 9.125 7.74282 9.1715C7.75787 9.21488 7.77748 9.25655 7.80131 9.29581C7.82132 9.33737 7.84585 9.37661 7.87444 9.41281L7.99144 9.50787L8.05725 9.57369L9.9585 10.6706C10.0699 10.7337 10.196 10.7665 10.3241 10.7656C10.486 10.7668 10.6437 10.7141 10.7725 10.616C10.9013 10.5178 10.9938 10.3797 11.0357 10.2233C11.0775 10.0669 11.0662 9.90098 11.0036 9.75166C10.941 9.60233 10.8306 9.47801 10.6898 9.39819H10.7044Z" fill="black" fill-opacity="0.3"/></svg></i>
							<span><?php printf( _nx( '%1$d Day', '%1$d Days', $trip_duration, 'trip duration', 'wp-travel-engine' ), $trip_duration ); ?></span>

							<?php if ( false && $trip_duration_nights ) : ?>
								<span><?php printf( _nx( ' - %1$d Night', ' - %1$d Nights', $trip_duration_nights, 'trip duration night', 'wp-travel-engine' ), $trip_duration_nights ); ?></span>
							<?php endif; ?>
						</span>
						<?php endif; ?>
					</div>
					<?php if ( ! empty( $display_price ) ) : ?>
						<div class="category-trip-budget">
							<?php if ( $discount_percent ) : ?>
								<div class="category-disc-feat-wrap">
									<div class="category-trip-discount">
										<span class="discount-offer">
											<span><?php echo sprintf( __( '%1$s%% ', 'wp-travel-engine' ), $discount_percent ); ?></span>
										<?php _e( 'Off', 'wp-travel-engine' ); ?></span>
									</div>
								</div>
							<?php endif; ?>
							<span class="price-holder">
								<span class="actual-price"><?php echo wte_get_formated_price( $display_price ); ?></span>
								<?php if ( $on_sale ) : ?>
									<span class="striked-price"><?php echo wte_get_formated_price( $trip_price ); ?></span>
								<?php endif; ?>
							</span>
						</div>
					<?php endif; ?>
				</div>
				<?php if ( $show_excerpt ) : ?>
				<div class="category-trip-desc">
					<p><?php echo wp_trim_words( get_the_excerpt(), 22, '...' ); ?></p>
				</div>
				<?php endif; ?>
			</div>
		</div>

		<?php
		$fsds = apply_filters( 'trip_card_fixed_departure_dates', get_the_ID() );

		echo '<div class="category-trip-aval-time">';
		if ( false !== $fsds ) {
			if ( $fsds == get_the_ID() || empty( $fsds ) ) {
				echo '<div class="category-trip-avl-tip-inner-wrap">';
				echo '<span class="category-available-trip-text"> ' . __( 'Available through out the year:', 'wp-travel-engine' ) . '</span>';
				echo '<ul class="category-available-months">';
				foreach ( range( 1, 12 ) as $month_number ) :
					echo '<li>' . date_i18n( 'M', strtotime( "2021-{$month_number}-01" ) ) . '</li>';
				endforeach;
				echo '</ul></div>';
			} elseif ( is_array( $fsds ) && count( $fsds ) > 0 ) {
				// echo '<div class="category-trip-aval-time">';
				switch ( $dates_layout ) {
					case 'months_list':
						$available_months = array_map(
							function( $fsd ) {
								return date_i18n( 'n', strtotime( $fsd['start_date'] ) );
							},
							$fsds
						);
						$available_months = array_flip( $available_months );

						if ( empty( $available_months ) ) {
							echo '<ul class="category-available-months">';
							foreach ( range( 1, 12 ) as $month_number ) :
								echo '<li>' . date_i18n( 'n-M', strtotime( "2021-{$month_number}-01" ) ) . '</li>';
							endforeach;
							echo '</ul>';
							break;
						}

						$availability_txt     = ! empty( $available_months ) && is_array( $available_months ) ? __( 'Available in the following months:', 'wp-travel-engine' ) : __( 'Available through out the year:', 'wp-travel-engine' );
						$available_throughout = apply_filters( 'wte_available_throughout_txt', $availability_txt );

						echo '<div class="category-trip-avl-tip-inner-wrap">';
						echo '<span class="category-available-trip-text"> ' . esc_html( $available_throughout ) . '</span>';
						$months_list = '';
						echo '<ul class="category-available-months">';
						foreach ( range( 1, 12 ) as $month_number ) {
							isset( $available_months[ $month_number ] ) ? printf( '<li><a href="%1$s">%2$s</a></li>', esc_url( get_the_permalink() ) . '?month=' . esc_html( $available_months[ $month_number ] ) . '#wte-fixed-departure-dates', date_i18n( 'M', strtotime( "2021-{$month_number}-01" ) ) ) : printf( '<li><a href="#" class="disabled">%1$s</a></li>', date_i18n( 'M', strtotime( "2021-{$month_number}-01" ) ) );
						}
						echo '</ul>';
						echo '</div>';
						break;
					case 'dates_list':
						$settings = get_option( 'wp_travel_engine_settings', true );

						$list_count = isset( $settings['trip_dates']['number'] ) ? (int) $settings['trip_dates']['number'] : 3;
						$icon       = '<i><svg xmlns="http://www.w3.org/2000/svg" width="17.332" height="15.61" viewBox="0 0 17.332 15.61"><g id="Group_773" data-name="Group 773" transform="translate(283.072 34.13)"><path id="Path_23383" data-name="Path 23383" d="M-283.057-26.176h.1c.466,0,.931,0,1.4,0,.084,0,.108-.024.1-.106-.006-.156,0-.313,0-.469a5.348,5.348,0,0,1,.066-.675,5.726,5.726,0,0,1,.162-.812,5.1,5.1,0,0,1,.17-.57,9.17,9.17,0,0,1,.383-.946,10.522,10.522,0,0,1,.573-.96c.109-.169.267-.307.371-.479a3.517,3.517,0,0,1,.5-.564,6.869,6.869,0,0,1,1.136-.97,9.538,9.538,0,0,1,.933-.557,7.427,7.427,0,0,1,1.631-.608c.284-.074.577-.11.867-.162a7.583,7.583,0,0,1,1.49-.072c.178,0,.356.053.534.062a2.673,2.673,0,0,1,.523.083c.147.038.3.056.445.1.255.07.511.138.759.228a6.434,6.434,0,0,1,1.22.569c.288.179.571.366.851.556a2.341,2.341,0,0,1,.319.259c.3.291.589.592.888.882a4.993,4.993,0,0,1,.64.85,6.611,6.611,0,0,1,.71,1.367c.065.175.121.352.178.53s.118.348.158.526c.054.242.09.487.133.731.024.14.045.281.067.422a.69.69,0,0,1,.008.1c0,.244.005.488,0,.731s-.015.5-.04.745a4.775,4.775,0,0,1-.095.5c-.04.191-.072.385-.128.572-.094.312-.191.625-.313.926a7.445,7.445,0,0,1-.43.9c-.173.3-.38.584-.579.87a8.045,8.045,0,0,1-1.2,1.26,5.842,5.842,0,0,1-.975.687,8.607,8.607,0,0,1-1.083.552,11.214,11.214,0,0,1-1.087.36c-.19.058-.386.1-.58.137-.121.025-.245.037-.368.052a12.316,12.316,0,0,1-1.57.034,3.994,3.994,0,0,1-.553-.065c-.166-.024-.33-.053-.5-.082a1.745,1.745,0,0,1-.21-.043c-.339-.1-.684-.189-1.013-.317a7,7,0,0,1-1.335-.673c-.2-.136-.417-.263-.609-.415a6.9,6.9,0,0,1-.566-.517.488.488,0,0,1-.128-.331.935.935,0,0,1,.1-.457.465.465,0,0,1,.3-.223.987.987,0,0,1,.478-.059.318.318,0,0,1,.139.073c.239.185.469.381.713.559a5.9,5.9,0,0,0,1.444.766,5.073,5.073,0,0,0,.484.169c.24.062.485.1.727.154a1.805,1.805,0,0,0,.2.037c.173.015.346.033.52.036.3.006.6.01.9,0a3.421,3.421,0,0,0,.562-.068c.337-.069.676-.139,1-.239a6.571,6.571,0,0,0,.783-.32,5.854,5.854,0,0,0,1.08-.663,5.389,5.389,0,0,0,.588-.533,8.013,8.013,0,0,0,.675-.738,5.518,5.518,0,0,0,.749-1.274,9.733,9.733,0,0,0,.366-1.107,4.926,4.926,0,0,0,.142-.833c.025-.269.008-.542.014-.814a4.716,4.716,0,0,0-.07-.815,5.8,5.8,0,0,0-.281-1.12,5.311,5.311,0,0,0-.548-1.147,9.019,9.019,0,0,0-.645-.914,9.267,9.267,0,0,0-.824-.788,3.354,3.354,0,0,0-.425-.321,5.664,5.664,0,0,0-1.048-.581c-.244-.093-.484-.2-.732-.275a6.877,6.877,0,0,0-.688-.161c-.212-.043-.427-.074-.641-.109a.528.528,0,0,0-.084,0c-.169,0-.338,0-.506,0a5.882,5.882,0,0,0-1.177.1,6.79,6.79,0,0,0-1.016.274,6.575,6.575,0,0,0-1.627.856,6.252,6.252,0,0,0-1.032.948,6.855,6.855,0,0,0-.644.847,4.657,4.657,0,0,0-.519,1.017c-.112.323-.227.647-.307.979a3.45,3.45,0,0,0-.13.91,4.4,4.4,0,0,1-.036.529c-.008.086.026.1.106.1.463,0,.925,0,1.388,0a.122.122,0,0,1,.08.028c.009.009-.005.051-.019.072q-.28.415-.563.827c-.162.236-.33.468-.489.705-.118.175-.222.359-.339.535-.1.144-.2.281-.3.423-.142.2-.282.41-.423.615-.016.023-.031.047-.048.069-.062.084-.086.083-.142,0-.166-.249-.332-.5-.5-.746-.3-.44-.6-.878-.9-1.318q-.358-.525-.714-1.051c-.031-.045-.063-.09-.094-.134Z" transform="translate(0 0)"/><path id="Path_23384" data-name="Path 23384" d="M150.612,112.52c0,.655,0,1.31,0,1.966a.216.216,0,0,0,.087.178,4.484,4.484,0,0,1,.358.346.227.227,0,0,0,.186.087q1.616,0,3.233,0a.659.659,0,0,1,.622.4.743.743,0,0,1-.516,1.074,1.361,1.361,0,0,1-.323.038q-1.507,0-3.013,0a.248.248,0,0,0-.216.109,1.509,1.509,0,0,1-.765.511,1.444,1.444,0,0,1-1.256-2.555.218.218,0,0,0,.09-.207q0-1.916,0-3.831a.784.784,0,0,1,.741-.732.742.742,0,0,1,.761.544.489.489,0,0,1,.015.127Q150.612,111.547,150.612,112.52Z" transform="translate(-423.686 -141.471)"/></g></svg></i>';
						echo '<div class="next-trip-info">';
						printf( '<div class="fsd-title">%1$s</div>', esc_html__( 'Next Departure', 'wp-travel-engine' ) );
						echo '<ul class="next-departure-list">';
						foreach ( $fsds as $fsd ) {
							if ( --$list_count < 0 ) {
								break;
							}
							printf( '<li><span class="left">%1$s %2$s</span><span class="right">%3$s</span></li>', $icon, wte_get_formated_date( $fsd['start_date'] ), sprintf( __( '%s Available', 'wp-travel-engine' ), $fsd['seats_left'] ) );
						}
						echo '</ul>';
						echo '</div>';
						break;
					default:
						break;
				}
			}
		}
		echo '<a href="' . get_the_permalink() . '" class="button category-trip-viewmre-btn">' . apply_filters( 'wp_travel_engine_view_detail_txt', __( 'View Details', 'wp-travel-engine' ) ) . '</a>';
		echo '</div>';
		?>

	</div>
</div>

<?php
/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
