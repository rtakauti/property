<?php
declare(strict_types=1);


namespace Rtakauti\Support;


use Generator;

class Connection
{
    private string $etag;
    private int $block;
    private int $port;
    private string $domain;
    private string $endpoint;

    public function __construct(int $block = 1)
    {
        $this->etag = file_exists(__DIR__ . '/../assets/.etag') ? file_get_contents(__DIR__ . '/../assets/.etag') : '';
        $this->block = 1024 * $block;
    }

    /**
     * @return Generator
     */
    public function getGenerator(): Generator
    {
        if (!$handler = fsockopen($this->domain, $this->port, $errno, $error, 30)) {
            die("$error ($errno)\n");
        }
        $out = "GET $this->endpoint HTTP/1.1\r\n";
        $out .= "Host: $this->domain\r\n";
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
     * @param int $port
     */
    public function setPort(int $port = 80): void
    {
        $this->port = $port;
    }

    /**
     * @param string $domain
     */
    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }

    /**
     * @param string $endpoint
     */
    public function setEndpoint(string $endpoint): void
    {
        $this->endpoint = $endpoint;
    }
}