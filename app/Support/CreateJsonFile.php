<?php


namespace Rtakauti\Support;


use Exception;
use Generator;
use JsonException;
use RuntimeException;

class CreateJsonFile
{
    private string $fileName;
    private string $etag;
    private int $block;
    private string $delimiter;
    private string $newDelimiter;
    private Generator $generator;


    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
        $this->etag = '';
        $this->delimiter = '}},{';
        $this->newDelimiter = "}},\n{";
    }

    public function create(): void
    {
        try {
            $this->analyseHeader($this->generator);
            $this->createStaging($this->generator);
            $this->createJsonFile();
            unlink(__DIR__ . '/../assets/staging.json');
        }catch (RuntimeException $runtimeException){
            echo $runtimeException->getMessage();
        } catch (Exception $exception) {
            die($exception->getMessage());
        }
    }

    /**
     * @param Generator $lines
     * @return void
     */
    private function analyseHeader(Generator $lines): void
    {
        foreach ($lines as $line) {
            if (preg_match('/(304)/', $line) && file_exists(__DIR__ . '/../assets/'.$this->fileName)) {
                throw new RuntimeException('Not Modified');
            }
            if ("\r\n" === $line) {
                return;
            }
            try {
                $this->createEtagFile($line);
            } catch (Exception $exception) {
                die($exception->getMessage());
            }
        }
        $lines->next();
    }

    /**
     * @param $line
     * @return void
     */
    private function createEtagFile($line): void
    {
        if (preg_match('/(ETag:)\s"(.*)"/', $line, $matches)) {
            if (!$handler = fopen(__DIR__ . '/../assets/.etag', 'wb')) {
                throw new RuntimeException('Could not open file ".etag"');
            }
            fwrite($handler, $matches[2]);
            fclose($handler);
        }
    }

    /**
     * @param Generator $lines
     * @return void
     */
    private function createStaging(Generator $lines): void
    {
        if (!$handler = fopen(__DIR__ . '/../assets/staging.json', 'wb')) {
            throw new RuntimeException('Could not open file "staging.json"');
        }
        for (; $lines->valid(); $lines->next()) {
            fwrite($handler, trim(str_replace($this->delimiter, $this->newDelimiter, $lines->current()), "\r\n"));
        }
        fclose($handler);
    }

    private function createJsonFile(): void
    {
        if (!$handler = fopen(__DIR__ . '/../assets/staging.json', 'rb')) {
            throw new RuntimeException('Could not open file "staging.json"');
        }
        if (!$handler1 = fopen(__DIR__ . '/../assets/' . $this->fileName, 'wb')) {
            throw new RuntimeException('Could not open file "' . $this->fileName . '"');
        }
        while (!feof($handler)) {
            if (!is_string($analyse = fgets($handler))) {
                continue;
            }
            if (preg_match("/$this->delimiter/", $analyse)) {
                $analyse = str_replace($this->delimiter, $this->newDelimiter, $analyse);
            }
            fwrite($handler1, $analyse);
        }
        fclose($handler1);
        fclose($handler);
    }


    /**
     * @param Generator $generator
     */
    public function setGenerator(Generator $generator): void
    {
        $this->generator = $generator;
    }


    /**
     * @return Generator
     * @deprecated
     */
    public function getProperty(): Generator
    {
        if (!$handler = fopen($this->fileName, 'rb')) {
            throw new RuntimeException('Could not open file "' . $this->fileName . '"');
        }
        while (!feof($handler)) {
            if ($line = trim(fgets($handler), " ,\r\n[]")) {
                yield $line;
            }
        }
        fclose($handler);
    }

    /**
     * @throws JsonException
     * @deprecated
     */
    public function formatJson(): void
    {
        $file = file_get_contents('property.json');
        $handler = fopen('property.json', 'wb');
        $items = json_decode($file, true, 512, JSON_THROW_ON_ERROR);
        fwrite($handler, "[");
        for ($i = 0; $i < count($items) - 1; $i++) {
            fwrite($handler, json_encode($items[$i], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ",\n");
        }
        fwrite($handler, json_encode($items[$i], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "]");
        fclose($handler);
    }

    /**
     * @return string
     */
    public function getEtag(): string
    {
        return $this->etag;
    }

    /**
     * @return float|int
     */
    public function getBlock()
    {
        return $this->block;
    }
}
