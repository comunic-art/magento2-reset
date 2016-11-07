<?php

namespace Comunicart\Reset\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Products extends AbstractCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('reset:products');
        $this->setDescription('Reset products in database.');
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $output->setDecorated(true);
        $sync = $this->objectManager->get('Comunicart\Reset\Model\Products');
        $sync->setOutput($output);
        $sync->reset();
    }

}
