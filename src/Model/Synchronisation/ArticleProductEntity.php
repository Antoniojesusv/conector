<?php

namespace App\Model\Synchronisation;

use Exception;

class ArticleProductEntity
{
    private string $code;
    private string $name;
    private string $image;
    private string $low;
    private string $internet;
    private string $artCanon;
    private string $pvp;
    private string $rate;
    private string $final;
    private int $productId;
    private ?string $S01Value;
    private array $shopperGroups;

    public function __construct(
        string $code,
        string $name,
        string $image,
        string $low,
        string $internet,
        string $artCanon,
        string $pvp,
        string $rate,
        string $final,
        int $productId = 0,
        array $shopperGroups = [],
        ?string $S01Value = null
    ) {
        $this->setCode($code);
        $this->setName($name);
        $this->setImage($image);
        $this->setLow($low);
        $this->setInternet($internet);
        $this->setArtCanon($artCanon);
        $this->setPvp($pvp);
        $this->setRate($rate);
        $this->setFinal($final);
        $this->setProductId($productId);
        $this->setShopperGroups($shopperGroups);
        $this->setS01Value($S01Value);
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        if (empty($code)) {
            throw new Exception('The code cannot be empty');
        }
        
        $this->code = trim($code);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        if (empty($name)) {
            throw new Exception('The name cannot be empty');
        }
        
        $this->name = $name;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): void
    {
        if (empty($image)) {
            throw new Exception('The image cannot be empty');
        }

        $this->image = $image;
    }

    public function getLow(): string
    {
        return $this->low;
    }

    public function setLow(string $low): void
    {
        if (empty($low) && !is_numeric($low)) {
            throw new Exception('The low cannot be empty');
        }
        
        $this->low = $low;
    }

    public function getInternet(): string
    {
        return $this->internet;
    }

    public function setInternet(string $internet): void
    {
        if (empty($internet) && !is_numeric($internet)) {
            throw new Exception('The internet cannot be empty');
        }
        
        $this->internet = $internet;
    }

    public function getArtCanon(): string
    {
        return $this->artCanon;
    }

    public function setArtCanon(string $artCanon): void
    {
        if (empty($artCanon) && !is_numeric($artCanon)) {
            throw new Exception('The artCanon cannot be empty');
        }
        
        $this->artCanon = $artCanon;
    }

    public function getPvp(): string
    {
        return $this->pvp;
    }

    public function setPvp(string $pvp): void
    {
        if (empty($pvp)) {
            throw new Exception('The pvp cannot be empty');
        }
        
        $this->pvp = $pvp;
    }

    public function getRate(): string
    {
        return $this->rate;
    }

    public function setRate(string $rate): void
    {
        if (empty($rate)) {
            throw new Exception('The rate cannot be empty');
        }
        
        $this->rate = $rate;
    }

    public function getFinal(): string
    {
        return $this->final;
    }

    public function setFinal(string $final): void
    {
        if (empty($final)) {
            throw new Exception('The final cannot be empty');
        }
        
        $this->final = $final;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function setProductId(?int $productId): void
    {
        $this->productId = $productId;
    }

    public function getShopperGroups(): array
    {
        return $this->shopperGroups;
    }

    public function setShopperGroups(array $shopperGroups): void
    {
        $this->shopperGroups = $shopperGroups;
    }
    
    public function getS01Value(): ?string
    {
        return $this->S01Value;
    }

    public function setS01Value(?string $S01Value): void
    {
        $this->S01Value = $S01Value;
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'image' => $this->image,
            'low' => $this->low,
            'internet' => $this->internet,
            'artCanon' => $this->artCanon,
            'pvp' => $this->pvp,
            'rate' => $this->rate,
            'final' => $this->final,
            'productId' => $this->productId,
            'S01Value' => $this->S01Value
        ];
    }

    public function __toString(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}
