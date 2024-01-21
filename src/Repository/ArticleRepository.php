<?php

namespace App\Repository;

use App\DbConnectors\Factories\PdoFactoryI;
use App\Model\Synchronisation\ArticleEntity;
use App\Model\Synchronisation\ArticleRepositoryI;
use App\Shared\Infrastructure\Pdo\Dbal\Contract\PdoManager;
use Error;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use PDO;

class ArticleRepository implements ArticleRepositoryI
{
    const LENGTH_ERROR_MESSAGE = [
        "SQLSTATE[22001]: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]String or binary data would be truncated.",
        "SQLSTATE[22001]: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]Los datos de cadena o binarios se truncarían.",
        "SQLSTATE[22001]: [Microsoft][ODBC Driver 18 for SQL Server][SQL Server]String or binary data would be truncated.",
        "SQLSTATE[22001]: [Microsoft][ODBC Driver 18 for SQL Server][SQL Server]Los datos de cadena o binarios se truncarían."
    ];
    private PdoFactoryI $sqlPdoFactory;
    private PDO $connection;
    private int $length = 0;
    // private float $tenPercent = 0;
    private float $currentPercentage = 0;
    // private float $currentNumberPercentage = 0;

    public function __construct(
        private PdoManager $sqlServerPdoManager,
        ContainerBagInterface $params
    ) {
        $this->params = $params;
        $this->from = 'art_foto';
        $this->sqlServerPdoManager = $sqlServerPdoManager;
        $this->connection = $this->sqlServerPdoManager->getConnection();
    }

    // public function getPdoConnection(): PdoConnector
    // {
    //     $authenticationMethod = $this->params->get("sql.authentication.method");

    //     if ($authenticationMethod === 'nm') {
    //         return $this->sqlPdoFactory->create('sqlServer');
    //     }

    //     return $this->sqlPdoFactory->create();
    // }

    public function save(iterable $entityList): void
    {
        // ini_set('max_execution_time', '1500');

        // $this->length = count($entityList);

        // if ($length <= 10) {
        // }

        // $this->tenPercent = ($this->length * 10) / 100;

        $this->clearJsonLogFile();

        $sql = "UPDATE " . $this->from . " ";
        $sql .= "SET imagen = :imagePath ";
        $sql .= "WHERE articulo = :code";

        $query = $this->connection->prepare($sql);

        $code = null;
        $imagePath = null;

        $query->bindParam(":code", $code, PDO::PARAM_STR);
        $query->bindParam(":imagePath", $imagePath, PDO::PARAM_STR);

        foreach ($entityList as $key => $article) {
            // $currentKey = ($key + 1);

            // $this->currentPercentage = $this->calculateCurrentPertange($currentKey);

            if ($this->existRow($article)) {
                $code = $article->getCode();
                $imagePath = $article->getEurowinImage();
                try {
                    $query->execute();
                } catch (Exception $e) {
                    $message = $e->getMessage();

                    if (in_array($message, $this::LENGTH_ERROR_MESSAGE)) {
                        $imagePath = $article->getEurowinImage();

                        throw new Error(
                            json_encode(
                                [
                                    "Mensage" => "La longitud de la ruta de la imagen supera la longitud permitida (90)",
                                    "Articulo" => $code,
                                    "Ruta" => $imagePath,
                                    "Longitud" => strlen($imagePath)
                                ]
                            ),
                            JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
                        );
                    }

                    throw new Error($message);
                }
                $data = $article->toArray();
                $data['updated'] = true;
            } else {
                $data = $article->toArray();
                $data['updated'] = false;
                $code = $article->getCode();
                $data['error'] = "The code $code does not exist";
            }

            $this->addToJsonLog($data);
        }
    }

    // private function calculateCurrentPertange(int $currentKey): float
    // {
    //     return ($currentKey * 100) / $this->length;
    // }

    public function getCurrentPercentage(): float
    {
        return $this->currentPercentage;
    }

    public function setCurrentPercentage(float $currentPercentage): void
    {
        $this->currentPercentage = $currentPercentage;
    }

    public function getAll(): array
    {
        $imageLogFile = $this->params->get("images.log.file");
        $imageLog = file_get_contents($imageLogFile);
        return json_decode($imageLog, true);
    }

    private function addToJsonLog(array $data): void
    {
        $imageLogFile = $this->params->get("images.log.file");
        $imageLog = file_get_contents($imageLogFile);
        $tempArray = json_decode($imageLog, true);
        array_push($tempArray, $data);
        $jsonData = json_encode($tempArray, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        file_put_contents($imageLogFile, $jsonData);
    }

    private function clearJsonLogFile(): void
    {
        $imageLogFile = $this->params->get("images.log.file");
        $tempArray = [];
        $jsonData = json_encode($tempArray, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        file_put_contents($imageLogFile, $jsonData);
    }

    private function existRow(ArticleEntity $articleEntity): bool
    {
        $sql = "SELECT TOP 1 * FROM " . $this->from . " ";
        $sql .= "WHERE articulo=:code";

        $query = $this->connection->prepare($sql);

        $code = $articleEntity->getCode();

        $query->bindValue(":code", $code, PDO::PARAM_INT);

        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_COLUMN, 0);

        return !empty($result);
    }
}
