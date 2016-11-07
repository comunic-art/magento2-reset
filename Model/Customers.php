<?php

namespace Comunicart\Reset\Model;

class Customers extends AbstractReset {
    protected function executeBefore() {
        return [];
    }

    protected function executeAfter() {
        return [];
    }

    protected function getTables() {
        return [
            'customer_address_entity',
            'customer_address_entity_datetime',
            'customer_address_entity_decimal',
            'customer_address_entity_int',
            'customer_address_entity_text',
            'customer_address_entity_varchar',
            'customer_entity',
            'customer_entity_datetime',
            'customer_entity_decimal',
            'customer_entity_int',
            'customer_entity_text',
            'customer_entity_varchar',
            'wishlist',
            'wishlist_item',
            'wishlist_item_option',
            'report_viewed_product_index',
        ];
    }

    public function reset()
    {
        parent::reset();
        $this->_output->writeln('<info>Reindexing...</info>');

        $this->reindex([
            'customer_grid',
        ]);

        $this->_output->writeln('<info>Reindexing has been performed successfully!</info>');
    }
}