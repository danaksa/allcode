<?php

namespace JpMotor\Basket;

use CSaleBasket;
use CSaleUser;
use Exception;
use Bitrix\Currency\CurrencyManager;
use Bitrix\Main\Loader;

class BasketService
{
    /** @var int */
    private $fuserId;

    /** @var \CAllUser|\CUser */
    private $user;

    public function __construct()
    {
        global $USER;
        Loader::includeModule('sale');

        $this->user = $USER;
        $this->setFuserId();
    }

    public function add(BasketItem $basketItem, int $maxQuantity): int
    {
        $bitrixBasketItem = CSaleBasket::GetList(
            [],
            array(
                'FUSER_ID' => $this->fuserId,
                'LID' => SITE_ID,
                'ORDER_ID' => 'NULL',
                'PRODUCT_ID' => $basketItem->getProductId(),
            ),
            false,
            false,
            ['ID', 'QUANTITY']
        );

        if ($bitrixBasketItem->SelectedRowsCount() > 0) {
            $item = $bitrixBasketItem->Fetch();
            $newQuantity = $item['QUANTITY'] + $basketItem->getQuantity();

            if ($newQuantity > $maxQuantity) {
                $newQuantity = $maxQuantity;
            }

            return CSaleBasket::Update($item['ID'], ['QUANTITY' => $newQuantity]);
        } else {
            if ($basketItem->getQuantity() < 1) {
                $basketItem->setQuantity(1);
            }

            if ($basketItem->getQuantity() > $maxQuantity) {
                $basketItem->setQuantity($maxQuantity);
            }

            $basketFields = [
                'PRODUCT_ID' => $basketItem->getProductId(),
                'FUSER_ID' => $this->fuserId,
                'PRICE' => $basketItem->getPrice(),
                'CURRENCY' => CurrencyManager::getBaseCurrency(),
                'QUANTITY' => $basketItem->getQuantity(),
                'LID' => SITE_ID,
                'DELAY' => 'N',
                'CAN_BUY' => 'Y',
                'NAME' => $basketItem->getBrand().' ['.$basketItem->getArticle().'] '.$basketItem->getName(),
                'MODULE' => 'jpmotor',
                'PROPS' => $this->createProperties($basketItem)
            ];


            return CSaleBasket::Add($basketFields);
        }
    }

    /**
     * @param int $id
     * @param string $comment
     * @return bool
     */
    public function setComment(int $id, string $comment)
    {
        $bitrixBasketItem = CSaleBasket::GetList(
            [],
            array(
                'FUSER_ID' => $this->fuserId,
                'LID' => SITE_ID,
                'ID' => $id,
            ),
            false,
            false,
            ['ID']
        );

        if ($bitrixBasketItem->SelectedRowsCount() > 0) {
            $props = $this->getProperties($id);
            $propertyComment = $this->createProperty('Комментарий', 'comment', $comment);
            $props[$propertyComment['CODE']] = $propertyComment;

            return CSaleBasket::Update($id, ['PROPS' => $props]);
        }

        return false;
    }

    public function getCountItems(): int
    {
        $countItemsRes = CSaleBasket::GetList(
            [],
            array(
                'FUSER_ID' => $this->fuserId,
                'LID' => SITE_ID,
                'ORDER_ID' => 'NULL',
            ),
            false,
            false,
            ['ID']
        );

        return (int)$countItemsRes->SelectedRowsCount();
    }

    private function setFuserId(): void
    {
        if ($this->user->IsAuthorized()) {
            $saleUser = CSaleUser::GetList(['USER_ID' => $this->user->GetID()]);

            if (!$saleUser['ID']) {
                $saleUser['ID'] = CSaleUser::_Add(['USER_ID' => $this->user->GetID()]);
            }

            $this->fuserId = $saleUser['ID'];
        } else {
            $this->fuserId = CSaleBasket::GetBasketUserID();
        }

        if ($this->fuserId < 1) {
            throw new Exception('Fuser not identity');
        }
    }

    private function createProperties(BasketItem $basketItem): array
    {
        $props = [];

        $props[] = $this->createProperty('ID Поставщика', 'supplier_id', $basketItem->getSupplierId());
        $props[] = $this->createProperty('Название', 'part_title', $basketItem->getName());
        $props[] = $this->createProperty('Артикул', 'article', $basketItem->getArticle());
        $props[] = $this->createProperty('Производитель', 'brand_title', $basketItem->getBrand());
        $props[] = $this->createProperty('Комментарий', 'comment', $basketItem->getComment());
        $props[] = $this->createProperty('Доступное количество', 'maxquantity', $basketItem->getMaxQuantity());
        //убрал для корзины для новых алгоритмов
        //$props[] = $this->createProperty('Уникальный ID', 'uid', $basketItem->getUid());

        return $props;
    }

    private function getProperties(int $id): array
    {
        $properties = [];
        $db_res = CSaleBasket::GetPropsList([], ['BASKET_ID' => $id]);

        while ($property = $db_res->Fetch())
        {
            $properties[$property['CODE']] = $property;
        }

        return $properties;
    }

    private function createProperty(string $name, string $code, string $value): array
    {
        return  [
            'NAME' => $name,
            'CODE' => $code,
            'VALUE' => mb_substr($value, 0, 254),
        ];
    }
}
