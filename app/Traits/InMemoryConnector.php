<?php

namespace App\Traits;

use Mike42\Escpos\PrintConnectors\PrintConnector;

class InMemoryConnector implements PrintConnector
{
    /** @var string */
    protected $buffer = '';

    /**
     * Called when the object is destroyed.
     * We forward to finalize() so any buffered data is “sent.”
     */
    public function __destruct()
    {
        $this->finalize();
    }

    /**
     * Finish using this connector.
     * No real output device, so this is a no-op (or you could flush to log).
     */
    public function finalize()
    {
        // no-op, or you could e.g. error_log($this->buffer);
    }

    /**
     * Read data from the printer (not used here).
     *
     * @param int $len
     * @return string
     */
    public function read($len)
    {
        // nothing to read from a "printer"
        return '';
    }

    /**
     * Write data to our internal buffer.
     *
     * @param string $data
     * @return void
     */
    public function write($data)
    {
        $this->buffer .= $data;
    }

    /**
     * Retrieve the full ESC/POS payload.
     *
     * @return string
     */
    public function getBuffer(): string
    {
        return $this->buffer;
    }
}
