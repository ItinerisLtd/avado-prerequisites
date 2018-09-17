<?php
declare(strict_types=1);

namespace Itineris\AvadoPrerequisites;

use WC_Checkout;
use WC_Order;

class PrerequisiteCollectionFactory
{
    protected const HOOK_PREFIX = 'avado_prerequisites_';

    public static function fromWCCheckout(WC_Checkout $checkout): PrerequisiteCollection
    {
        $prerequisiteCollection = new PrerequisiteCollection();
        do_action(static::HOOK_PREFIX . 'from_wc_checkout', $prerequisiteCollection, $checkout);

        return $prerequisiteCollection;
    }

    public static function fromWCOrder(WC_Order $order)
    {
        $prerequisiteCollection = new PrerequisiteCollection();
        do_action(static::HOOK_PREFIX . 'from_wc_order', $prerequisiteCollection, $order);

        return $prerequisiteCollection;
    }
}
