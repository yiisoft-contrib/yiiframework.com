<?php

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
