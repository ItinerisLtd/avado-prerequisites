<?php
/**
 * Plugin Name:     Avado Prerequisites
 * Plugin URI:      https://www.itineris.co.uk/
 * Description:     Verify education and languages prerequisites for Avado before WooCommerce checkout.
 * Version:         0.1.0
 * Author:          Itineris Limited
 * Author URI:      https://www.itineris.co.uk/
 * License:         GPL-2.0-or-later
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:     avado-prerequisites
 */

declare(strict_types=1);

namespace Itineris\AvadoPrerequisites;

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

/**
 * Begins execution of the plugin.
 *
 * @return void
 */
function run(): void
{
    $prerequisites = new Prerequisites();

    add_action('woocommerce_before_order_notes', [$prerequisites, 'render']);
    add_action('woocommerce_checkout_update_order_meta', [$prerequisites, 'save']);
    add_action('woocommerce_checkout_order_processed', [$prerequisites, 'intercept']);
}

run();
