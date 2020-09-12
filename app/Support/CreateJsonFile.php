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

    public function __construct(string $fileName, int $block = 1)
    {
        $this->fileName = $fileName;
        $this->block = 1024 * $block;
        $this->etag = '';
        $this->delimiter = '}},{';
        $this->newDelimiter = "}},\n{";
        if (file_exists('.etag')) {
            $this->etag .= file_get_contents('.etag');
        }
    }

    public function save(): void
    {
        $lines = $this->getJson();
        try {
            $this->analyseHeader($lines);
            $this->createStaging($lines);
            $this->createJsonFile();
            unlink('staging.json');
        }catch (Exception $exception){
            die($exception->getMessage());
        }
    }

    /**
     * @param Generator $lines
     * @return void
     */
    private function createStaging(Generator $lines): void
    {
        if (!$handler = fopen('staging.json', 'wb')) {
            throw new RuntimeException('Could not open file "staging.json"');
        }
        for (; $lines->valid(); $lines->next()) {
            fwrite($handler, trim(str_replace($this->delimiter, $this->newDelimiter, $lines->current()), "\r\n"));
        }
        fclose($handler);
    }


    private function createJsonFile(): void
    {
        if (!$handler = fopen('staging.json', 'rb')) {
            throw new RuntimeException('Could not open file "staging.json"');
        }
        if (!$handler1 = fopen($this->fileName, 'wb')) {
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
     * @return Generator
     */
    private function getJson(): Generator
    {
        $domain = 'grupozap-code-challenge.s3-website-us-east-1.amazonaws.com';
        $endpoint = '/sources/source-2.json';
        if (!$handler = fsockopen($domain, 80, $errno, $error, 30)) {
            die("$error ($errno)\n");
        }
        $out = "GET $endpoint HTTP/1.1\r\n";
        $out .= "Host: $domain\r\n";
        $out .= "Accept: application/json; charset=utf-8\r\n";
        $out .= "If-None-Match: \"$this->etag\"\r\n";
        $out .= "Connection: keep-alive\r\n\r\n";
        fwrite($handler, $out);
        while (!feof($handler)) {
            yield fgets($handler, $this->block);
        }
        fclose($handler);
    }


    /**
     * @param Generator $lines
     * @return void
     */
    private function analyseHeader(Generator $lines): void
    {
        foreach ($lines as $line) {
            if (preg_match('/(304)/', $line) && file_exists($this->fileName)) {
                die('Not Modified');
            }
            if ("\r\n" === $line) {
                return;
            }
            try {
                $this->createEtagFile($line);
            }catch (Exception $exception){
                echo $exception->getMessage();
                return;
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
            if (!$handler = fopen('.etag', 'wb')) {
                throw new RuntimeException('Could not open file ".etag"');
            }
            fwrite($handler, $matches[2]);
            fclose($handler);
        }
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
}
