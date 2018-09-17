<?php
declare(strict_types=1);

namespace Itineris\AvadoPrerequisites;

use Closure;
use WC_Checkout;
use WC_Order;

class Prerequisite implements PrerequisiteInterface
{
    /**
     * Key of the field. Must be unique per instance.
     *
     * @var string
     */
    protected $key;

    /**
     * Arguments to be passed into woocommerce_form_field.
     *
     * @var array
     */
    protected $args;

    /**
     * Predicate to determine is prerequisite met.
     * Must return boolean.
     *
     * @var Closure
     */
    protected $predicate;

    /**
     * Prerequisite constructor.
     *
     * @param string  $key       Key of the field. Must be unique per instance.
     * @param array   $args      Arguments to be passed into woocommerce_form_field.
     * @param Closure $predicate Predicate to determine is prerequisite met.
     */
    public function __construct(string $key, array $args, Closure $predicate)
    {
        $this->key = $key;
        $this->args = $args;
        $this->predicate = $predicate;
    }


    /**
     * Display form fields on checkout form by invoking woocommerce_form_field.
     *
     * @param WC_Checkout $checkout
     *
     * @return void
     */
    public function render(WC_Checkout $checkout): void
    {
        woocommerce_form_field(
            $this->key,
            $this->args,
            $checkout->get_value($this->key)
        );
    }

    /**
     * Save user input as post meta.
     * Assume value is a string.
     *
     * @param WC_Order $order
     *
     * @return void
     */
    public function save(WC_Order $order): void
    {
        if (empty($_POST[$this->key])) { // WPCS: CSRF, input var ok.
            return;
        }

        $value = sanitize_text_field(wp_unslash($_POST[$this->key])); // WPCS: CSRF, input var ok.

        // TODO: Ensure post meta is single?
        update_post_meta(
            $order->get_id(),
            $this->key,
            $value
        );
    }

    /**
     * Whether prerequisite for given order is met.
     *
     * @param WC_Order $order
     *
     * @return bool
     */
    public function isMet(WC_Order $order): bool
    {
        $values = get_post_meta(
            $order->get_id(),
            $this->key,
            true
        );

        return (bool) $this->predicate->call($this, $values);
    }

    /**
     * Key getter.
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }
}
