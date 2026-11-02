<?php

use app\components\github\GithubProgress;
use Github\Exception\ApiLimitExceedException;
use Github\Exception\RuntimeException;
use GuzzleHttp\Psr7\Response;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use yii\caching\ArrayCache;
use yii\console\Application;

require_once __DIR__ . '/../_support/GithubProgressHttpDiscovery.php';
require_once __DIR__ . '/../_support/github-progress-sleep.php';

class GithubProgressControllerTest extends TestCase
{
    public static $delays = [];

    private $previousApp;
    private $previousAliases;
    private $previousStrategies;
    private $previousRepositories;
    private $repositoriesProperty;
    private $dataPath;
    private $outcomes = [];
    private $calls = [];
    private $app;

    protected function setUp(): void
    {
        $this->previousApp = Yii::$app;
        $this->previousAliases = Yii::$aliases;
        Yii::setAlias('@app', dirname(__DIR__, 2));
        $this->previousStrategies = [];
        foreach (HttpClientDiscovery::getStrategies() as $strategy) {
            $this->previousStrategies[] = $strategy;
        }
        $this->repositoriesProperty = new ReflectionProperty(GithubProgress::class, 'allResponseRepositories');
        $this->repositoriesProperty->setAccessible(true);
        $this->previousRepositories = $this->repositoriesProperty->getValue();
        $this->repositoriesProperty->setValue(null, null);
        self::$delays = [];

        $client = $this->createMock(HttpClient::class);
        $client->method('sendRequest')->willReturnCallback(function (RequestInterface $request) {
            return $this->respond($request);
        });
        GithubProgressHttpDiscovery::$client = $client;
        HttpClientDiscovery::prependStrategy(GithubProgressHttpDiscovery::class);

        $this->app = new Application([
            'id' => 'github-progress-test',
            'basePath' => dirname(__DIR__, 2),
            'controllerNamespace' => 'app\\commands',
            'components' => ['cache' => ['class' => ArrayCache::class]],
        ]);
        $this->dataPath = sys_get_temp_dir() . '/github-progress-' . uniqid('', true);
        mkdir($this->dataPath);
        file_put_contents($this->dataPath . '/github.token', 'test-token');
        Yii::setAlias('@app/data', $this->dataPath);

        foreach (GithubProgress::VERSIONS as $version) {
            $this->app->cache->set('github_progress_data_' . $version, ['stale' => $version]);
        }
    }

    protected function tearDown(): void
    {
        unlink($this->dataPath . '/github.token');
        rmdir($this->dataPath);
        Yii::$aliases = $this->previousAliases;
        Yii::$app = $this->previousApp;
        HttpClientDiscovery::setStrategies($this->previousStrategies);
        GithubProgressHttpDiscovery::$client = null;
        $this->repositoriesProperty->setValue(null, $this->previousRepositories);
    }

    /** @dataProvider temporaryResponses */
    public function testRecoversFromTemporaryHttpFailure(int $status, string $body): void
    {
        $this->outcomes['1.1'] = [new Response($status, ['Content-Type' => 'text/html'], $body)];

        self::assertSame(0, $this->app->runAction('github-progress'));
        self::assertSame(['1.1', '1.1', '2.0', '3.0'], $this->calls);
        self::assertSame([90], self::$delays);
        foreach (GithubProgress::VERSIONS as $version) {
            self::assertSame([], $this->app->cache->get('github_progress_data_' . $version));
        }
    }

    public function temporaryResponses(): array
    {
        return [
            'observed HTML' => [502, "<html>Unicorn! We couldn't respond to your request in time.</html>"],
            'changed response wording' => [504, '<html>Gateway timeout</html>'],
            'unavailable' => [503, 'Service temporarily unavailable'],
            'server error' => [500, 'Internal server error'],
            'legacy response' => [502, 'This may be the result of a timeout, or it could be a GitHub bug.'],
        ];
    }

    /** @dataProvider timeoutsWithoutStatus */
    public function testRecoversFromTimeoutWithoutHttpStatus(string $body): void
    {
        $this->outcomes['1.1'] = [new RuntimeException($body)];

        self::assertSame(0, $this->app->runAction('github-progress'));
        self::assertSame(['1.1', '1.1', '2.0', '3.0'], $this->calls);
        self::assertSame([90], self::$delays);
        foreach (GithubProgress::VERSIONS as $version) {
            self::assertSame([], $this->app->cache->get('github_progress_data_' . $version));
        }
    }

    /** @dataProvider timeoutsWithoutStatus */
    public function testExhaustedTimeoutWithoutHttpStatusPropagatesFailure(string $body): void
    {
        $failure = new RuntimeException($body);
        $this->outcomes['1.1'] = array_fill(0, 3, $failure);

        try {
            $this->app->runAction('github-progress');
            self::fail('Exhausted timeouts without an HTTP status must propagate the final exception.');
        } catch (RuntimeException $exception) {
            self::assertSame($failure, $exception);
        }

        self::assertSame(['1.1', '1.1', '1.1'], $this->calls);
        self::assertSame([90, 90], self::$delays);
        foreach (GithubProgress::VERSIONS as $version) {
            self::assertSame(['stale' => $version], $this->app->cache->get('github_progress_data_' . $version));
        }
    }

    public function timeoutsWithoutStatus(): array
    {
        return [
            'observed HTML' => ["<html>Unicorn! We couldn't respond to your request in time.</html>"],
            'legacy response' => ['This may be the result of a timeout, or it could be a GitHub bug.'],
        ];
    }

    /** @dataProvider exhaustedResponses */
    public function testExhaustionPropagatesFailureAndPreservesCachedData(bool $hasCache, string $body): void
    {
        if (!$hasCache) {
            $this->app->cache->delete('github_progress_data_2.0');
        }
        $this->outcomes['2.0'] = array_fill(0, 3, new Response(502, ['Content-Type' => 'text/html'], $body));

        try {
            $this->app->runAction('github-progress');
            self::fail('Exhausted retries must propagate an exception to the console application.');
        } catch (RuntimeException $exception) {
            self::assertSame(502, $exception->getCode());
            self::assertSame($body, $exception->getMessage());
        }

        self::assertSame(['1.1', '2.0', '2.0', '2.0'], $this->calls);
        self::assertSame([90, 90], self::$delays);
        self::assertSame([], $this->app->cache->get('github_progress_data_1.1'));
        self::assertSame($hasCache ? ['stale' => '2.0'] : false, $this->app->cache->get('github_progress_data_2.0'));
        self::assertSame(['stale' => '3.0'], $this->app->cache->get('github_progress_data_3.0'));
    }

    public function exhaustedResponses(): array
    {
        $html = "<html>Unicorn! We couldn't respond to your request in time.</html>";
        $legacy = 'This may be the result of a timeout, or it could be a GitHub bug.';

        return [
            'HTML timeout with existing cache' => [true, $html],
            'HTML timeout with missing cache' => [false, $html],
            'legacy timeout with existing cache' => [true, $legacy],
            'legacy timeout with missing cache' => [false, $legacy],
        ];
    }

    /** @dataProvider permanentResponses */
    public function testPermanentFailureIsNotRetried(int $status, string $body, array $headers): void
    {
        $this->outcomes['1.1'] = [new Response($status, $headers, $body)];

        try {
            $this->app->runAction('github-progress');
            self::fail('Permanent failures must propagate immediately.');
        } catch (RuntimeException $exception) {
            self::assertNotSame('', $exception->getMessage());
        }

        self::assertSame(['1.1'], $this->calls);
        self::assertSame([], self::$delays);
        foreach (GithubProgress::VERSIONS as $version) {
            self::assertSame(['stale' => $version], $this->app->cache->get('github_progress_data_' . $version));
        }
    }

    public function testRateLimitQuotaRaisesApiLimitExceedException(): void
    {
        // The GitHub API v3 rate limit answer (403 JSON 'API rate limit
        // exceeded' with Remaining/Limit/Reset headers) must reach the
        // controller as the library's ApiLimitExceedException and be treated
        // as permanent: no retry, no delay, existing caches preserved. This
        // covers the exception-code-zero quota path (not only the plain HTTP
        // 403 fixture below).
        $this->outcomes['1.1'] = [new Response(403, [
            'Content-Type' => 'application/json',
            'X-RateLimit-Remaining' => '0',
            'X-RateLimit-Limit' => '5000',
            'X-RateLimit-Reset' => '1234567890',
        ], '{"message":"API rate limit exceeded for 192.0.2.1. Limit 5000, Used 5000, Reset 1234567890","documentation_url":"https://docs.github.com/rest"}')];

        try {
            $this->app->runAction('github-progress');
            self::fail('Rate limit quota must propagate immediately.');
        } catch (ApiLimitExceedException $exception) {
            self::assertSame(5000, $exception->getLimit());
            self::assertSame(1234567890, $exception->getResetTime());
            self::assertSame(0, $exception->getCode());
        }

        self::assertSame(['1.1'], $this->calls);
        self::assertSame([], self::$delays);
        foreach (GithubProgress::VERSIONS as $version) {
            self::assertSame(['stale' => $version], $this->app->cache->get('github_progress_data_' . $version));
        }
    }

    public function permanentResponses(): array
    {
        return [
            'credentials' => [401, '{"message":"Bad credentials"}', ['Content-Type' => 'application/json']],
            'permission' => [403, 'Forbidden', []],
            'missing repository' => [404, 'Not Found', []],
            'invalid query' => [422, 'Invalid query', []],
            'unsupported operation' => [501, 'Not implemented', []],
            'misleading timeout text' => [401, "We couldn't respond to your request in time.", []],
            'rate limit' => [403, 'Rate limit', ['X-RateLimit-Remaining' => '0']],
        ];
    }

    private function respond(RequestInterface $request): Response
    {
        if ($request->getUri()->getPath() === '/orgs/yiisoft/repos') {
            $repositories = [];
            if (strpos($request->getUri()->getQuery(), 'page=1&') === 0) {
                foreach (GithubProgress::VERSIONS as $version) {
                    $repositories[] = [
                        'archived' => false,
                        'full_name' => 'yiisoft/yii' . (int) $version,
                        'topics' => ['yii' . (int) $version],
                    ];
                }
            }
            return new Response(200, ['Content-Type' => 'application/json'], json_encode($repositories));
        }

        self::assertSame('/graphql', $request->getUri()->getPath());
        $body = (string) $request->getBody();
        foreach (GithubProgress::VERSIONS as $version) {
            if (strpos($body, 'yii' . (int) $version) !== false) {
                $this->calls[] = $version;
                if (!empty($this->outcomes[$version])) {
                    $outcome = array_shift($this->outcomes[$version]);
                    if ($outcome instanceof RuntimeException) {
                        throw $outcome;
                    }
                    return $outcome;
                }
                return new Response(200, ['Content-Type' => 'application/json'], '{"data":[]}');
            }
        }

        throw new LogicException('Unexpected GitHub query.');
    }
}
