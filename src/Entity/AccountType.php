<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AccountTypeRepository")
 */
class AccountType
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Account", mappedBy="type")
     */
    private $accounts;

	public function getName() : string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
    	$this->name = $name;
    }

    /**
     * @return Collection|Accounts[]
     */
    public function getAccounts() : Collection
    {
        return $this->accounts;
    }
}
