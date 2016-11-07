<?php

namespace Comunicart\Reset\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Reviews extends AbstractCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('reset:reviews');
        $this->setDescription('Reset reviews in database.');
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $output->setDecorated(true);
        $sync = $this->objectManager->get('Comunicart\Reset\Model\Reviews');
        $sync->setOutput($output);
        $sync->reset();
    }

}
