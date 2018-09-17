<?php
declare(strict_types=1);

namespace Itineris\AvadoPrerequisites;

use WC_Checkout;

class Plugin
{
    public static function render(WC_Checkout $checkout): void
    {
        $prerequisiteCollection = PrerequisiteCollectionFactory::fromWCCheckout($checkout);

        if ($prerequisiteCollection->isEmpty()) {
            return;
        }

        echo '<div id="prerequisites">';
        echo '<h2>' . esc_html_e('Language and Education', 'avado-prerequisites') . '</h2>';
        $prerequisiteCollection->render($checkout);
        echo '</div>';
    }

    public static function save(int $orderId): void
    {
        $order = wc_get_order($orderId);
        $prerequisiteCollection = PrerequisiteCollectionFactory::fromWCOrder($order);

        $prerequisiteCollection->save($order);
    }

    public static function intercept(int $orderId): void
    {
        $order = wc_get_order($orderId);
        $prerequisiteCollection = PrerequisiteCollectionFactory::fromWCOrder($order);

        if ($prerequisiteCollection->isMet($order)) {
            return;
        }

        $order = wc_get_order($orderId);
        $order->update_status('on-hold', __('Prerequisites not met', 'woocommerce'));
        wc()->cart->empty_cart();

        // TODO!
        $redirectUrl = home_url('/todo/prerequisites-not-met');

        do_action('avado_prerequisites_before_not_met_redirection', $order);

        if (wp_doing_ajax()) {
            wp_send_json([
                'result' => 'success',
                'redirect' => $redirectUrl,
            ]);
        }

        wp_safe_redirect($redirectUrl);
        exit;
    }
}
