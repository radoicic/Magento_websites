<?php

declare(strict_types=1);

namespace Amasty\GiftCardGraphQl\Model\Resolver\Customer;

use Amasty\GiftCardAccount\Api\CustomerCardRepositoryInterface;
use Amasty\GiftCardAccount\Api\GiftCardAccountRepositoryInterface;
use Magento\CustomerGraphQl\Model\Customer\GetCustomer;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class RemoveGiftCardCode implements ResolverInterface
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
            $accountId = $this->accountRepository->getByCode($code)->getAccountId();
            $customerCard = $this->customerCardRepository->getByAccountAndCustomerId($accountId, $currentCustomerId);

            if ($customerCard->getCustomerId() == $currentCustomerId) {
                $this->customerCardRepository->delete($customerCard);
                $response = ['message' => __('Gift Card has been successfully removed'), 'error' => false];
            } else {
                $response = ['message' => __('Specified Gift Card for current user is not found.'), 'error' => true];
            }
        } catch (\Exception $e) {
            $response = ['message' => __('Cannot remove gift card.'), 'error' => true];
        }

        return $response;
    }
}
