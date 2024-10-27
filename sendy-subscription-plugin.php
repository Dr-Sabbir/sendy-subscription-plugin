<?php
/**
 * Plugin Name: Sendy Subscription Plugin with Image CAPTCHA
 * Description: A plugin to integrate Sendy with WordPress using custom image-based CAPTCHA.
 * Version: 1.3
 * Author: Dr. Sabbir H
 * Author URI: https://sabbirh.com/
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

require_once plugin_dir_path(__FILE__) . 'sendy-form-handler.php'; // Include form handler

// Register the settings page
function sendy_subscription_menu() {
    add_options_page(
        'Sendy Subscription Settings',
        'Sendy Subscription',
        'manage_options',
        'sendy-subscription-settings',
        'sendy_subscription_settings_page'
    );
}
add_action('admin_menu', 'sendy_subscription_menu');

// Render the settings page
function sendy_subscription_settings_page() {
    if (isset($_POST['sendy_settings_submit'])) {
        $sendy_url = rtrim(sanitize_text_field($_POST['sendy_url']), '/'); // Remove trailing slash
        $api_key = sanitize_text_field($_POST['sendy_api_key']);
        $sendy_woo_list = sanitize_text_field($_POST['sendy_woo_list']);

        update_option('sendy_url', $sendy_url);
        update_option('sendy_api_key', $api_key);
        update_option('sendy_woo_list', $sendy_woo_list);

        echo '<div class="updated"><p>Settings saved successfully!</p></div>';
    }

    $sendy_url = get_option('sendy_url', '');
    $sendy_api_key = get_option('sendy_api_key', '');

    $sendy_woo_list = get_option('sendy_woo_list', '');
    ?>
    <div class="wrap">
        <h1>Sendy Subscription Settings</h1>
        <form method="post" action="">
            <h2>General Settings</h2>
            <table class="form-table">
                <tr>
                    <th><label for="sendy_url">Sendy Installation URL</label></th>
                    <td><input type="text" name="sendy_url" value="<?php echo esc_attr($sendy_url); ?>" class="regular-text" required /></td>
                </tr>
                <tr>
                    <th><label for="sendy_api_key">API Key</label></th>
                    <td><input type="text" name="sendy_api_key" value="<?php echo esc_attr($sendy_api_key); ?>" class="regular-text" required /></td>
                </tr>
            </table>
            
            <h2>WooCommerce Settings</h2>
            <table class="form-table">
                <tr>
                    <th><label for="sendy_woo_list">WooCommerce List ID</label></th>
                    <td><input type="text" name="sendy_woo_list" value="<?php echo esc_attr($sendy_woo_list); ?>" class="regular-text" required /></td>
                </tr>
            </table>

            <p><input type="submit" name="sendy_settings_submit" class="button-primary" value="Save Changes" /></p>
        </form>
    </div>
    <?php
}

// Shortcode for subscription form with CAPTCHA
function sendy_subscription_form_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'name_placeholder' => 'Your Name',
            'email_placeholder' => 'Your Email',
            'list' => '',
            'submit_text' => 'Subscribe',
            'redirect_url' => ''
        ),
        $atts,
        'sendy_subscription_form'
    );

    $nonce = wp_create_nonce('sendy_subscription_form_nonce');
    $unique_id = uniqid('sf_'); // Generate a unique ID for the form

    ob_start(); ?>
    <form id="<?php echo esc_attr($unique_id); ?>">
        <input type="text" id="<?php echo esc_attr($unique_id . '_name'); ?>" name="name" placeholder="<?php echo esc_attr($atts['name_placeholder']); ?>" required />
        <input type="email" id="<?php echo esc_attr($unique_id . '_email'); ?>" name="email" placeholder="<?php echo esc_attr($atts['email_placeholder']); ?>" required />
        <div>
            <img src="<?php echo plugin_dir_url(__FILE__) . 'captcha/captcha.php'; ?>" alt="CAPTCHA" />
            <input type="text" id="<?php echo esc_attr($unique_id . '_captcha_code'); ?>" name="captcha_code" placeholder="Enter CAPTCHA" required />
        </div>
        <input type="hidden" id="<?php echo esc_attr($unique_id . '_list'); ?>" name="list" value="<?php echo esc_attr($atts['list']); ?>">
        <input type="hidden" id="<?php echo esc_attr($unique_id . '_sendy_nonce'); ?>"  name="sendy_nonce" value="<?php echo $nonce; ?>"> <!-- Nonce -->
        <input type="submit" id="<?php echo esc_attr($unique_id . '_submit'); ?>" value="<?php echo esc_attr($atts['submit_text']); ?>" />
        <div id="<?php echo esc_attr($unique_id . '_status'); ?>"></div>
    </form>

    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            var formId = '<?php echo esc_js($unique_id); ?>';
            
            $('#' + formId).on('submit', function (e) {
                e.preventDefault(); // Prevent default form submission behavior
                // Disable the submit button
                $('#' + formId + '_submit').attr('disabled', true);

                var name = $('#' + formId + '_name').val();
                var email = $('#' + formId + '_email').val();
                var captcha = $('#' + formId + '_captcha_code').val();
                var list = $('#' + formId + '_list').val();
                var nonce = $('#' + formId + '_sendy_nonce').val();

                $.ajax({
                    url: '<?php echo admin_url("admin-ajax.php"); ?>',
                    type: 'POST',
                    dataType: 'json', // Expecting JSON response
                    data: {
                        action: 'sendy_subscribe',
                        name: name,
                        email: email,
                        captcha: captcha,
                        list: list,
                        sendy_nonce: nonce
                    },
                    success: function (response) {
                        if (response.success === true) {
                            if(response.data.message == "Already subscribed."){
                                $('#' + formId + '_status').html(response.data.message).css('color', 'green');
                            }else{
                                $('#' + formId + '_status').html(response.message).css('color', 'green').show();
                                if ('<?php echo esc_js($atts['redirect_url']); ?>' !== '') {
                                    window.location.href = '<?php echo esc_js($atts['redirect_url']); ?>';
                                } else {
                                    $('#' + formId)[0].reset(); // Reset form if no redirect
                                }
                            }
                            
                        } else {
                            $('#' + formId + '_status').html(response.data.message).css('color', 'red').show();
                        }
                        // Enable the submit button after processing
                        $('#' + formId + '_submit').attr('disabled', false);
                    },
                    error: function (xhr, status, error) {
                        console.error('Error:', error);
                        console.error('Status:', status);
                        console.error('Response:', xhr.responseText);

                        $('#' + formId + '_status').html('An error occurred. Please try again.').css('color', 'red').show();

                        // Enable the submit button if an error occurs
                        $('#' + formId + '_submit').attr('disabled', false);
                    }
                });
            });
        });
    </script>

    <?php
    return ob_get_clean();
}
add_shortcode('sendy_subscription_form', 'sendy_subscription_form_shortcode');


function sendy_is_elementor_active() {
    return did_action('elementor/loaded');
}

// Register Elementor Widget
function register_sendy_subscription_widget() {
    if (!sendy_is_elementor_active()) return; // Bail if Elementor is not active

    require_once plugin_dir_path(__FILE__) . 'widgets/sendy-widget.php'; // Include widget class

    \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \Sendy_Subscription_Widget());
}
add_action('elementor/widgets/widgets_registered', 'register_sendy_subscription_widget');




// CF7
function sendy_ssp_shortcode($atts) {
    $atts = shortcode_atts(
        [
            'list' => '',
            'name' => 'your-name',
            'email' => 'your-email',
        ],
        $atts,
        'sendy_cf7'
    );

    // Generate hidden fields for Sendy list, name, and email
    return sprintf(
        '<input type="hidden" name="sendy_list" value="%s">
         <input type="hidden" name="sendy_name_field" value="%s">
         <input type="hidden" name="sendy_email_field" value="%s">',
        esc_attr($atts['list']),
        esc_attr($atts['name']),
        esc_attr($atts['email'])
    );
}
add_shortcode('sendy_cf7', 'sendy_ssp_shortcode');



function sendy_cf7_submission_handler($cf7) {
    $submission = WPCF7_Submission::get_instance();
    if (!$submission) return;

    $data = $submission->get_posted_data();

    // Check if Sendy list is specified
    if (empty($data['sendy_list'])) return;

    // Get the Sendy list, name, and email field values
    $list_id = sanitize_text_field($data['sendy_list']);
    $name_field = sanitize_text_field($data['sendy_name_field']);
    $email_field = sanitize_text_field($data['sendy_email_field']);

    // Ensure the name and email fields exist in the submission
    if (isset($data[$name_field]) && isset($data[$email_field])) {
        $name = sanitize_text_field($data[$name_field]);
        $email = sanitize_email($data[$email_field]);

        // Send data to Sendy
        $sendy_url = rtrim(get_option('sendy_url'), '/') . '/subscribe';
        $api_key = get_option('sendy_api_key');

        $response = wp_remote_post($sendy_url, [
            'body' => [
                'name' => $name,
                'email' => $email,
                'list' => $list_id,
                'api_key' => $api_key,
                'boolean' => 'true',
            ],
        ]);

        if (is_wp_error($response)) {
            error_log('Sendy API Error: ' . $response->get_error_message());
        } else {
            $body = wp_remote_retrieve_body($response);
            if (trim($body) !== '1') {
                error_log('Sendy Subscription Failed: ' . $body);
            }
        }
    }
}
add_action('wpcf7_mail_sent', 'sendy_cf7_submission_handler');




// Hook to WooCommerce order completion event
add_action('woocommerce_order_status_completed', 'sendy_subscribe_on_order_complete', 10, 1);

function sendy_subscribe_on_order_complete($order_id) {
    // Get the order object
    $order = wc_get_order($order_id);

    // Get customer details from the order
    $name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
    $email = $order->get_billing_email();
    $country = $order->get_billing_country();
    $ip_address = $order->get_customer_ip_address();
    $referrer = wc_get_order_item_meta($order_id, '_customer_user_agent', true); // Optional referrer tracking

    // Sendy configuration
    $sendy_url = rtrim(get_option('sendy_url', ''), '/'); // Ensure no trailing slash
    $api_key = get_option('sendy_api_key', '');
    $list_id = get_option('sendy_woo_list', '');

    // Prepare POST data for single opt-in
    $postdata = http_build_query([
        'name'         => $name,
        'email'        => $email,
        'list'         => $list_id,
        'api_key'      => $api_key,
        'country'      => $country,   // Optional: Country code
        'ipaddress'    => $ip_address, // Optional: Customer's IP address
        'referrer'     => $referrer,   // Optional: URL where the user signed up
        'silent'       => 'true',      // Optional: Bypass double opt-in for single opt-in
        'hp'           => '',          // Honeypot to block spambots
        'boolean'      => 'true',      // Boolean response for easier handling
    ]);

    $opts = [
        'http' => [
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => $postdata,
        ],
    ];

    $context = stream_context_create($opts);

    // Send request to Sendy and capture the response
    $result = @file_get_contents($sendy_url . '/subscribe', false, $context);

    // Handle the response
    if ($result === '1') {
        error_log("Sendy: Successfully subscribed $email.");
    } else {
        error_log("Sendy: Subscription failed for $email. Response: $result");
    }
}
