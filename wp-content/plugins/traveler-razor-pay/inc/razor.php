<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 12/15/2015
 * Time: 3:19 PM
 */
use Razorpay\Api\Api;
use Razorpay\Api\Errors;
if (!class_exists('ST_Razor_Payment_Gateway') && class_exists('STAbstactPaymentGateway')) {
	class ST_Razor_Payment_Gateway extends STAbstactPaymentGateway
	{
	    public static $_ints;
		private $default_status = TRUE;

		private $_gatewayObject = null;

		private $_gateway_id = 'st_razor';
		public $has_fields = false;
		function __construct()
		{
			add_filter('st_payment_gateway_st_razor', array($this, 'get_name'));
			add_action('admin_notices', array($this, '_add_notices'));
			add_action('admin_init', array($this, '_dismis_notice'));

		}



		public function do_checkout($order_id){
			return ([
                "success" => true,
                "status" => 'incomplete'
			  ]);
		}

		function _dismis_notice()
		{
			if (STInput::get('st_dismiss_razor_notice')) {
				update_option('st_dismiss_razor_notice', 1);
			}

		}

		function _add_notices()
		{
			if (get_option('st_dismiss_razor_notice')) return;

			if (class_exists('ST_RazorPay')) {
                $version = ST_RAZOR_VERSION;
                if (version_compare('1.0.0', $version, '>')) {
                    $url = admin_url('plugin-install.php?tab=plugin-information&plugin=traveler-code&TB_iframe=true&width=753&height=350');
                    ?>
                    <div class="error settings-error notice is-dismissible">
                        <p class=""><strong><?php _e('Razor Pay Notice:', 'traveler-razor-pay') ?></strong></p>

                        <p>
                            <?php printf(__('<strong>ST Razor Pay</strong> require %s version %s or above. Your current is %s', 'traveler-razor-pay'), '<strong><em>' . __('Traveler Code', 'traveler-razor-pay') . '</em></strong>', '<strong>1.3.2</strong>', '<strong>' . $version . '</strong>'); ?>
                        </p>

                        <p>
                            <a href=""
                                target="_blank"><?php _e('Learn how to update it', 'traveler-razor-pay') ?></a>
                            |
                            <a href="<?php echo admin_url('index.php?st_dismiss_razor_notice=1') ?>"
                                class="dismiss-notice"
                                target="_parent"><?php _e('Dismiss this notice', 'traveler-razor-pay') ?></a>
                        </p>
                        <button type="button" class="notice-dismiss"><span
                                class="screen-reader-text"><?php _e('Dismiss this notice', 'traveler-razor-pay') ?>.</span>
                        </button>
                    </div>
                    <?php
                }
			}
		}

		function get_option_fields()
		{
			return array(
				array(
					'id'        => 'key_id_razor',
					'label'     => __('Key ID', 'traveler-razor-pay'),
					'type'      => 'text',
					'section'   => 'option_pmgateway',
					'desc'      => __('The key Id and key secret can be generated from "API Keys" section of Razorpay Dashboard. Use test or live for test or live mode.', 'traveler-razor-pay'),
					'condition' => 'pm_gway_st_razor_enable:is(on)'
				),
				array(
					'id'        => 'key_secret_razor',
					'label'     => __('Key Secret', 'traveler-razor-pay'),
					'type'      => 'text',
					'section'   => 'option_pmgateway',
					'desc'      => __('The key Id and key secret can be generated from "API Keys" section of Razorpay Dashboard. Use test or live for test or live mode.', 'traveler-razor-pay'),
					'condition' => 'pm_gway_st_razor_enable:is(on)'
				),
				array(
					'id'        => 'payment_action_razor',
					'label'     => __('Payment Action', 'traveler-razor-pay'),
					'type' => 'select',
					'choices' => [
						[
							'value' => 'authorize',
							'label' => esc_html__('Authorize', 'traveler-razor-pay')
						],
						[
							'value' => 'capture',
							'label' => esc_html__('Authorize and Capture', 'traveler-razor-pay')
						],
					],
					'std' => 'authorize',
					'section'   => 'option_pmgateway',
					'desc'      => __('Payment action on order compelete', 'traveler-razor-pay'),
					'condition' => 'pm_gway_st_razor_enable:is(on)'
				),
			);
		}

		function stop_change_order_status()
		{
			return true;
		}

		function _pre_checkout_validate()
		{
			return true;
		}

		function package_do_checkout($order_id){
			if (!class_exists('STAdminPackages')) {
                return ['status' => TravelHelper::st_encrypt($order_id . 'st0'), 'message' => __('This function is off', 'traveler-razor-pay')];
            }

			return [
				'status' => TravelHelper::st_encrypt($order_id . 'st1'),
				'redirect_url' => STAdminPackages::get_inst()->get_return_url($order_id),
			];

        }



		function get_authorize_url($order_id)
		{
			return ([
                "success" => true,
                "status" => 'incomplete'
			  ]);
		}




		function  check_complete_purchase($order_id)
		{
			$r = [
				'status' => false
			];
			return $r;
		}

		function package_completed_checkout($order_id){
            if (!class_exists('STAdminPackages')) {
                return ['status' => false];
            }

            $status = STInput::get('status');
            if ( TravelHelper::st_compare_encrypt( (int) $order_id . 'st1', $status ) ) {
                return [
					'status' => false,
					'redirect_url' => STAdminPackages::get_inst()->get_return_url($order_id),
				];
            }
        }

		function html()
		{
		    echo __('Payment after create order', 'traveler-razor-pay');

		}

		function get_name()
		{
			return __('RazorPay', 'traveler-razor-pay');
		}

		function get_default_status()
		{
			return $this->default_status;
		}

		function is_available($item_id = FALSE)
		{
			if (st()->get_option('pm_gway_st_razor_enable') == 'off') {
				return FALSE;
			}

			if ($item_id) {
				$meta = get_post_meta($item_id, 'is_meta_payment_gateway_st_razor', TRUE);
				if ($meta == 'off') {
					return FALSE;
				}
			}

			return TRUE;
		}

		function getGatewayId()
		{
			return $this->_gateway_id;
		}

		function is_check_complete_required()
		{
			return true;
		}

		function get_logo()
		{
			return ST_RAZOR_PLUGIN_URL. 'assets/img/logo.png';
		}

        static function instance() {
            if ( ! self::$_ints ) {
                self::$_ints = new self();
            }

            return self::$_ints;
        }

        static function add_payment( $payment ) {
            $payment['st_razor'] = self::instance();

            return $payment;
        }

	}

	add_filter( 'st_payment_gateways', array( 'ST_Razor_Payment_Gateway', 'add_payment' ) );
}
