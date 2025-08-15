<?php get_header();
$register_page = get_the_permalink(st()->get_option("page_user_register"));
$reset_page = get_the_permalink(st()->get_option("page_reset_password"));
?>
<div id="st-content-wrapper" class="search-result-page st-style-elementor">
	<?php echo st()->load_template('layouts/modern/hotel/elements/banner'); ?>
</div>
<div class="container">
    <div id="st-login-form-page" class="st-login-class-wrapper">
        <div class="modal-dialog" role="document">
            <div class="modal-content st-border-radius relative">
                <?php echo st()->load_template('layouts/modern/common/loader'); ?>
                <div class="modal-header d-sm-flex d-md-flex justify-content-between align-items-center">
                    <ul class="account-tabs">
                        <li class="active"
                            data-bs-target="login-component"><?php echo esc_html__('Sign in', 'traveler'); ?></li>
                        <li data-bs-target="register-component"><a href="<?php echo esc_url($register_page) ?>"><?php echo esc_html__('Sign up', 'traveler'); ?></a></li>
                    </ul>
                </div>
                <div class="modal-body relative">
                    <div class="map-loading" style="display:none">
                    </div>
                    <form action="#" class="form" method="post">
                        <input type="hidden" name="st_theme_style" value="modern"/>
                        <input type="hidden" name="action" value="st_login_popup">
                        <input type="hidden" name="post_id" value="<?php echo get_the_ID();?>">
                        <div class="form-group">
                            <input type="text" class="form-control" name="username" autocomplete="off"
                                placeholder="<?php echo esc_html__('Email or Username', 'traveler') ?>">
                                <?php echo TravelHelper::getNewIcon('ico_email_login_form', '', '18px', ''); ?>
                        </div>
                        <div class="form-group field-password ic-view">
                            <input type="password" class="form-control" name="password" autocomplete="off"
                                placeholder="<?php echo esc_html__('Password', 'traveler') ?>">
                                <?php echo TravelHelper::getNewIcon('ico_pass_login_form', '', '16px', ''); ?>
                        </div>
                        <div class="form-group">
                            <input type="submit" name="submit" class="form-submit"
                                value="<?php echo esc_html__('Log In', 'traveler') ?>">
                        </div>
                        <div class="message-wrapper mt20"></div>
                        <div class="mt20 st-flex space-between st-icheck">
                            <div class="st-icheck-item">
                                <label for="remember-me" class="c-grey">
                                    <input type="checkbox" name="remember" id="remember-me"
                                        value="1"> <?php echo esc_html__('Remember me', 'traveler') ?>
                                    <span class="checkmark fcheckbox"></span>
                                </label>
                            </div>
                            <a href="#" class="st-link open-loss-password"
                            data-toggle="modal"><?php echo esc_html__('Forgot Password?', 'traveler') ?></a>
                        </div>

                        <?php
							if (
								is_plugin_active('traveler-social-login/traveler-social-login.php') &&
								(
									st_social_channel_status('facebook') ||
									st_social_channel_status('google') ||
									st_social_channel_status('twitter')
								)
							):
						?>
                        <div class="advanced">
                            <p class="text-center f14 c-grey"><?php echo esc_html__('or continue with', 'traveler') ?></p>
                            <div class="row">
                                <div class="col-xs-12 col-sm-12">
                                    <?php if (st_social_channel_status('facebook')): ?>
                                        <a onclick="return false" href="#"
                                        class="btn_login_fb_link st_login_social_link" data-channel="facebook">
                                        <div class="st-login-facebook">
                                                <div
                                                    onlogin="startLoginWithFacebook()"
                                                    class="fb-login-button"
                                                    data-width="100%"
                                                    data-height="48px"
                                                    data-max-rows="1"
                                                    data-size="large"
                                                    login_text="<?php echo esc_html__('Continue with Facebook', 'traveler') ?>"
                                                    data-scope="public_profile, email">
                                                </div>

                                            </div>
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <div class="col-xs-12 col-sm-12">
                                    <?php if (st_social_channel_status('google')):
                                        echo do_shortcode('[st-google-login type="login"]');
                                        ?>
                                    <?php endif; ?>
                                </div>
                                <div class="col-xs-12 col-sm-12">
                                    <?php if (st_social_channel_status('twitter')): ?>
                                        <a href="<?php echo site_url() ?>/social-login/twitter"
                                        onclick="return false"
                                        class="btn_login_tw_link st_login_social_link" data-channel="twitter">
											<span id="button-twitter">
												<span class="icon">
													<svg style="color: white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
														<path fill="white" d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/>
													</svg>
												</span>
												<span class="text"><?php echo esc_html__('Log in with X.com', 'traveler') ?></span>
											</span>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="mt20 c-grey font-medium f14 text-center">
                            <?php echo esc_html__('Do not have an account? ', 'traveler') ?>
                            <a href="#"
                            class="st-link open-signup"
                            data-toggle="modal"><?php echo esc_html__('Sign Up', 'traveler') ?></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>
