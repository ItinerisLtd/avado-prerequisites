<?php
declare(strict_types=1);

namespace Itineris\AvadoPrerequisites;

use WC_Checkout;
use WC_Order;

class PrerequisiteCollection
{
    /**
     * Holds all prerequisite instances.
     *
     * @var PrerequisiteInterface[]
     */
    protected $prerequisites = [];

    public function add(PrerequisiteInterface $prerequisite): void
    {
        $this->prerequisites[$prerequisite->getKey()] = $prerequisite;
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
        foreach ($this->prerequisites as $prerequisite) {
            $prerequisite->render($checkout);
        }
    }

    /**
     * Save user input as post meta.
     *
     * @param WC_Order $order
     *
     * @return void
     */
    public function save(WC_Order $order): void
    {
        foreach ($this->prerequisites as $prerequisite) {
            $prerequisite->save($order);
        }
    }

    /**
     * Whether prerequisites for given order are met.
     *
     * @param WC_Order $order
     *
     * @return bool
     */
    public function isMet(WC_Order $order): bool
    {
        foreach ($this->prerequisites as $prerequisite) {
            if (! $prerequisite->isMet($order)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Whether this collection is considered to be empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->prerequisites);
    }
}
