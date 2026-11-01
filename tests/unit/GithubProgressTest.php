<?php

use Github\Client;
use Github\HttpClient\Builder;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Client\ClientInterface;

/**
 * Regression tests for the knplabs/github-api 2.x -> 3.x migration used by
 * app\components\github\GithubProgress and the (untouched by this PR, see #488)
 * app\commands\GithubProgressController.
 *
 * These tests do not hit the network; they assert on the public API the app
 * relies on, which is what actually changed between major versions.
 */
class GithubProgressTest extends \Codeception\Test\Unit
{
    public function testRepositoryEnumerationSurvivesMiddlewareAndReachesGraphql(): void
    {
        // A nonempty repo page must survive github-api 3.x's plugin chain
        // (GithubExceptionThrower stringifies 200 bodies on its way through),
        // an empty second page must terminate pagination, and the GraphQL
        // query must then actually be issued.
        $ref = new ReflectionProperty(\app\components\github\GithubProgress::class, 'allResponseRepositories');
        $ref->setAccessible(true);
        $previousRepositories = $ref->getValue();
        $previousAliases = Yii::$aliases;
        $dataPath = null;
        $graphqlCalls = [];

        try {
            $dataPath = sys_get_temp_dir() . '/github-progress-enumeration-' . uniqid('', true);
            mkdir($dataPath, 0777, true);
            file_put_contents($dataPath . '/github.token', 'test-token');
            \Yii::setAlias('@app', dirname(__DIR__, 2));
            \Yii::setAlias('@app/data', $dataPath);

            $client = new class($graphqlCalls) implements ClientInterface {
                public function __construct(private array &$calls)
                {
                }

                public function sendRequest(\Psr\Http\Message\RequestInterface $request): \Psr\Http\Message\ResponseInterface
                {
                    $path = $request->getUri()->getPath();
                    if ($path === '/orgs/yiisoft/repos') {
                        if (stripos($request->getUri()->getQuery(), 'page=1') !== 0) {
                            return new Response(200, ['Content-Type' => 'application/json'], '[]');
                        }
                        $repositories = [];
                        foreach (\app\components\github\GithubProgress::VERSIONS as $version) {
                            $repositories[] = [
                                'archived' => false,
                                'full_name' => 'yiisoft/yii' . (int) $version,
                                'topics' => ['yii' . (int) $version],
                            ];
                        }
                        return new Response(200, ['Content-Type' => 'application/json'], json_encode($repositories));
                    }

                    \PHPUnit\Framework\Assert::assertSame('/graphql', $path);
                    $this->calls[] = (string) $request->getBody();
                    return new Response(200, ['Content-Type' => 'application/json'], json_encode([
                        'data' => [
                            'yiisoft_yii1' => [
                                'nameWithOwner' => 'yiisoft/yii1',
                                'issues' => ['totalCount' => 1],
                                'pullRequests' => ['totalCount' => 2],
                                'mergedPRs' => ['nodes' => []],
                                'refs' => ['edges' => []],
                                'releases' => ['edges' => []],
                                'repositoryTopics' => ['nodes' => []],
                            ],
                        ],
                    ]));
                }
            };

            // 3.x Builder accepts an injected PSR-18 client and installs its
            // usual public plugins (exception thrower included).
            $githubClient = new Client(new Builder($client));
            $data = (new \app\components\github\GithubProgress('1.1', $githubClient))->getData();

            $repositories = $ref->getValue();
            self::assertCount(3, $repositories, 'the nonempty page must survive the plugin chain');
            self::assertSame('yiisoft/yii1', $repositories[0]->full_name);

            self::assertCount(1, $graphqlCalls, 'the GraphQL query must actually be issued');
            self::assertSame('yiisoft/yii1', $data[0]['repository']);
            self::assertSame(1, $data[0]['issues']);
            self::assertSame(2, $data[0]['pullRequests']);
            self::assertSame('', $data[0]['latest']);
            self::assertNull($data[0]['no_release_for']);
        } finally {
            $ref->setValue(null, $previousRepositories);
            \Yii::$aliases = $previousAliases;
            if ($dataPath !== null) {
                @unlink($dataPath . '/github.token');
                @rmdir($dataPath);
            }
        }
    }

    public function testAuthenticateUsesAccessTokenConstant(): void
    {
        // Locks GithubProgress::getData()'s call:
        //   $this->client->authenticate($token, null, GithubClient::AUTH_ACCESS_TOKEN);
        //
        // GitHub API client 3.x removed AUTH_HTTP_TOKEN; ACCESS_TOKEN is the
        // replacement, exposed on Github\Client as AUTH_ACCESS_TOKEN and on
        // Github\AuthMethod as ACCESS_TOKEN.
        $clientConst = Github\Client::AUTH_ACCESS_TOKEN;
        self::assertSame(Github\AuthMethod::ACCESS_TOKEN, $clientConst);
        self::assertSame('access_token_header', $clientConst);

        // The removed constant must indeed be gone (this is why the app code
        // had to be updated).
        self::assertFalse(
            (new ReflectionClass(Github\Client::class))->hasConstant('AUTH_HTTP_TOKEN'),
            'AUTH_HTTP_TOKEN should have been removed by github-api 3.x'
        );
    }

    public function testAuthenticateMethodAcceptsTokenAndAuthMethod(): void
    {
        // Signature of GitHub client 3.x:
        //   public function authenticate($tokenOrLogin, $password = null, $authMethod = null): void
        $method = (new ReflectionClass(Github\Client::class))->getMethod('authenticate');
        $params = array_map(fn($p) => $p->getName(), $method->getParameters());
        self::assertSame(['tokenOrLogin', 'password', 'authMethod'], $params);
        self::assertSame(null, $method->getParameters()[1]->getDefaultValue());
        self::assertSame(null, $method->getParameters()[2]->getDefaultValue());
    }

    public function testGithubProgressAcceptsValidVersionsOnly(): void
    {
        // Valid versions accepted (constructor does not reach the network).
        foreach (['1.1', '2.0', '3.0'] as $v) {
            self::assertNotNull(
                new \app\components\github\GithubProgress($v, new Github\Client()),
                "version $v should be accepted"
            );
        }

        $this->expectException(\yii\base\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid version. Valid versions are: 1.1,2.0,3.0.');
        new \app\components\github\GithubProgress('2.1', new Github\Client());
    }
}
