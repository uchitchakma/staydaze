<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 13-11-2018
	 * Time: 3:00 PM
	 * Since: 1.0.0
	 * Updated: 1.0.0
	 */

if ( ! isset( $position ) ) {
	$position = '';
}
?>
<div class="form-button st-cartransfer-advance d-flex align-items-center justify-content-between">
	<?php if ( ! is_page_template( 'template-transfer-search.php' ) ) : ?>
		<div class="advance dropdown">
			<div class="form-group form-extra-field field-advance">
				<div class="dropdown-toggle"  id="dropdown-advance" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
					<?php if ( $position != 'sidebar' ) { ?>
					<label class="d-none d-sm-block d-md-block"><?php echo __( 'Advance', 'traveler' ); ?></label>
					<div class="render">
						<span class="d-none d-sm-block d-md-block"><?php echo __( 'More', 'traveler' ); ?> <i class="fa fa-caret-down"></i></span>
						<span class="d-sm-none d-md-none"><?php echo __( 'More options', 'traveler' ); ?> <i
									class="fa fa-caret-down"></i></span>
					</div>
					<?php } else { ?>
						<div class="render">
							<span><?php echo __( 'More options', 'traveler' ); ?> <i class="fa fa-caret-down"></i></span>
						</div>
					<?php } ?>
				</div>
				<div class="dropdown-menu" aria-labelledby="dropdown-advance">
					<div class="row">
						<?php
						$search_tax_advance = st()->get_option( 'attribute_search_form_car', 'st_category_cars' );
						$get_label_tax      = get_taxonomy( $search_tax_advance );
						$tax                = STInput::get( 'taxonomy' );
						$in_facilities      = [];
						$temp_facilities    = '';
						if ( ! empty( $tax ) ) {
							if ( isset( $tax[ $search_tax_advance ] ) ) {
								if ( ! empty( $tax[ $search_tax_advance ] ) ) {
									$temp_facilities = $tax[ $search_tax_advance ];
									$in_facilities   = explode( ',', $tax[ $search_tax_advance ] );
								}
							}
						}
						$facilities = get_terms(
							[
								'taxonomy'   => $search_tax_advance,
								'hide_empty' => false,
							]
						);
						if ( $facilities && ! is_wp_error( $facilities ) ) {
							?>
							<div class="col-12">
								<div class="advance-item facilities st-icheck">

									<div class="item-title">
											<?php
											if ( ! empty( $get_label_tax ) ) {
												echo '<h4>' . esc_html( $get_label_tax->label ) . '</h4>';
											}
											?>
									</div>

									<div class="item-content st-scrollbar" tabindex="1">
										<div class="row">
											<?php
											foreach ( $facilities as $term ) {
												?>
												<div class="<?php echo ( $position == 'sidebar' ) ? 'col-lg-12' : 'col-lg-4 col-sm-6'; ?>">
													<div class="st-icheck-item">
														<label><?php echo esc_html( $term->name ); ?><input
																	type="checkbox"
																	name="" value="<?php echo esc_attr( $term->term_id ); ?>" <?php echo in_array( $term->term_id, $in_facilities ) ? 'checked' : ''; ?>><span
																	class="checkmark fcheckbox"></span>
														</label></div>
												</div>
												<?php
											}
											?>
										</div>
									</div>
									<input type="hidden" class="data_taxonomy" name="taxonomy[<?php echo esc_attr( $search_tax_advance ) ?>]" value="<?php echo esc_attr( $temp_facilities ); ?>">

								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>
	<button class="btn btn-primary btn-search" type="submit"><?php echo __( 'Search', 'traveler' ); ?></button>
</div>
