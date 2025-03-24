<?php 

namespace App\Model; //déclaration du namespace "Model" dans le répertoire "App" 

class SearchDataBeers //création de la classe SearchDataBeers 
{   
    //définition des propriétés de la classe (attributs)
    private ?string $name = null; //attribut privé "name" renseignant la dénomination du produit étant une chaîne de caractères 
    private $type = null; //attribut privé "type" renseignant le type du produit 
    private ?string $color = null; //attribut privé "color" étant une chaîne de caractères et renseignant la couleur du produit 
    private ?bool $isPermanent = null; //attribut privé "isPermanent" étant un booléen et renseignant la gamme du produit 
    private ?int $category = null; //attribut privé "categpry" étant un entier et renseignant la catégorie du produit 

    // Getters and Setters : méthodes publiques permettant d'accéder (accesseurs) aux attributs privés de l'objet et de les modifier (mutateur)
    public function getName(): ?string // déclaration de l'accesseur getName 
    {
        return $this->name; //qui retourne la valeur de l'attribut privé "name"
    }

    public function setName(?string $name): self //déclaration du mutateur setName 
    {
        $this->name = $name; //l'attribut privé "name" est mis à à jour ave cla valeur du paramètre $name passé à la méthode
        return $this; //retourne l'objet courant 
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType( $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;
        return $this;
    }

    public function getIsPermanent(): ?bool
    {
        return $this->isPermanent;
    }

    public function setIsPermanent(?bool $isPermanent): self
    {
        $this->isPermanent = $isPermanent;
        return $this;
    }

    public function getCategory(): int
    {
        return $this->category;
    }

    public function setCategory($category): self
    {
        $this->category = $category;
        return $this;
    }
}
