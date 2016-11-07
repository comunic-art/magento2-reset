<?php

namespace Comunicart\Reset\Model;

use Magento\Store\Model\StoreManagerInterface;

class Orders extends AbstractReset {
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
	\Magento\Indexer\Model\IndexerFactory $indexerFactory,
        StoreManagerInterface $storeManager
    )
    {
        parent::__construct($resource, $indexerFactory);
        $this->_storeManager = $storeManager;
    }

    protected function executeBefore() {
        return [];
    }

    protected function executeAfter() {
        return [];
    }

    protected function getTables() {
        $tables = [
            'eav_entity_store',
            'report_compared_product_index',
            'report_event',
            'sales_bestsellers_aggregated_daily',
            'sales_bestsellers_aggregated_monthly',
            'sales_bestsellers_aggregated_yearly',
            'sales_creditmemo',
            'sales_creditmemo_comment',
            'sales_creditmemo_grid',
            'sales_creditmemo_item',
            'sales_invoice',
            'sales_invoiced_aggregated',
            'sales_invoiced_aggregated_order',
            'sales_invoice_comment',
            'sales_invoice_grid',
            'sales_invoice_item',
            'sales_order',
            'sales_order_address',
            'sales_order_aggregated_created',
            'sales_order_aggregated_updated',
            'sales_order_grid',
            'sales_order_item',
            'sales_order_payment',
            'sales_order_status_history',
            'sales_order_tax',
            'sales_order_tax_item',
            'sales_payment_transaction',
            'sales_refunded_aggregated',
            'sales_refunded_aggregated_order',
            'sales_shipment',
            'sales_shipment_comment',
            'sales_shipment_grid',
            'sales_shipment_item',
            'sales_shipment_track',
            'sales_shipping_aggregated',
            'sales_shipping_aggregated_order',
            'quote',
            'quote_address',
            'quote_address_item',
            'quote_id_mask',
            'quote_item',
            'quote_item_option',
            'quote_payment',
            'quote_shipping_rate',
        ];

        foreach ($this->_storeManager->getStores(true) as $store) {
            foreach (['invoice', 'order', 'shipment', 'creditmemo'] as $type) {
                $tables[] = 'sequence_' . $type . '_' . $store->getId();
            }
        }

        return $tables;
    }
}
