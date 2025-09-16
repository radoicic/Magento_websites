<?php

declare(strict_types=1);

namespace Amasty\GiftCardGraphQl\Model\Resolver\Customer;

use Amasty\GiftCardAccount\Api\CustomerCardRepositoryInterface;
use Amasty\GiftCardAccount\Api\GiftCardAccountRepositoryInterface;
use Magento\CustomerGraphQl\Model\Customer\GetCustomer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class AddGiftCardCode implements ResolverInterface
{
    const CODE_KEY = 'am_giftcard_code';

    /**
     * @var GetCustomer
     */
    private $getCustomer;

    /**
     * @var CustomerCardRepositoryInterface
     */
    private $customerCardRepository;

    /**
     * @var GiftCardAccountRepositoryInterface
     */
    private $accountRepository;

    public function __construct(
        GetCustomer $getCustomer,
        CustomerCardRepositoryInterface $customerCardRepository,
        GiftCardAccountRepositoryInterface $accountRepository
    ) {
        $this->getCustomer = $getCustomer;
        $this->customerCardRepository = $customerCardRepository;
        $this->accountRepository = $accountRepository;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($args['input'][self::CODE_KEY]) || empty($args['input'][self::CODE_KEY])) {
            throw new GraphQlInputException(__("Required parameter '%1' is missing.", self::CODE_KEY));
        }

        $code = $args['input'][self::CODE_KEY];
        $currentCustomerId = (int)$this->getCustomer->execute($context)->getId();

        try {
            $account = $this->accountRepository->getByCode($code);
        } catch (LocalizedException $r) {
            return [
                'message' => __('Wrong Gift Card code.'),
                'error' => true
            ];
        }

        if ($this->customerCardRepository->hasCardForAccountId($account->getAccountId())) {
            return [
                'message' => __('This Gift Code already exists.'),
                'error' => true
            ];
        }

        $customerCard = $this->customerCardRepository->getEmptyCustomerCardModel();
        $customerCard->setCustomerId($currentCustomerId);
        $customerCard->setAccountId($account->getAccountId());
        $this->customerCardRepository->save($customerCard);

        return [
            'message' => __('Gift Code added.'),
            'error' => false
        ];
    }
}
