<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Command\InventoryReservationCli;

/**
 * Create reservation compensations command plugin
 */
class CreateCompensations
{
    /**
     * Get command line standard input
     * 
     * @var \Magento\InventoryReservationCli\Command\Input\GetCommandlineStandardInput
     */
    private $getCommandlineStandardInput;

    /**
     * Get reservation from compensation argument
     * 
     * @var \Magento\InventoryReservationCli\Command\Input\GetReservationFromCompensationArgument
     */
    private $getReservationFromCompensationArgument;

    /**
     * Append reservations
     * 
     * @var \Magento\InventoryReservationsApi\Model\AppendReservationsInterface
     */
    private $appendReservations;

    /**
     * Constructor
     * 
     * @param \Magento\InventoryReservationCli\Command\Input\GetCommandlineStandardInput $getCommandlineStandardInput
     * @param \Magento\InventoryReservationCli\Command\Input\GetReservationFromCompensationArgument $getReservationFromCompensationArgument
     * @param \Magento\InventoryReservationsApi\Model\AppendReservationsInterface $appendReservations
     */
    public function __construct(
        \Magento\InventoryReservationCli\Command\Input\GetCommandlineStandardInput $getCommandlineStandardInput,
        \Magento\InventoryReservationCli\Command\Input\GetReservationFromCompensationArgument $getReservationFromCompensationArgument,
        \Magento\InventoryReservationsApi\Model\AppendReservationsInterface $appendReservations
    )
    {
        $this->getCommandlineStandardInput = $getCommandlineStandardInput;
        $this->getReservationFromCompensationArgument = $getReservationFromCompensationArgument;
        $this->appendReservations = $appendReservations;
    }

    /**
     * Around execute
     * 
     * @param \Magento\InventoryReservationCli\Command\CreateCompensations $subject
     * @param callable $proceed
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     * @throws \Magento\Framework\Validation\ValidationException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function aroundExecute(
        \Magento\InventoryReservationCli\Command\CreateCompensations $subject,
        callable $proceed,
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ): int
    {
        $output->writeln('<info>Following reservations were created:</info>');
        $hasErrors = false;
        foreach ($this->getCompensationArguments($input) as $compensationArgument) {
            try {
                $reservation = $this->getReservationFromCompensationArgument->execute($compensationArgument);
                $this->appendReservations->execute([$reservation]);
                $format = ' - Product <comment>%s</comment> was compensated by <comment>%+f</comment> for source <comment>%s</comment>';
                $output->writeln(sprintf($format, $reservation->getSku(), $reservation->getQuantity(), $reservation->getSourceCode()));
            } catch (\Magento\Framework\Exception\CouldNotSaveException $exception) {
                $hasErrors = true;
                $output->writeln(sprintf(' - <error>%s</error>', $exception->getMessage()));
            } catch (\Symfony\Component\Console\Exception\InvalidArgumentException $exception) {
                $hasErrors = true;
                $format = ' - <error>Error while parsing argument "%s". %s</error>';
                $output->writeln(sprintf($format, $compensationArgument, $exception->getMessage()));
            } catch (\Exception $exception) {
                $format = ' - <error>Argument "%s" caused exception "%s"</error>';
                $output->writeln(sprintf($format, $compensationArgument, $exception->getMessage()));
            }
        }
        return $hasErrors ? 1 : 0;
    }
    
    /**
     * Get compensation arguments
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @return array
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    private function getCompensationArguments(\Symfony\Component\Console\Input\InputInterface $input): array
    {
        $compensationArguments = $input->getArgument('compensations');
        if (empty($compensationArguments)) {
            $compensationArguments = $this->getCommandlineStandardInput->execute();
        }
        if (empty($compensationArguments)) {
            throw new \Symfony\Component\Console\Exception\InvalidArgumentException('A list of compensations needs to be defined as argument or STDIN.');
        }
        return $compensationArguments;
    }
}