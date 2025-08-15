<?php
use Razorpay\Api\Api;
use Razorpay\Api\Errors;
if(!class_exists('RazorProcess')){
    class RazorProcess{
        const SESSION_KEY                    = 'razorpay_st_order_id';
        const RAZORPAY_PAYMENT_ID            = 'razorpay_payment_id';
        const RAZORPAY_ORDER_ID              = 'razorpay_order_id';
        const RAZORPAY_SIGNATURE             = 'razorpay_signature';
        const RAZORPAY_ST_FORM_SUBMIT        = 'razorpay_st_form_submit';

        const INR                            = 'INR';
        const CAPTURE                        = 'capture';
        const AUTHORIZE                      = 'authorize';
        const ST_ORDER_ID                    = 'order_item_id';
        public $icon;
        public $form_fields = array();
        public $supports = array(
            'products',
            'refunds'
        );
        public function __construct(){
            $this->icon =  "https://cdn.razorpay.com/static/assets/logo/payment.svg";
            add_action('st_receipt_st_razor', array($this, 'receipt_page'),10,1);
            add_action('template_redirect', array($this, 'check_razorpay_response'));
            add_action('wp_enqueue_scripts', array($this, 'razor_enqueue'));
        }
        public function razor_enqueue(){
            if(get_page_template_slug(get_the_ID()) === 'template-payment-success.php'){
                wp_enqueue_style('razor-pay-css', ST_RAZOR_PLUGIN_URL . 'assets/css/style.css');
            }
        }
        function enqueueCheckoutScripts($data){
			wp_localize_script( 'jquery', 'st_plugin_params', [
	            'ajax_url' => admin_url( 'admin-ajax.php' ),
	            'home_url' => home_url('/'),
	            '_s'       => wp_create_nonce( '_wpnonce_security' ),
	        ] );
			if(st()->get_option('pm_gway_st_razor_enable'))
            {

                if($data === 'checkoutForm')
				{
					wp_register_script('razorpay_st_script', ST_RAZOR_PLUGIN_URL.'assets/js/script.js',
					null, null);
				}
				else
				{
					wp_register_script('razorpay_st_script', ST_RAZOR_PLUGIN_URL.'assets/js/script.js',
					array('razorpay_checkout'));

					wp_register_script('razorpay_checkout',
						'https://checkout.razorpay.com/v1/checkout.js',
						null, null);
				}

				wp_localize_script('razorpay_st_script',
					'razorpay_st_checkout_vars',
					$data
				);

				wp_enqueue_script('razorpay_st_script');
            }
		}
        public function receipt_page($orderId)
            {

            global $wp_session;
            $_SESSION[self::SESSION_KEY] = $orderId;
            $order = st_get_meta_orderby_id($orderId);

            try
            {
                $params = $this->getRazorpayPaymentParams($orderId);
            }
            catch (Exception $e)
            {
                return $e->getMessage();
            }

            $checkoutArgs = $this->getCheckoutArguments($order, $params);
            $html = '<p class="notice-success">'.__('Thank you for your order, please click the button below to pay with Razorpay.', 'traveler-razor-pay').'</p>';

            $html .= $this->generateOrderForm($checkoutArgs);

            echo $html;
        }
        private function getCheckoutArguments($order, $params)
        {
            $args = $this->getDefaultCheckoutArguments($order);
            $args = array_merge($args, $params);
            return $args;
        }
        private function getDefaultCheckoutArguments($order)
        {
            global $wp;
            if ( (! empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https') ||
                (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ||
                (! empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') ) {
                $server_request_scheme = 'https';
            } else {
                $server_request_scheme = 'http';
            }
            $callbackUrl =  $server_request_scheme.'://'.$_SERVER['HTTP_HOST'].'/'.$_SERVER['REQUEST_URI'];
            $orderId = $order['wc_order_id'];

            $productinfo = __('Order','razor-pay').$orderId;
            $key_id_razor = st()->get_option('key_id_razor', 'rzp_test_veUEpGt3opYjIx');
            return array(
                'key'          => $key_id_razor,
                'name'         => get_bloginfo('name'),
                'currency'     => self::INR,
                'description'  => '',
                'notes'        => array(
                    __('Order id','razor-pay') => $orderId,
                    __('Full Name','razor-pay') => get_post_meta($order['wc_order_id'], 'st_first_name', true) . " ". get_post_meta($order['wc_order_id'], 'st_last_name', true),
                    __('Email','razor-pay') => get_post_meta($order['wc_order_id'], 'st_email', true)." ",
                    __('Phone','razor-pay') => get_post_meta($order['wc_order_id'], 'st_phone', true)." ",
                    __('Address','razor-pay') => get_post_meta($order['wc_order_id'], 'st_address', true)." ",
                    __('City','razor-pay') => get_post_meta($order['wc_order_id'], 'st_city', true)." ",
                    __('Zip / Code','razor-pay') => get_post_meta($order['wc_order_id'], 'st_zip_code', true)." ",
                    __('Country','razor-pay') => get_post_meta($order['wc_order_id'], 'st_country', true)." ",
                    __('Note','razor-pay') => get_post_meta($order['wc_order_id'], 'st_note', true)." ",
                ),
                'callback_url' => $callbackUrl,
                'prefill'      => $this->getCustomerInfo($order),
                '_'            => array(
                    'integration'                   => 'st-checkout',
                    'integration_version'           => '1.0.0',
                    'integration_parent_version'    => '1.0.0',
                ),
                'customer' => $this->getCustomerInfo($order),
                "notify"=>  array(
                    "sms" => true,
                    "email" => true,
                ),
            );
        }
        public function getCustomerInfo($order)
        {
            $args = array(
                'name'    => get_post_meta($order['wc_order_id'], 'st_first_name', true) . ' '. get_post_meta($order['wc_order_id'], 'st_last_name', true),
                'email'   => get_post_meta($order['wc_order_id'], 'st_email', true),
                'contact' => get_post_meta($order['wc_order_id'], 'st_phone', true),
            );

            return $args;
        }
        protected function getRazorpayPaymentParams($orderId)
        {
            $razorpayOrderId = $this->createOrGetRazorpayOrderId($orderId);

            if ($razorpayOrderId === null)
            {
                throw new Exception('RAZORPAY ERROR: Razorpay API could not be reached');
            }
            else if ($razorpayOrderId instanceof Exception)
            {
                $message = $razorpayOrderId->getMessage();

                throw new Exception("RAZORPAY ERROR: Order creation failed with the message: '$message'.");
            }

            return [
                'order_id'  =>  $razorpayOrderId
            ];
        }
        protected function getOrderSessionKey($orderId)
        {
            return self::RAZORPAY_ORDER_ID . $orderId;
        }
        protected function createOrGetRazorpayOrderId($orderId)
        {
            global $wp_session;
            $sessionKey = $this->getOrderSessionKey($orderId);
            try
            {

                $razorpayOrderId = !empty($_SESSION[$sessionKey]) ? $_SESSION[$sessionKey] : null ;

                // If we don't have an Order
                // or the if the order is present in session but doesn't match what we have saved
                if (($razorpayOrderId === null) or
                    (($razorpayOrderId and ($this->verifyOrderAmount($razorpayOrderId, $orderId)) === false)))
                {
                    $create = true;
                }
                else
                {
                    return $razorpayOrderId;
                }
            }
            // Order doesn't exist or verification failed
            // So try creating one
            catch (Exception $e)
            {
                $create = true;
            }

            if ($create)
            {
                try
                {
                    return $this->createRazorpayOrderId($orderId, $sessionKey);
                }
                // For the bad request errors, it's safe to show the message to the customer.
                catch (Errors\BadRequestError $e)
                {
                    return $e;
                }
                // For any other exceptions, we make sure that the error message
                // does not propagate to the front-end.
                catch (Exception $e)
                {
                    return new Exception("Payment failed");
                }
            }
        }
        public function getOrderCurrency($order_id){
            $currency = get_post_meta( $order_id, 'currency', true );
            return $currency['name'];
        }
        private function getOrderCreationData($orderId)
        {

            $data_price = get_post_meta( $orderId , 'total_price' , true);
            $pay_amount = ! empty( $data_price ) ? $data_price : 0;

			$currency = get_post_meta( $orderId, 'currency', true );
			$rate     = $currency['rate'];
			$money    = TravelHelper::convert_money( $pay_amount, $rate );
			$money    = (int) ( number_format( $money * 100, 0, '.', '' ) );

            $payment_action_razor = st()->get_option('payment_action_razor','');
            $data = array(
                'receipt'         => $orderId,
                'amount'          => $money,
                'currency'        => $this->getOrderCurrency($orderId),
                'payment_capture' => ($payment_action_razor === self::AUTHORIZE) ? 0 : 1,
                'app_offer'       =>  0,
                'notes'           => array(
                    self::ST_ORDER_ID  => (string) $orderId,
                ),
            );

            return $data;
        }
        protected function createRazorpayOrderId($orderId, $sessionKey)
        {
            // Calls the helper function to create order data
            global $wp_session;

            $api = $this->getRazorpayApiInstance();

            $data = $this->getOrderCreationData($orderId);

            try
            {
                $razorpayOrder = $api->order->create($data);

            }
            catch (Exception $e)
            {

                return $e;
            }

            $razorpayOrderId = $razorpayOrder['id'];
            $_SESSION[$this->getOrderSessionKey($orderId)] = $razorpayOrderId;

            return $razorpayOrderId;
        }
        public function getRazorpayApiInstance()
        {
            $key_id_razor = st()->get_option('key_id_razor', 'rzp_test_veUEpGt3opYjIx');
            $key_secret_razor = st()->get_option('key_secret_razor', '3GMZdpweAwcXslCQFgSbbr2J');
            return new Api($key_id_razor, $key_secret_razor);
        }
        protected function verifyOrderAmount($razorpayOrderId, $orderId)
        {
            $api = $this->getRazorpayApiInstance();

            try
            {
                $razorpayOrder = $api->order->fetch($razorpayOrderId);
            }
            catch (Exception $e)
            {
                $message = $e->getMessage();
                return "RAZORPAY ERROR: Order fetch failed with the message '$message'";
            }

            $orderCreationData = $this->getOrderCreationData($orderId);

            $razorpayOrderArgs = array(
                'id'        => $razorpayOrderId,
                'amount'    => $orderCreationData['amount'],
                'currency'  => $orderCreationData['currency'],
                'receipt'   => (string) $orderId,
            );

            $orderKeys = array_keys($razorpayOrderArgs);

            foreach ($orderKeys as $key)
            {
                if ($razorpayOrderArgs[$key] !== $razorpayOrder[$key])
                {
                    return false;
                }
            }

            return true;
        }
        function generateOrderForm($data)
        {
            global $wp_session , $wp;

            if ( (! empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https') ||
                (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ||
                (! empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') ) {
                $server_request_scheme = 'https';
            } else {
                $server_request_scheme = 'http';
            }
            $callbackUrl =  $server_request_scheme.'://'.$_SERVER['HTTP_HOST'].''.$_SERVER['REQUEST_URI'];
            $key_id_razor = st()->get_option('key_id_razor', 'rzp_test_veUEpGt3opYjIx');
            $redirectUrl = $callbackUrl;
            $data['cancel_url'] = get_permalink( st()->get_option( 'page_checkout' ) );
            $api = new Api($key_id_razor,"");

            $merchantPreferences = $api->request->request("GET", "preferences");

            if(isset($merchantPreferences['options']['redirect']) && $merchantPreferences['options']['redirect'] === true)
            {
                $this->enqueueCheckoutScripts('checkoutForm');

                $data['preference']['image'] = $merchantPreferences['options']['image'];

                return $this->hostCheckoutScripts($data);

            } else {
                $this->enqueueCheckoutScripts($data);

                return '<form name="razorpayform" action="'.$redirectUrl.'" method="POST">
                    <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
                    <input type="hidden" name="razorpay_signature"  id="razorpay_signature" >
                    <input type="hidden" name="razorpay_st_form_submit" value="1">
                </form>
                <p id="msg-razorpay-success" class="pay-info pay-message" style="display:none">
                '.__('Please wait while we are processing your payment.','traveler-razor-pay').'
                </p>
                <p class="pay-button">
                    <button id="btn-razorpay" class="btn btn-primary">'.__('Pay Now','traveler-razor-pay').'</button>
                    <button id="btn-razorpay-cancel" style="display: none" onclick="document.razorpayform.submit()" class="btn btn-primary">'.__('Cancel','traveler-razor-pay').'</button>
                </p>';
            }
        }
        private function hostCheckoutScripts($data){
            $url = Api::getFullUrl("checkout/embedded");
            $formFields = "";
            foreach ($data as $fieldKey => $val) {
                if(in_array($fieldKey, array('notes', 'prefill', '_')))
                {
                    foreach ($data[$fieldKey] as $field => $fieldVal) {
                        $formFields .= "<input type='hidden' name='$fieldKey" ."[$field]"."' value='$fieldVal'> \n";
                    }
                }
            }

            return '<form method="POST" action="'.$url.'" id="checkoutForm">
                    <input type="hidden" name="key_id" value="'.$data['key'].'">
                    <input type="hidden" name="order_id" value="'.$data['order_id'].'">
                    <input type="hidden" name="name" value="'.$data['name'].'">
                    <input type="hidden" name="description" value="'.$data['description'].'">
                    <input type="hidden" name="image" value="'.$data['preference']['image'].'">
                    <input type="hidden" name="callback_url" value="'.$data['callback_url'].'">
                    <input type="hidden" name="cancel_url" value="'.$data['cancel_url'].'">
                    '. $formFields .'
                </form>';

        }
        protected function verifySignature($razorpay_order_id,$razorpayPaymentId,$razorpay_signature)
        {
            global $wp_session;

            $api = $this->getRazorpayApiInstance();

            // Please note that the razorpay order ID must
            // come from a trusted source (session here, but
            // could be database or something else)
            $attributes = array(
                'razorpay_order_id' => $razorpay_order_id,
                'razorpay_payment_id' => $razorpayPaymentId,
                'razorpay_signature' => $razorpay_signature
            );


            $api->utility->verifyPaymentSignature($attributes);
        }
        function _st_booking_change_status($status,$order_id,$booking_type)
        {
            // Check if table order item meta was created
            global $wpdb;
            $table_name=$wpdb->prefix . 'st_order_item_meta';
            $SQL="UPDATE $table_name SET `status`='$status' where order_item_id=$order_id and `type`='$booking_type' ";
            $wpdb->query($SQL);
        }

		function check_razorpay_response()
        {    if(get_page_template_slug(get_the_ID()) === 'template-payment-success.php'){
                $order_token_code=STInput::get('order_token_code');
                if($order_token_code)
                {
                    $order_code_id=STOrder::get_order_id_by_token($order_token_code)->post_id;

                }
                $gateway=get_post_meta($order_code_id,'payment_method',true);
                if($gateway==='st_razor'){
					// dd($_POST[self::RAZORPAY_PAYMENT_ID]);
                    global $wp_session;
                    $orderId = !empty($_SESSION[self::SESSION_KEY]) ? $_SESSION[self::SESSION_KEY] : '';
                    $orderId = STInput::get('order_code');
					$order_token_code=STInput::get('order_token_code');

					if($order_token_code)
					{
						$orderId=STOrder::get_order_id_by_token($order_token_code)->post_id;

					}
                    if ( get_post_meta( $orderId, 'status', true ) !== 'complete' ) {
                        $order = st_get_meta_orderby_id($orderId);
                        $razorpayPaymentId = !empty($_POST[self::RAZORPAY_PAYMENT_ID]) ? $_POST[self::RAZORPAY_PAYMENT_ID] : null;
                        $razorpay_st_form_submit = !empty($_POST[self::RAZORPAY_ST_FORM_SUBMIT]) ? $_POST[self::RAZORPAY_ST_FORM_SUBMIT] : null;
                        $razorpay_signature = !empty($_POST[self::RAZORPAY_SIGNATURE]) ? $_POST[self::RAZORPAY_SIGNATURE] : null;
                        $sessionKey = $this->getOrderSessionKey($orderId);
                        session_start();
                        $razorpay_order_id = isset($_SESSION['razorpay_order_id'.$orderId]) ? $_SESSION['razorpay_order_id'.$orderId] : '';

                        if (!empty($orderId)  && !empty($_POST[self::RAZORPAY_PAYMENT_ID]))
                        {

                            $error = "";
                            $success = false;

                            try
                            {
                                $this->verifySignature($razorpay_order_id,$razorpayPaymentId,$razorpay_signature);
                                $success = true;
                                $razorpayPaymentId = sanitize_text_field($_POST[self::RAZORPAY_PAYMENT_ID]);

                            }
                            catch (Errors\SignatureVerificationError $e)
                            {

                                $success = 'incomplete';
                                $error = 'ERROR: Payment to Razorpay Failed. ' . $e->getMessage();
                            }
                            $this->updateOrder($order, $success, $error, $razorpayPaymentId,$orderId);
                        }
                        else
                        {
                            if($razorpay_st_form_submit == 1)
                            {
                                $success = false;
                                $error = 'Customer cancelled the payment';
                            }
                            else
                            {
                                $success = false;
                                $error = "Payment Failed.";
                            }
                            $this->updateOrder($order, $success, $error, $razorpayPaymentId,$orderId);
                        }
                    }
                }

            }
        }
        public function updateOrder($order, $success, $errorMessage, $razorpayPaymentId, $orderId)
        {
            $idOrder = $order['wc_order_id'];
            if ( get_post_meta( $idOrder, 'status', true ) !== 'complete' ) {

                if ($success === true)
                {
                    $this->_st_booking_change_status('complete',$idOrder, 'normal_booking');
                    update_post_meta($idOrder, 'status', 'complete');
                }
                elseif($success === 'incomplete')
                {
                    $this->_st_booking_change_status('incomplete',$idOrder, 'normal_booking');
                    update_post_meta($idOrder, 'status', 'incomplete');
                } else {

                }
            }


        }

    }
    new RazorProcess;
}