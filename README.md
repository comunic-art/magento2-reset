# Magento 2 Reset module

Reset database removing categories, products, orders, customers and reviews

# Install

Download:

`composer require comunicart/magento2-reset`

Enable module:

`php bin/magento module:enable Comunicart_Reset`

# Usage

`php bin/magento reset:categories`: Reset categories in database

`php bin/magento reset:customers`: Reset customers in database

`php bin/magento reset:orders`: Reset orders in database

`php bin/magento reset:products`: Reset products in database

`php bin/magento reset:reviews`: Reset reviews in database

