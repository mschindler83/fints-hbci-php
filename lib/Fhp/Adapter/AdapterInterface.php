<?php

namespace Fhp\Adapter;

use Fhp\Message\AbstractMessage;

/**
 * Interface AdapterInterface
 * @package Fhp\Adapter
 */
interface AdapterInterface
{
    /**
     * @param AbstractMessage $message
     * @return string
     */
    public function send(AbstractMessage $message, $inputCharset = 'ISO-8859-1', $outputCharset = 'UTF-8');
}
