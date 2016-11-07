<?php

namespace Comunicart\Reset\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\ObjectManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends \Symfony\Component\Console\Command\Command {
    public function __construct(ObjectManagerInterface $objectManager) {
        $this->objectManager = $objectManager;
        parent::__construct();
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $areaCode = Area::AREA_ADMINHTML;
        /** @var \Magento\Framework\App\State $appState */
        $appState = $this->objectManager->get('Magento\Framework\App\State');
        $appState->setAreaCode($areaCode);
        /** @var \Magento\Framework\ObjectManager\ConfigLoaderInterface $configLoader */
        $configLoader = $this->objectManager->get('Magento\Framework\ObjectManager\ConfigLoaderInterface');
        $this->objectManager->configure($configLoader->load($areaCode));
        
        $this->objectManager->get('Magento\Store\Model\StoreManagerInterface')->setCurrentStore(Area::AREA_ADMIN);
    }
}

