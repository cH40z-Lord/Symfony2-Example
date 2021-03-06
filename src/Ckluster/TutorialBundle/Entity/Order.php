<?php

namespace Ckluster\TutorialBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * An order.
 * @ORM\Entity
 * @ORM\Table(name="ShoppingOrder")
 * 
 * @author arturo
 */
class Order extends BaseEntity {

    /**
     * @ORM\Column(type="datetime")
     *
     * @Assert\NotBlank()
     *
     * @var DateTime
     */
    private $dateOfPurchase;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="orders")
     *
     * @Assert\NotBlank()
     *
     * @var Ckluster\TutorialBundle\Entity\User
     */
    private $user;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank()
     * @Assert\Choice({"processed", "intransit", "delivered", "notdelivered"})
     *
     * @var string
     */
    private $status;

    /**
     * @ORM\Column(type="decimal", precision=16, scale=2)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="numeric")
     * 
     * @var string
     */
    private $subtotal;

     /**
     * @ORM\Column(type="decimal", precision=16, scale=4)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="numeric")
     *
     * @var string
     */
    private $taxPercentage;

    /**
     * @ORM\OneToMany(targetEntity="OrderItem", mappedBy="order", cascade={"all"})
     *
     * @var Doctrine\Common\Collections\ArrayCollection
     */

    private $items;

    public function __construct(User $pUser, $psTaxPercentage, $paItems)
    {
        parent::__construct();
        
        $this->dateOfPurchase = $this->now();
        $this->user = $pUser;
        $this->status = 'processed';
        $this->subtotal = '0';
        $this->taxPercentage = $psTaxPercentage;
        $this->items = new ArrayCollection();
        
        foreach ($paItems as $item) {
            $this->addItem($item);
        }
    }

    public function addItem($poItem)
    {
        if ($poItem instanceof OrderItem) {
            $this->addOrderItem($poItem);
        } else {
            $orderItem = new OrderItem($this, $poItem->getProduct(),
                                       $poItem->getQuantity());
            $this->addOrderItem($orderItem);
        }
    }

    private function addOrderItem(OrderItem $item)
    {
        if ($item->getOrder()->getId() !== $this->getId()) {
            return;
        }
        
        $product = $item->getProduct();
        $cost = \bcmul($product->getCost(), strval($item->getQuantity()));
        $this->subtotal = \bcadd($this->subtotal, $cost);

        foreach ($this->items as $presentItem) {
            if ($presentItem->getProduct()->id === $item->getProduct()->id) {
                $newQuantity = $presentItem->getQuantity() + $item->getQuantity();
                $presentItem->setQuantity($newQuantity);
                return;
            }
        }

        $this->items[] = $item;
    }

    public function getDateOfPurchase()
    {
        return $this->dateOfPurchase;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $pUser)
    {
        $this->user = $pUser;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setErrorInDelivery()
    {
        $this->status = 'notdelivered';
    }

    public function setNextStageInDelivery()
    {
        switch ($this->status) {
            case 'processed':
                $this->status = 'intransit';
                break;
            case 'intransit':
                $this->status = 'delivered';
                break;
        }
    }

    public function getSubtotal()
    {
        return $this->subtotal;
    }

    public function getTotal()
    {
        return \bcadd($this->subtotal, $this->getTax());
    }

    public function getTaxPercentage()
    {
        return $this->taxPercentage;
    }

    public function setTaxPercentage($psTaxPercentage)
    {
        $this->taxPercentage = $psTaxPercentage;
    }

    public function getTax()
    {
        return \bcmul($this->subtotal, $this->taxPercentage);
    }

    public function getItems()
    {
        return $this->items;
    }

}
