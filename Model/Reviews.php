<?php

namespace Comunicart\Reset\Model;

class Reviews extends AbstractReset {
    protected function executeBefore() {
        return [];
    }

    protected function executeAfter() {
        return [];
    }

    protected function getTables() {
        return [
            'rating_option_vote',
            'rating_option_vote_aggregated',
            'review',
            'review_detail',
            'review_entity_summary',
            'review_store',
        ];
    }
}