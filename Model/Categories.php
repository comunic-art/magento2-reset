<?php

namespace Comunicart\Reset\Model;

class Categories extends AbstractReset {
    protected function executeBefore() {
        return [];
    }

    protected function executeAfter() {
        $now = date('Y-m-d H:i:s');
        return [
            "DELETE FROM ".$this->getTableName('url_rewrite')." WHERE entity_type = 'category'",
            "INSERT INTO ".$this->getTableName('catalog_category_entity')." (entity_id, attribute_set_id, parent_id, created_at, updated_at, path, position, level, children_count)
            VALUES
                (1, 0, 0, '$now', '$now', '1', 0, 0, 1),
                (2, 3, 1, '$now', '$now', '1/2', 1, 1, 0);",
        ];
    }

    protected function getTables() {
        return [
            'catalog_category_entity',
            'catalog_category_entity_datetime',
            'catalog_category_entity_decimal',
            'catalog_category_entity_int',
            'catalog_category_entity_text',
            'catalog_category_entity_varchar',
            'catalog_category_product',
            'catalog_category_product_index',
        ];
    }

    public function reset()
    {
        parent::reset();
        $this->_output->writeln('<info>Reindexing...</info>');

        $this->reindex([
            'catalog_category_product',
            'catalog_product_category',
        ]);

        $this->_output->writeln('<info>Reindexing has been performed successfully!</info>');
    }
}