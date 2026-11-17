<?php

namespace app\commands;

// Record the command's backoff without delaying the test process.
function sleep($seconds)
{
    \GithubProgressControllerTest::$delays[] = $seconds;
    return 0;
}
