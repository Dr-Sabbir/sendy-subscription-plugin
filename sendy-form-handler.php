<?php
session_start(); // Start session

if (!defined('ABSPATH')) exit; // Exit if accessed directly

// AJAX handler for subscription with CAPTCHA validation
function sendy_subscribe_handler() {
    check_ajax_referer('sendy_subscription_form_nonce', 'sendy_nonce'); // Verify the nonce

    $name = sanitize_text_field($_POST['name'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    $list = sanitize_text_field($_POST['list'] ?? '');
    $captcha = sanitize_text_field($_POST['captcha'] ?? ''); // User input for CAPTCHA

    // Compare the hashed user input with the stored CAPTCHA hash
    if (hash('sha256', $captcha) !== $_SESSION['captcha_code']) {
        wp_send_json_error(['message' => 'Invalid CAPTCHA. Please try again.']);
        return;
    }
    $sendy_url = get_option('sendy_url', '');
    $api_key = get_option('sendy_api_key', '');

    // Prepare POST data
    $postdata = http_build_query([
        'name'    => $name,
        'email'   => $email,
        'list'    => $list,
        'api_key' => $api_key,
        'boolean' => 'true',
    ]);

    $opts = [
        'http' => [
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => $postdata,
        ],
    ];

    $context = stream_context_create($opts);
    $result = file_get_contents($sendy_url . '/subscribe', false, $context);

    wp_send_json_success(['message' => $result]);

    if ($result === '1') {
        $_SESSION['captcha_code'] = hash('sha256', $secure_code);
        wp_send_json_success(['message' => 'You are successfully subscribed!']);
    } else {
        wp_send_json_error(['message' => 'Subscription failed. Please try again.']);
    }
}

// Register the AJAX handlers for both logged-in and non-logged-in users
add_action('wp_ajax_sendy_subscribe', 'sendy_subscribe_handler');
add_action('wp_ajax_nopriv_sendy_subscribe', 'sendy_subscribe_handler');
?>
