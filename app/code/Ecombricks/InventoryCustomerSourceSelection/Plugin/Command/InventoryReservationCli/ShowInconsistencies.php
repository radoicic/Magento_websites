<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Command\InventoryReservationCli;

/**
 * Show reservation inconsistencies command plugin
 */
class ShowInconsistencies
{
    /**
     * Get salable quantity inconsistencies
     * 
     * @var \Magento\InventoryReservationCli\Model\GetSalableQuantityInconsistencies
     */
    private $getSalableQuantityInconsistencies;

    /**
     * Get orders total count
     * 
     * @var \Magento\InventoryReservationCli\Model\ResourceModel\GetOrdersTotalCount
     */
    private $getOrdersTotalCount;
    
    /**
     * Filter complete orders
     * 
     * @var \Magento\InventoryReservationCli\Model\SalableQuantityInconsistency\FilterCompleteOrders
     */
    private $filterCompleteOrders;

    /**
     * Filter incomplete orders
     * 
     * @var \Magento\InventoryReservationCli\Model\SalableQuantityInconsistency\FilterIncompleteOrders
     */
    private $filterIncompleteOrders;

    /**
     * Logger
     * 
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * Constructor
     * 
     * @param \Magento\InventoryReservationCli\Model\GetSalableQuantityInconsistencies $getSalableQuantityInconsistencies
     * @param \Magento\InventoryReservationCli\Model\ResourceModel\GetOrdersTotalCount $getOrdersTotalCount
     * @param \Magento\InventoryReservationCli\Model\SalableQuantityInconsistency\FilterCompleteOrders $filterCompleteOrders
     * @param \Magento\InventoryReservationCli\Model\SalableQuantityInconsistency\FilterIncompleteOrders $filterIncompleteOrders
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\InventoryReservationCli\Model\GetSalableQuantityInconsistencies $getSalableQuantityInconsistencies,
        \Magento\InventoryReservationCli\Model\ResourceModel\GetOrdersTotalCount $getOrdersTotalCount,
        \Magento\InventoryReservationCli\Model\SalableQuantityInconsistency\FilterCompleteOrders $filterCompleteOrders,
        \Magento\InventoryReservationCli\Model\SalableQuantityInconsistency\FilterIncompleteOrders $filterIncompleteOrders,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->getSalableQuantityInconsistencies = $getSalableQuantityInconsistencies;
        $this->getOrdersTotalCount = $getOrdersTotalCount;
        $this->filterCompleteOrders = $filterCompleteOrders;
        $this->filterIncompleteOrders = $filterIncompleteOrders;
        $this->logger = $logger;
    }

    /**
     * Around execute
     * 
     * @param \Magento\InventoryReservationCli\Command\ShowInconsistencies $subject
     * @param callable $proceed
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Validation\ValidationException
     * @throws \Magento\InventoryConfigurationApi\Exception\SkuIsNotAssignedToStockException
     */
    public function aroundExecute(
        \Magento\InventoryReservationCli\Command\ShowInconsistencies $subject,
        callable $proceed,
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ): int
    {
        $startTime = $this->getTime();
        $isRawOutput = (bool) $input->getOption('raw');
        $bunchSize = (int) $input->getOption('bunch-size');
        $maxPage = $this->retrieveMaxPage($bunchSize);
        $hasInconsistencies = false;
        for ($page = 1; $page <= $maxPage; $page++) {
            $pageStartTime = $this->getTime();
            $inconsistencies = $this->getSalableQuantityInconsistencies->execute($bunchSize, $page);
            if ($input->getOption('complete-orders')) {
                $inconsistencies = $this->filterCompleteOrders->execute($inconsistencies);
            } elseif ($input->getOption('incomplete-orders')) {
                $inconsistencies = $this->filterIncompleteOrders->execute($inconsistencies);
            }
            $hasInconsistencies = !empty($inconsistencies);
            if ($isRawOutput) {
                $this->rawOutput($output, $inconsistencies);
            } else {
                $this->prettyOutput($output, $inconsistencies);
            }
            $this->logger->debug(
                'Bunch processed for reservation inconsistency check',
                [ 'duration' => $this->getDuration($pageStartTime), 'memory_usage' => $this->getMemoryUsage(), 'bunch_size' => $bunchSize, 'page' => $page ]
            );
        }
        if ($hasInconsistencies === false) {
            $output->writeln('<info>No order inconsistencies were found</info>');
            return 0;
        }
        $this->logger->debug(
            'Finished reservation inconsistency check',
            [ 'duration' => $this->getDuration($startTime), 'memory_usage' => $this->getMemoryUsage() ]
        );
        return -1;
    }
    
    /**
     * Get time
     * 
     * @return float
     */
    private function getTime(): float
    {
        return $this->microtime(true);
    }
    
    /**
     * Get duration
     * 
     * @param float $startTime
     * @return string
     */
    private function getDuration(float $startTime): string
    {
        return sprintf('%.2fs', ($this->getTime() - $startTime));
    }
    
    /**
     * Get memory usage
     * 
     * @return string
     */
    private function getMemoryUsage(): string
    {
        return sprintf('%.2fMB', (memory_get_peak_usage(true) / 1024 / 1024));
    }

    /**
     * Retrieve max page
     *
     * @param int $bunchSize
     * @return int
     */
    private function retrieveMaxPage(int $bunchSize): int
    {
        $ordersTotalCount = $this->getOrdersTotalCount->execute();
        return (int) ceil($ordersTotalCount / $bunchSize);
    }
    
    /**
     * Pretty output
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservationCli\SalableQuantityInconsistency[] $inconsistencies
     */
    private function prettyOutput(\Symfony\Component\Console\Output\OutputInterface $output, array $inconsistencies): void
    {
        $format = ' - Product <comment>%s</comment> should be compensated by <comment>%+f</comment> for source <comment>%s</comment>';
        foreach ($inconsistencies as $inconsistency) {
            $inconsistentItems = $inconsistency->getItems();
            $output->writeln(sprintf('Order <comment>%s</comment>:', $inconsistency->getOrderIncrementId()));
            foreach ($inconsistentItems as $sku => $qty) {
                $output->writeln(sprintf($format, $sku, -$qty, $inconsistency->getSourceCode()));
            }
        }
    }

    /**
     * Raw output
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservationCli\SalableQuantityInconsistency[] $inconsistencies
     */
    private function rawOutput(\Symfony\Component\Console\Output\OutputInterface $output, array $inconsistencies): void
    {
        foreach ($inconsistencies as $inconsistency) {
            $inconsistentItems = $inconsistency->getItems();
            foreach ($inconsistentItems as $sku => $qty) {
                $output->writeln(sprintf('%s:%s:%f:%s', $inconsistency->getOrderIncrementId(), $sku, -$qty, $inconsistency->getSourceCode()));
            }
        }
    }
}