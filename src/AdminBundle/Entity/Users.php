<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;
use Doctrine\ORM\Mapping\Index;

/**
 * users
 *
 * @ORM\Table(name="users", indexes={@Index(name="search_idx", columns={"email"})})
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\UsersRepository")
 */
class Users
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthday", type="datetime", nullable=true)
     */
    private $birthday;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="home_city", type="string", length=255, nullable=true)
     */
    private $homeCity;

    /**
     * @var string
     *
     * @ORM\Column(name="home_street", type="string", length=255, nullable=true)
     */
    private $homeStreet;

    /**
     * @var string
     *
     * @ORM\Column(name="home_zip", type="string", length=255, nullable=true)
     */
    private $homeZip;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="company_name", type="string", length=255, nullable=true)
     */
    private $companyName;

    /**
     * @var string
     *
     * @ORM\Column(name="company_city", type="string", length=255, nullable=true)
     */
    private $companyCity;

    /**
     * @var string
     *
     * @ORM\Column(name="company_street", type="string", length=255, nullable=true)
     */
    private $companyStreet;

    /**
     * @var string
     *
     * @ORM\Column(name="job_position", type="string", length=255, nullable=true)
     */
    private $jobPosition;

    /**
     * @var string
     *
     * @ORM\Column(name="cv", type="text", nullable=true)
     */
    private $cv;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Users
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Users
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     *
     * @return Users
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     *
     * @return Users
     */
    public function setBirthdayAsDotSeparatedDMY($birthday)
    {
        $dt = new \DateTime($birthday);
        return $this->setBirthday($dt);
    }

    /**
     * Get birthday
     *
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Users
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set homeCity
     *
     * @param string $homeCity
     *
     * @return Users
     */
    public function setHomeCity($homeCity)
    {
        $this->homeCity = $homeCity;

        return $this;
    }

    /**
     * Get homeCity
     *
     * @return string
     */
    public function getHomeCity()
    {
        return $this->homeCity;
    }

    /**
     * Set homeStreet
     *
     * @param string $homeStreet
     *
     * @return Users
     */
    public function setHomeStreet($homeStreet)
    {
        $this->homeStreet = $homeStreet;

        return $this;
    }

    /**
     * Get homeStreet
     *
     * @return string
     */
    public function getHomeStreet()
    {
        return $this->homeStreet;
    }

    /**
     * Set homeZip
     *
     * @param string $homeZip
     *
     * @return Users
     */
    public function setHomeZip($homeZip)
    {
        $this->homeZip = $homeZip;

        return $this;
    }

    /**
     * Get homeZip
     *
     * @return string
     */
    public function getHomeZip()
    {
        return $this->homeZip;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Users
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set companyName
     *
     * @param string $companyName
     *
     * @return Users
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * Get companyName
     *
     * @return string
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * Set companyCity
     *
     * @param string $companyCity
     *
     * @return Users
     */
    public function setCompanyCity($companyCity)
    {
        $this->companyCity = $companyCity;

        return $this;
    }

    /**
     * Get companyCity
     *
     * @return string
     */
    public function getCompanyCity()
    {
        return $this->companyCity;
    }

    /**
     * Set companyStreet
     *
     * @param string $companyStreet
     *
     * @return Users
     */
    public function setCompanyStreet($companyStreet)
    {
        $this->companyStreet = $companyStreet;

        return $this;
    }

    /**
     * Get companyStreet
     *
     * @return string
     */
    public function getCompanyStreet()
    {
        return $this->companyStreet;
    }

    /**
     * Set jobPosition
     *
     * @param string $jobPosition
     *
     * @return Users
     */
    public function setJobPosition($jobPosition)
    {
        $this->jobPosition = $jobPosition;

        return $this;
    }

    /**
     * Get jobPosition
     *
     * @return string
     */
    public function getJobPosition()
    {
        return $this->jobPosition;
    }

    /**
     * Set cv
     *
     * @param string $cv
     *
     * @return Users
     */
    public function setCv($cv)
    {
        $this->cv = $cv;

        return $this;
    }

    /**
     * Get cv
     *
     * @return string
     */
    public function getCv()
    {
        return $this->cv;
    }
}
