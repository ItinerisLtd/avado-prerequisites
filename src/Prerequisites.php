<?php
declare(strict_types=1);

namespace Itineris\AvadoPrerequisites;

use WC_Checkout;

class Prerequisites
{
    public const FIELDS_HOOK = 'avado_prerequisites_fields';
    public const IS_MET_HOOK = 'avado_prerequisites_is_met';
    public const BEFORE_NOT_MET_REDIRECTION_HOOK = 'avado_prerequisites_before_not_met_redirection';

    /**
     * The fields.
     *
     * @var array
     */
    protected $fields;

    public function __construct()
    {
        $this->fields = (array) apply_filters(static::FIELDS_HOOK, []);
    }

    public function render(WC_Checkout $checkout): void
    {
        if (empty($this->fields)) {
            return;
        }

        echo '<div id="prerequisites">';
        echo '<h2>' . esc_html_e('Language and Education', 'avado-prerequisites') . '</h2>';

        foreach ($this->fields as $key => $args) {
            woocommerce_form_field(
                $key,
                $args,
                $checkout->get_value($key)
            );
        }

        echo '</div>';
    }

    public function save(int $orderId): void
    {
        if (empty($this->fields)) {
            return;
        }

        $keys = array_keys($this->fields);
        foreach ($keys as $key) {
            if (empty($_POST[$key])) { // WPCS: CSRF, input var ok.
                continue;
            }

            $value = sanitize_text_field(wp_unslash($_POST[$key])); // WPCS: CSRF, input var ok.
            update_post_meta($orderId, $key, $value);
        }
    }

    public function intercept(int $orderId): void
    {
        if ($this->isMet($orderId)) {
            return;
        }

        $order = wc_get_order($orderId);
        $order->update_status('on-hold', __('Prerequisites not met', 'woocommerce'));
        wc_empty_cart();

        // TODO!
        $redirectUrl = home_url('/todo/prerequisites-not-meet');

        do_action(static::BEFORE_NOT_MET_REDIRECTION_HOOK, $orderId);

        if (wp_doing_ajax()) {
            wp_send_json([
                'result' => 'success',
                'redirect' => $redirectUrl,
            ]);
        }

        wp_safe_redirect($redirectUrl);
        exit;
    }

    protected function isMet(int $orderId): bool
    {
        if (empty($this->fields)) {
            return true;
        }

        $keys = array_keys($this->fields);
        $values = [];

        foreach ($keys as $key) {
            $values[$key] = get_post_meta($orderId, $key);
        }

        return (bool) apply_filters(static::IS_MET_HOOK, true, $values, $orderId);
    }
}
