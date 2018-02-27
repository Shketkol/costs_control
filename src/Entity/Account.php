<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AccountRepository")
 */
class Account
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="accounts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;


	/**
     * @ORM\Column(type="string", length=150)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AccountType", inversedBy="accounts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    public function getUser(): User
    {
        return $this->category;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
    	$this->name = $name;
    }

    public function getType(): AccountType
    {
        return $this->type;
    }

    public function setType(AccountType $type)
    {
        $this->type = $type;
    }
}
