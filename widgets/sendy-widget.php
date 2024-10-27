<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Sendy_Subscription_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'sendy_subscription_form';
    }

    public function get_title() {
        return __('Sendy Subscription Form', 'sendy-subscription-plugin');
    }

    public function get_icon() {
        return 'eicon-mail'; // Elementor icon for email forms
    }

    public function get_categories() {
        return ['general']; // Elementor widget category
    }

    protected function _register_controls() {
        $this->start_controls_section(
            'form_section',
            [
                'label' => __('Form Settings', 'sendy-subscription-plugin'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'name_placeholder',
            [
                'label' => __('Name Placeholder', 'sendy-subscription-plugin'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Your Name', 'sendy-subscription-plugin'),
            ]
        );

        $this->add_control(
            'email_placeholder',
            [
                'label' => __('Email Placeholder', 'sendy-subscription-plugin'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Your Email', 'sendy-subscription-plugin'),
            ]
        );

        $this->add_control(
            'list_id',
            [
                'label' => __('List ID', 'sendy-subscription-plugin'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
            ]
        );

        $this->add_control(
            'submit_text',
            [
                'label' => __('Submit Button Text', 'sendy-subscription-plugin'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Subscribe', 'sendy-subscription-plugin'),
            ]
        );

        $this->add_control(
            'redirect_url',
            [
                'label' => __('Redirect URL', 'sendy-subscription-plugin'),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => __('https://your-redirect-url.com', 'sendy-subscription-plugin'),
            ]
        );

        $this->end_controls_section();



        // Input Field Style Controls
    $this->start_controls_section(
        'input_style_section',
        [
            'label' => __( 'Input Fields', 'glister-addons' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]
    );

    $this->add_control(
        'input_background_color',
        [
            'label' => __( 'Background Color', 'glister-addons' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="number"]' => 'background-color: {{VALUE}};',
            ],
        ]
    );

    $this->add_control(
        'input_text_color',
        [
            'label' => __( 'Text Color', 'glister-addons' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="number"]' => 'color: {{VALUE}};',
            ],
        ]
    );

    $this->add_group_control(
        \Elementor\Group_Control_Typography::get_type(),
        [
            'name' => 'input_typography',
            'selector' => '{{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="number"]',
        ]
    );

    $this->add_group_control(
        \Elementor\Group_Control_Border::get_type(),
        [
            'name' => 'input_border',
            'label' => __( 'Border', 'glister-addons' ),
            'selector' => '{{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="number"]',
        ]
    );

    $this->add_responsive_control(
        'input_border_radius',
        [
            'label' => __( 'Border Radius', 'glister-addons' ),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'selectors' => [
                '{{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="number"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

    $this->add_responsive_control(
        'input_padding',
        [
            'label' => __( 'Padding', 'glister-addons' ),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'selectors' => [
                '{{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="number"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

    $this->add_responsive_control(
        'input_margin',
        [
            'label' => __( 'Margin', 'glister-addons' ),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'selectors' => [
                '{{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="number"], {{WRAPPER}} img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );


    $this->add_responsive_control(
        'column_gap',
        [
            'label' => __( 'Column Gap', 'sendy-subscription-plugin' ),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%', 'em' ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 50,
                    'step' => 1,
                ],
            ],
            'default' => [
                'unit' => 'px',
                'size' => 10,
            ],
            'selectors' => [
                '{{WRAPPER}} .ssf_div' => 'column-gap: {{SIZE}}{{UNIT}};',
            ],
        ]
    );

    $this->end_controls_section();

    // Submit Button Style Controls
    $this->start_controls_section(
        'submit_button_style_section',
        [
            'label' => __( 'Submit Button', 'glister-addons' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]
    );

    $this->start_controls_tabs('button_style_tabs');

    // Normal State
    $this->start_controls_tab(
        'button_normal_tab',
        [
            'label' => __( 'Normal', 'glister-addons' ),
        ]
    );

    $this->add_control(
        'button_background_color',
        [
            'label' => __( 'Background Color', 'glister-addons' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} input[type="submit"]' => 'background-color: {{VALUE}};',
            ],
        ]
    );

    $this->add_control(
        'button_text_color',
        [
            'label' => __( 'Text Color', 'glister-addons' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} input[type="submit"]' => 'color: {{VALUE}};',
            ],
        ]
    );

    $this->add_group_control(
        \Elementor\Group_Control_Typography::get_type(),
        [
            'name' => 'button_typography',
            'selector' => '{{WRAPPER}} input[type="submit"]',
        ]
    );

    $this->add_group_control(
        \Elementor\Group_Control_Border::get_type(),
        [
            'name' => 'button_border',
            'label' => __( 'Border', 'glister-addons' ),
            'selector' => '{{WRAPPER}} input[type="submit"]',
        ]
    );

    $this->add_responsive_control(
        'button_border_radius',
        [
            'label' => __( 'Border Radius', 'glister-addons' ),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'selectors' => [
                '{{WRAPPER}} input[type="submit"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

    $this->add_responsive_control(
        'button_padding',
        [
            'label' => __( 'Padding', 'glister-addons' ),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'selectors' => [
                '{{WRAPPER}} input[type="submit"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

    $this->add_responsive_control(
        'button_margin',
        [
            'label' => __( 'Margin', 'glister-addons' ),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'selectors' => [
                '{{WRAPPER}} input[type="submit"]' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

    $this->end_controls_tab();

    // Hover State
    $this->start_controls_tab(
        'button_hover_tab',
        [
            'label' => __( 'Hover', 'glister-addons' ),
        ]
    );

    $this->add_control(
        'button_hover_background_color',
        [
            'label' => __( 'Background Color', 'glister-addons' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} input[type="submit"]:hover' => 'background-color: {{VALUE}};',
            ],
        ]
    );

    $this->add_control(
        'button_hover_text_color',
        [
            'label' => __( 'Text Color', 'glister-addons' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} input[type="submit"]:hover' => 'color: {{VALUE}};',
            ],
        ]
    );

    $this->add_control(
        'button_hover_border_color',
        [
            'label' => __( 'Hover Border Color', 'glister-addons' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} input[type="submit"]:hover' => 'border-color: {{VALUE}};',
            ],
        ]
    );

    $this->end_controls_tab();

    $this->end_controls_tabs();

    $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $unique_id = uniqid('sf_');
        $nonce = wp_create_nonce('sendy_subscription_form_nonce');
        ?>

        <form id="<?php echo esc_attr($unique_id); ?>">
            <div class="ssf_div" style="display: flex; flex-wrap: nowrap; ">
            <input type="text" id="<?php echo esc_attr($unique_id . '_name'); ?>" 
                name="name" placeholder="<?php echo esc_attr($settings['name_placeholder']); ?>" required />
            
            <input type="email" id="<?php echo esc_attr($unique_id . '_email'); ?>" 
                name="email" placeholder="<?php echo esc_attr($settings['email_placeholder']); ?>" required />
            </div>
            <div class="ssf_div" style="display: flex; flex-wrap: nowrap; align-items: center;">
                <img src="<?php echo plugin_dir_url(__FILE__) . '../captcha/captcha.php'; ?>" alt="CAPTCHA" style="height:30px;" />
                <input type="text" id="<?php echo esc_attr($unique_id . '_captcha_code'); ?>" 
                    name="captcha_code" placeholder="Enter CAPTCHA" required />
            </div>

            <input type="hidden" id="<?php echo esc_attr($unique_id . '_list'); ?>" 
                name="list" value="<?php echo esc_attr($settings['list_id']); ?>">
            
            <input type="hidden" id="<?php echo esc_attr($unique_id . '_sendy_nonce'); ?>" 
                name="sendy_nonce" value="<?php echo esc_attr($nonce); ?>">
            
            <input type="submit" value="<?php echo esc_attr($settings['submit_text']); ?>" />

            <div id="<?php echo esc_attr($unique_id . '_status'); ?>"></div>
        </form>

        <script type="text/javascript">
        jQuery(document).ready(function ($) {
            var formId = '<?php echo esc_js($unique_id); ?>';
            $('#' + formId).on('submit', function (e) {
                e.preventDefault();

                var name = $('#' + formId + '_name').val();
                var email = $('#' + formId + '_email').val();
                var captcha = $('#' + formId + '_captcha_code').val();
                var list = $('#' + formId + '_list').val();
                var nonce = $('#' + formId + '_sendy_nonce').val();

                $.ajax({
                    url: '<?php echo admin_url("admin-ajax.php"); ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'sendy_subscribe',
                        name: name,
                        email: email,
                        captcha: captcha,
                        list: list,
                        sendy_nonce: nonce
                    },
                    success: function (response) {
                        if (response.success) {
                            console.log(response);
                            if(response.data.message == "Already subscribed."){
                                $('#' + formId + '_status').html(response.data.message).css('color', 'green');
                            }else{
                                $('#' + formId + '_status').html(response.data.message).css('color', 'green');
                                if ('<?php echo esc_js($settings['redirect_url']['url']); ?>' !== '') {
                                    window.location.href = '<?php echo esc_js($settings['redirect_url']['url']); ?>';
                                } else {
                                    $('#' + formId)[0].reset();
                                }
                            }
                            
                        } else {
                            $('#' + formId + '_status').html(response.data.message).css('color', 'red');
                        }
                    },
                    error: function () {
                        $('#' + formId + '_status').html('An error occurred. Please try again.').css('color', 'red');
                    }
                });
            });
        });
        </script>

        <?php
    }
}
