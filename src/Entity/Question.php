<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuestionRepository")
 */
class Question
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Survey", inversedBy="Questions", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $Survey;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\QuestionType")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Type;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\QuestionOption", mappedBy="Question", orphanRemoval=true)
     */
    private $questionOptions;

    /**
     * @ORM\Column(type="boolean")
     */
    private $Required;

    public function __construct()
    {
        $this->questionOptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): self
    {
        $this->Name = $Name;

        return $this;
    }

    public function getSurvey(): ?Survey
    {
        return $this->Survey;
    }

    public function setSurvey(?Survey $Survey): self
    {
        $this->Survey = $Survey;

        return $this;
    }

    public function getType(): ?QuestionType
    {
        return $this->Type;
    }

    public function setType(?QuestionType $Type): self
    {
        $this->Type = $Type;

        return $this;
    }

    public function __toString()
    {
        return $this->getName() . " (" . $this->getSurvey() . ")";
    }

    /**
     * @return Collection|QuestionOption[]
     */
    public function getQuestionOptions(): Collection
    {
        return $this->questionOptions;
    }

    public function addQuestionOption(QuestionOption $questionOption): self
    {
        if (!$this->questionOptions->contains($questionOption)) {
            $this->questionOptions[] = $questionOption;
            $questionOption->setQuestion($this);
        }

        return $this;
    }

    public function removeQuestionOption(QuestionOption $questionOption): self
    {
        if ($this->questionOptions->contains($questionOption)) {
            $this->questionOptions->removeElement($questionOption);
            // set the owning side to null (unless already changed)
            if ($questionOption->getQuestion() === $this) {
                $questionOption->setQuestion(null);
            }
        }

        return $this;
    }

    public function getRequired(): ?bool
    {
        return $this->Required;
    }

    public function setRequired(bool $Required): self
    {
        $this->Required = $Required;

        return $this;
    }
}
