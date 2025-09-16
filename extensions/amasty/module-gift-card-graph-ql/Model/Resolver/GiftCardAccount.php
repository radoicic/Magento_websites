<?php
declare(strict_types=1);

namespace Amasty\GiftCardGraphQl\Model\Resolver;

use Amasty\GiftCardAccount\Api\GiftCardAccountRepositoryInterface;
use Amasty\GiftCardAccount\Model\OptionSource\AccountStatus;
use Amasty\GiftCardGraphQl\Utils\MoneyFormatter;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Api\Data\StoreInterface;

class GiftCardAccount implements ResolverInterface
{
    const GIFT_CARD_CODE_KEY = 'am_gift_card_code';

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
     * @var AccountStatus
     */
    private $accountStatus;

    public function __construct(
        GiftCardAccountRepositoryInterface $accountRepository,
        MoneyFormatter $moneyFormatter,
        DateTime $date,
        AccountStatus $accountStatus
    ) {
        $this->accountRepository = $accountRepository;
        $this->moneyFormatter = $moneyFormatter;
        $this->date = $date;
        $this->accountStatus = $accountStatus;
    }

    /**
     * @inheritDoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (empty($args['input'][self::GIFT_CARD_CODE_KEY])) {
            throw new GraphQlInputException(__("Required parameter '%1' is missing.", self::GIFT_CARD_CODE_KEY));
        }

        return $this->getByCode(
            $args['input'][self::GIFT_CARD_CODE_KEY],
            $context->getExtensionAttributes()->getStore()
        );
    }

    /**
     * @param string $code
     * @param StoreInterface $store
     *
     * @return array
     * @throws GraphQlInputException
     * @throws LocalizedException
     */
    protected function getByCode(string $code, StoreInterface $store): array
    {
        try {
            $account = $this->accountRepository->getByCode($code);
        } catch (LocalizedException $e) {
            throw new GraphQlInputException(__($e->getMessage()), $e);
        }
        $statusArray = $this->accountStatus->toArray();

        if (!$account->getExpiredDate()) {
            $expiredData = 'Unlimited';
        } else {
            $expiredData = $this->date->date('Y-m-d', $account->getExpiredDate());
        }
        return [
            'code' => $account->getCodeModel()->getCode(),
            'current_balance' => $this->moneyFormatter->format($account->getCurrentValue(), $store),
            'status' => $statusArray[$account->getStatus()] ?? 'undefined',
            'expiration_date' => $expiredData
        ];
    }
}
