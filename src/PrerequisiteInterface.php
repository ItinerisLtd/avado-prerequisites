<?php
declare(strict_types=1);

namespace Itineris\AvadoPrerequisites;

use WC_Checkout;
use WC_Order;

interface PrerequisiteInterface
{
    /**
     * Display form fields on checkout form by invoking woocommerce_form_field.
     *
     * @param WC_Checkout $checkout
     *
     * @return void
     */
    public function render(WC_Checkout $checkout): void;

    /**
     * Save user input as post meta.
     *
     * @param WC_Order $order
     *
     * @return void
     */
    public function save(WC_Order $order): void;

    /**
     * Whether prerequisite for given order is met.
     *
     * @param WC_Order $order
     *
     * @return bool
     */
    public function isMet(WC_Order $order): bool;

    /**
     * Key getter.
     *
     * The one you passed into woocommerce_form_field during self::render. Must be unique per instance.
     *
     * @return string
     */
    public function getKey(): string;
}
