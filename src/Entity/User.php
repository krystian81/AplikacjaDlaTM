<?php
namespace App\Entity;

// src/Entities/User.php
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User
{
	/** 
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
	/** 
     * @ORM\Column(type="string") 
     */
    protected $login;
	/** 
     * @ORM\Column(type="string") 
     */
    protected $password;
	/** 
     * @ORM\Column(type="string") 
     */
    protected $name;
	/** 
     * @ORM\Column(type="string") 
     */
    protected $surname;
	/** 
     * @ORM\Column(type="boolean") 
     */
    protected $admin;

    public function getId()
    {
        return $this->id;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function setLogin($login)
    {
        $this->login = $login;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getSurname()
    {
        return $this->surname;
    }

    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    public function getAdmin()
    {
        return $this->admin;
    }

    public function setAdmin($admin)
    {
        $this->admin = $admin;
    }
}