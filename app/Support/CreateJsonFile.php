<?php
declare(strict_types=1);


namespace Rtakauti\Support;


use Exception;
use Generator;
use RuntimeException;

class CreateJsonFile
{
    private string $fileName;
    private string $delimiter;
    private string $newDelimiter;
    private Generator $generator;


    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
        $this->delimiter = '}},{';
        $this->newDelimiter = '}},' . PHP_EOL . '{';
    }

    /**
     * @param Generator $generator
     */
    public function setGenerator(Generator $generator): void
    {
        $this->generator = $generator;
    }

    public function create(): void
    {
        try {
            $this->analyseHeader($this->generator);
            $this->createStaging($this->generator);
            $this->createJsonFile();
            unlink(__DIR__ . '/../assets/staging.json');
        } catch (RuntimeException $runtimeException) {
            return;
        }
    }

    /**
     * @param Generator $lines
     * @return void
     */
    private function analyseHeader(Generator $lines): void
    {
        foreach ($lines as $line) {
            if (preg_match('/(304)/', $line) && file_exists(__DIR__ . '/../assets/' . $this->fileName)) {
                throw new RuntimeException('Not Modified');
            }
            if ("\r\n" === $line) {
                return;
            }
            $this->createEtagFile($line);
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
            $handler = fopen(__DIR__ . '/../assets/.etag', 'wb');
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
        $handler = fopen(__DIR__ . '/../assets/staging.json', 'wb');
        for (; $lines->valid(); $lines->next()) {
            fwrite($handler, trim(str_replace($this->delimiter, $this->newDelimiter, $lines->current()), "\r\n"));
        }
        fclose($handler);
    }

    private function createJsonFile(): void
    {
        $handler = fopen(__DIR__ . '/../assets/staging.json', 'rb');
        $handler1 = fopen(__DIR__ . '/../assets/' . $this->fileName, 'wb');
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
}
