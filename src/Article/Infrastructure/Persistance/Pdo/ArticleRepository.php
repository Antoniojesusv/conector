<?php
namespace App\Article\Infrastructure\Persistance\Pdo;

use App\Article\Domain\ArticleLog;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;


class ArticleRepository
{

    public function __construct(
        private ContainerBagInterface $params
    ) {
        $this->params = $params;
    }

    public function save(ArticleLog $articleLogEntity): void
    {
        $articleLogFile = $this->params->get("articles.log.file");
        $articleLog = file_get_contents($articleLogFile);
        $tempArray = json_decode($articleLog, true);
        array_push($tempArray, $articleLogEntity->toArray());
        $jsonData = json_encode($tempArray, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        file_put_contents($articleLogFile, $jsonData);
    }

    public function getAll(): array
    {
        $articleLogFile = $this->params->get("articles.log.file");
        $articleLog = file_get_contents($articleLogFile);
        return json_decode($articleLog, true);
    }

    public function clearJsonLogFile(): void
    {
        $articleLogFile = $this->params->get("articles.log.file");
        $tempArray = [];
        $jsonData = json_encode($tempArray, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        file_put_contents($articleLogFile, $jsonData);
    }
}