<?php


namespace Rtakauti\Support;


use Generator;
use RuntimeException;

class SaveJsonFile
{
    private  $fileName;
    private  $etag;
    private  $block;

    public function __construct(string $fileName, int $block = 1)
    {
        $this->fileName = $fileName;
        $this->block = $block * 1024;
        $this->etag = '';
        if (file_exists('.etag'))
            $this->etag .= file_get_contents('.etag');
    }

    /**
     */
    public function save(): void
    {
        $lines = $this->getJson();
        if (304 === $this->analyseHeader($lines)) {
            echo 'Not Modified', PHP_EOL;
            return;
        }
        $lines->next();
        if (!$handler = fopen('staging.json', 'w'))
            throw new RuntimeException('Couldn\'t open file "staging.json"');
        for (; $lines->valid(); $lines->next())
            fwrite($handler, trim(str_replace('}},{', "}},\n{", $lines->current()), "\r\n"));
        fclose($handler);
        if (!$handler = fopen('staging.json', 'r'))
            throw new RuntimeException('Couldn\'t open file "staging.json"');
        if (!$handler1 = fopen($this->fileName, 'w'))
            throw new RuntimeException('Couldn\'t open file "' . $this->fileName . '"');
        while (!feof($handler)) {
            if (!is_string($analyse = fgets($handler))) continue;
            if (preg_match('/}},{/', $analyse))
                $analyse = str_replace('}},{', "}},\n{", $analyse);
            fwrite($handler1, $analyse);
        }
        fclose($handler1);
        fclose($handler);
        unlink('staging.json');
    }

    /**
     * @return Generator
     */
    private function getJson(): Generator
    {
        $domain = 'grupozap-code-challenge.s3-website-us-east-1.amazonaws.com';
        $endpoint = '/sources/source-2.json';
        if (!$handler = fsockopen($domain, 80, $errno, $error, 30))
            die("$error ($errno)\n");
        $out = "GET $endpoint HTTP/1.1\r\n";
        $out .= "Host: $domain\r\n";
        $out .= "Accept: application/json; charset=utf-8\r\n";
        $out .= "If-None-Match: \"$this->etag\"\r\n";
        $out .= "Connection: keep-alive\r\n\r\n";
        fwrite($handler, $out);
        while (!feof($handler)) yield fgets($handler, $this->block);
        fclose($handler);
    }


    /**
     * @param Generator $lines
     * @return int
     */
    private function analyseHeader(Generator $lines): int
    {
        foreach ($lines as $line) {
            if (preg_match('/(304)/', $line)) return 304;
            if ("\r\n" == $line) return 0;
            $this->createEtagFile($line);
        }
        return 0;
    }

    /**
     * @param $line
     * @return void
     */
    private function createEtagFile($line): void
    {
        if (preg_match('/(ETag:)\s"(.*)"/', $line, $matches)) {
            if (!$handler = fopen('.etag', 'w'))
                throw new RuntimeException('Couldn\'t open file ".etag"');
            fwrite($handler, $matches[2]);
            fclose($handler);
        }
    }

    /**
     * @deprecated
     * @return Generator
     */
    public function getProperty(): Generator
    {
        if (!$handler = fopen($this->fileName, 'r'))
            throw new RuntimeException('Couldn\'t open file "' . $this->fileName . '"');
        while (!feof($handler))
            if ($line = trim(fgets($handler), " ,\r\n[]"))
                yield $line;
        fclose($handler);
    }

    /**
     * @deprecated
     */
    public function formatJson(): void
    {
        $file = file_get_contents('property.json');
        $handler = fopen('property.json', 'w');
        $items = json_decode($file, true);
        fwrite($handler, "[");
        for ($i = 0; $i < count($items) - 1; $i++)
            fwrite($handler, json_encode($items[$i], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ",\n");
        fwrite($handler, json_encode($items[$i], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "]");
        fclose($handler);
    }
}
