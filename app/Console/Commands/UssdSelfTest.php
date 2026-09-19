<?php

namespace App\Console\Commands;

use App\Ussd\Support\SelfTest;
use Illuminate\Console\Command;

class UssdSelfTest extends Command
{
    protected $signature   = 'ussd:selftest';

    protected $description = 'Walk a USSD session against this deployment and check every reply against the Nalo contract';

    public function handle(): int
    {
        $run = SelfTest::run();

        $this->line(SelfTest::report($run));

        return $run['passed'] ? self::SUCCESS : self::FAILURE;
    }
}
