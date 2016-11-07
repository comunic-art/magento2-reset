<?php

namespace Comunicart\Reset\Model;

class Products extends AbstractReset {
    protected function executeBefore() {
        return [];
    }

    protected function executeAfter() {
        return [
            "DELETE FROM ".$this->getTableName('url_rewrite')." WHERE entity_type = 'product'"
        ];
    }

    protected function getTables() {
        return [
            'sendfriend_log',
            'catalog_product_bundle_option',
            'catalog_product_bundle_option_value',
            'catalog_product_bundle_selection',
            'catalog_product_entity_datetime',
            'catalog_product_entity_decimal',
            'catalog_product_entity_gallery',
            'catalog_product_entity_int',
            'catalog_product_entity_media_gallery',
            'catalog_product_entity_media_gallery_value',
            'catalog_product_entity_text',
            'catalog_product_entity_tier_price',
            'catalog_product_entity_varchar',
            'catalog_product_link',
            'catalog_product_link_attribute_decimal',
            'catalog_product_link_attribute_int',
            'catalog_product_link_attribute_varchar',
            'catalog_product_option',
            'catalog_product_option_price',
            'catalog_product_option_title',
            'catalog_product_option_type_price',
            'catalog_product_option_type_title',
            'catalog_product_option_type_value',
            'catalog_product_super_attribute_label',
            'catalog_product_super_attribute',
            'catalog_product_super_link',
            'catalog_product_website',
            'catalog_category_product_index',
            'catalog_category_product',
            'cataloginventory_stock_item',
            'cataloginventory_stock_status',
            'catalog_product_entity',
            'search_query',
            'wishlist',
            'wishlist_item',
            'wishlist_item_option',
            'report_viewed_product_aggregated_daily',
            'report_viewed_product_aggregated_monthly',
            'report_viewed_product_aggregated_yearly',
            'report_viewed_product_index',
        ];
    }

    public function reset()
    {
        parent::reset();
        $this->_output->writeln('<info>Reindexing...</info>');

        $this->reindex([
            'catalog_category_product',
            'catalog_product_category',
            'catalog_product_price',
            'catalog_product_attribute',
            'cataloginventory_stock',
            'catalogsearch_fulltext',
        ]);

        $this->_output->writeln('<info>Reindexing has been performed successfully!</info>');
    }
}