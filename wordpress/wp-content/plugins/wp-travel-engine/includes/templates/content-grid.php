<?php
/**
 * Template part for displaying grid posts
 *
 * This template can be overridden by copying it to yourtheme/wp-travel-engine/content-grid.php.
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
			<?php if ( wte_is_trip_featured( get_the_ID() ) ) : ?>
				<div class="category-feat-ribbon">
					<span class="category-feat-ribbon-txt"><?php echo esc_html__( 'Featured', 'wp-travel-engine' ); ?></span>
					<span class="cat-feat-shadow"></span>
				</div>
			<?php endif; ?>
			<a href="<?php the_permalink(); ?>">
			<?php
			$size = apply_filters( 'wp_travel_engine_archive_trip_feat_img_size', 'destination-thumb-trip-size' );
			if ( has_post_thumbnail() ) {
				the_post_thumbnail( $size );
			}
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
			</div>

			<div class="category-trip-detail-wrap">
				<div class="category-trip-detail-wrap">
					<div class="category-trip-prc-wrap">
						<div class="category-trip-desti">
							<?php if ( ! empty( $destination ) ) : ?>
								<span class="category-trip-loc">
									<i><svg width="12" height="15" viewBox="0 0 12 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 0C4.4087 0 2.88258 0.632141 1.75736 1.75736C0.632141 2.88258 0 4.4087 0 6C0 10.05 5.2875 14.625 5.5125 14.82C5.64835 14.9362 5.82124 15 6 15C6.17877 15 6.35165 14.9362 6.4875 14.82C6.75 14.625 12 10.05 12 6C12 4.4087 11.3679 2.88258 10.2426 1.75736C9.11742 0.632141 7.5913 0 6 0ZM6 13.2375C4.4025 11.7375 1.5 8.505 1.5 6C1.5 4.80653 1.97411 3.66193 2.81802 2.81802C3.66193 1.97411 4.80653 1.5 6 1.5C7.19347 1.5 8.33807 1.97411 9.18198 2.81802C10.0259 3.66193 10.5 4.80653 10.5 6C10.5 8.505 7.5975 11.745 6 13.2375ZM6 3C5.40666 3 4.82664 3.17595 4.33329 3.50559C3.83994 3.83524 3.45542 4.30377 3.22836 4.85195C3.0013 5.40013 2.94189 6.00333 3.05764 6.58527C3.1734 7.16721 3.45912 7.70176 3.87868 8.12132C4.29824 8.54088 4.83279 8.8266 5.41473 8.94236C5.99667 9.05811 6.59987 8.9987 7.14805 8.77164C7.69623 8.54458 8.16477 8.16006 8.49441 7.66671C8.82405 7.17336 9 6.59334 9 6C9 5.20435 8.68393 4.44129 8.12132 3.87868C7.55871 3.31607 6.79565 3 6 3ZM6 7.5C5.70333 7.5 5.41332 7.41203 5.16665 7.2472C4.91997 7.08238 4.72771 6.84811 4.61418 6.57403C4.50065 6.29994 4.47094 5.99834 4.52882 5.70736C4.5867 5.41639 4.72956 5.14912 4.93934 4.93934C5.14912 4.72956 5.41639 4.5867 5.70737 4.52882C5.99834 4.47094 6.29994 4.50065 6.57403 4.61418C6.84811 4.72771 7.08238 4.91997 7.2472 5.16665C7.41203 5.41332 7.5 5.70333 7.5 6C7.5 6.39782 7.34197 6.77936 7.06066 7.06066C6.77936 7.34196 6.39783 7.5 6 7.5Z" fill="white" /></svg></i>
									<span><?php echo wp_kses_post( $destination ); ?></span>
								</span>
							<?php endif; ?>

							<?php if ( ! ! $trip_duration ) : ?>
								<span class="category-trip-dur">
									<i><svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.4375 1.625C6.99123 1.625 5.57743 2.05387 4.3749 2.85738C3.17236 3.66089 2.2351 4.80294 1.68163 6.13913C1.12817 7.47531 0.983357 8.94561 1.26551 10.3641C1.54767 11.7826 2.24411 13.0855 3.26678 14.1082C4.28946 15.1309 5.59242 15.8273 7.0109 16.1095C8.42939 16.3916 9.89969 16.2468 11.2359 15.6934C12.5721 15.1399 13.7141 14.2026 14.5176 13.0001C15.3211 11.7976 15.75 10.3838 15.75 8.9375C15.75 7.97721 15.5609 7.02632 15.1934 6.13913C14.8259 5.25193 14.2872 4.44581 13.6082 3.76678C12.9292 3.08775 12.1231 2.54912 11.2359 2.18163C10.3487 1.81414 9.39779 1.625 8.4375 1.625ZM8.4375 14.7875C7.28048 14.7875 6.14944 14.4444 5.18742 13.8016C4.22539 13.1588 3.47558 12.2451 3.03281 11.1762C2.59004 10.1072 2.47419 8.93101 2.69991 7.79622C2.92563 6.66143 3.48279 5.61906 4.30093 4.80092C5.11907 3.98279 6.16144 3.42563 7.29622 3.19991C8.43101 2.97418 9.60725 3.09003 10.6762 3.5328C11.7451 3.97558 12.6588 4.72539 13.3016 5.68741C13.9444 6.64944 14.2875 7.78048 14.2875 8.9375C14.2875 10.489 13.6712 11.977 12.5741 13.0741C11.477 14.1712 9.98902 14.7875 8.4375 14.7875ZM10.7044 9.39819L9.16875 8.51337V5.28125C9.16875 5.08731 9.09171 4.90131 8.95457 4.76418C8.81744 4.62704 8.63144 4.55 8.4375 4.55C8.24356 4.55 8.05757 4.62704 7.92043 4.76418C7.78329 4.90131 7.70625 5.08731 7.70625 5.28125V8.9375C7.70625 8.9375 7.70625 8.996 7.70625 9.02525C7.71058 9.07563 7.72293 9.125 7.74282 9.1715C7.75787 9.21488 7.77748 9.25655 7.80131 9.29581C7.82132 9.33737 7.84585 9.37661 7.87444 9.41281L7.99144 9.50787L8.05725 9.57369L9.9585 10.6706C10.0699 10.7337 10.196 10.7665 10.3241 10.7656C10.486 10.7668 10.6437 10.7141 10.7725 10.616C10.9013 10.5178 10.9938 10.3797 11.0357 10.2233C11.0775 10.0669 11.0662 9.90098 11.0036 9.75166C10.941 9.60233 10.8306 9.47801 10.6898 9.39819H10.7044Z" fill="black" fill-opacity="0.3"/></svg></i>
									<span><?php printf( _nx( '%1$d Day', '%1$d Days', $trip_duration, 'trip duration', 'wp-travel-engine' ), $trip_duration ); ?></span>

								<?php if ( $trip_duration_nights ) : ?>
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
						<p><?php echo wp_trim_words( get_the_excerpt(), 20, '...' ); ?></p>
					</div>
					<?php endif; ?>
					<a href="<?php the_permalink(); ?>" class="button category-trip-viewmre-btn"><?php echo apply_filters( 'wp_travel_engine_view_detail_txt', __( 'View Details', 'wp-travel-engine' ) ); ?></a>
				</div>
			</div>

			<?php
			$fsds = apply_filters( 'trip_card_fixed_departure_dates', get_the_ID() );

			if ( false !== $fsds ) {
				if ( $fsds == get_the_ID() || empty( $fsds ) ) {
					echo '<div class="category-trip-aval-time"><div class="category-trip-avl-tip-inner-wrap">';
					echo '<span class="category-available-trip-text"> ' . __( 'Available through out the year:', 'wp-travel-engine' ) . '</span>';
					echo '<ul class="category-available-months">';
					foreach ( range( 1, 12 ) as $month_number ) :
						echo '<li>' . date_i18n( 'M', strtotime( "2021-{$month_number}-01" ) ) . '</li>';
					endforeach;
					echo '</ul></div></div>';
				} elseif ( is_array( $fsds ) && count( $fsds ) > 0 ) {
					do_action( 'trip_card_fixed_departure_dates_content', $fsds, get_the_ID(), $dates_layout );
				}
			}
			?>
		</div>
	</div>
</div>
