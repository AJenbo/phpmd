<?php

namespace PHPMD\Console;

class NullOutput extends Output
{
    /**
     * @inheritDoc
     */
    protected function doWrite($message): void
    {
        // do nothing
    }
}
