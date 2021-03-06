<?php

namespace Ckluster\TutorialBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * A shopping cart
 * 
 * @ORM\Entity
 * 
 * @author arturo
 */
class ShoppingCartItem {

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="User", inversedBy="cartItems")
     *
     * @Assert\NotBlank()
     *
     * @var Ckluster\TutorialBundle\Entity\User
     */
    private $user;
    
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Product")
     * 
     * @Assert\NotBlank()
     * 
     * @var Ckluster\TutorialBundle\Entity\Product
     */
    private $product;
    
    /**
     * @ORM\Column(type="integer")
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     * @Assert\Min(limit=1)
     * 
     * @var integer
     */
    private $quantity;

    public function __construct(User $pUser, Product $pProduct, $piQuantity)
    {
        $this->user = $pUser;
        $this->product = $pProduct;
        $this->quantity = $piQuantity;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $pUser)
    {
        $this->user = $pUser;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function setProduct(Product $pProduct)
    {
        $this->product = $pProduct;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function setQuantity($piQuantity)
    {
        $this->quantity = $piQuantity;
    }

}
