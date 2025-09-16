<?php

declare(strict_types=1);

namespace Amasty\GiftCardGraphQl\Model\Resolver;

use Amasty\GiftCardAccount\Api\GiftCardAccountRepositoryInterface;
use Amasty\GiftCardAccount\Model\OptionSource\AccountStatus;
use Amasty\GiftCardGraphQl\Utils\MoneyFormatter;
use Magento\CustomerGraphQl\Model\Customer\GetCustomer;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Stdlib\DateTime\DateTime;

class GetUserAccounts implements ResolverInterface
{
    /**
     * @var GiftCardAccountRepositoryInterface
     */
    private $accountRepository;

    /**
     * @var MoneyFormatter
     */
    private $moneyFormatter;

    /**
     * @var DateTime
     */
    private $date;

    /**
     * @var GetCustomer
     */
    private $getCustomer;

    /**
     * @var AccountStatus
     */
    private $accountStatus;

    public function __construct(
        GiftCardAccountRepositoryInterface $accountRepository,
        MoneyFormatter $moneyFormatter,
        DateTime $date,
        GetCustomer $getCustomer,
        AccountStatus $accountStatus
    ) {
        $this->accountRepository = $accountRepository;
        $this->moneyFormatter = $moneyFormatter;
        $this->date = $date;
        $this->getCustomer = $getCustomer;
        $this->accountStatus = $accountStatus;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $customer = $this->getCustomer->execute($context);
        $accounts = $this->accountRepository->getAccountsByCustomerId((int)$customer->getId());
        $store = $context->getExtensionAttributes()->getStore();
        $statusArray = $this->accountStatus->toArray();

        return array_map(function ($account) use ($store, $statusArray) {
            $expiredData = !$account->getExpiredDate()
                ? 'Unlimited'
                : $this->date->date('Y-m-d', $account->getExpiredDate());

            return [
                'code' => $account->getCodeModel()->getCode(),
                'current_balance' => $this->moneyFormatter->format($account->getCurrentValue(), $store),
                'status' => $statusArray[$account->getStatus()] ?? 'undefined',
                'expiration_date' => $expiredData
            ];
        }, $accounts);
    }
}
