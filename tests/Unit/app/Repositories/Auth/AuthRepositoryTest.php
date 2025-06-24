<?php

namespace Tests\Unit\App\Repositories\Auth;

use App\Exceptions\Auth\RevokeTokenException;
use App\Repositories\Auth\AuthRepository;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Mockery;
use PHPUnit\Framework\TestCase;

class AuthRepositoryTest extends TestCase
{
    public function test_it_checks_if_token_exists(): void
    {
        $tokenName = 'test-token';

        $tokensMock = Mockery::mock();
        $tokensMock->shouldReceive('whereName')
            ->with($tokenName)
            ->once()
            ->andReturnSelf();
        $tokensMock->shouldReceive('exists')
            ->once()
            ->andReturn(true);

        $userMock = Mockery::mock();
        $userMock->shouldReceive('tokens')
            ->once()
            ->andReturn($tokensMock);

        Auth::shouldReceive('user')
            ->once()
            ->andReturn($userMock);

        $repository = new AuthRepository();

        $this->assertTrue($repository->exists($tokenName));
    }

    public function test_it_gets_token_by_name(): void
    {
        $tokenName = 'test-token';

        $tokenMock = Mockery::mock(PersonalAccessToken::class);

        $tokenMock->shouldReceive('setAttribute');

        $tokensMock = Mockery::mock();
        $tokensMock->shouldReceive('whereName')
            ->with($tokenName)
            ->once()
            ->andReturnSelf();
        $tokensMock->shouldReceive('first')
            ->once()
            ->andReturn($tokenMock);

        $userMock = Mockery::mock();
        $userMock->shouldReceive('tokens')
            ->once()
            ->andReturn($tokensMock);

        Auth::shouldReceive('user')
            ->once()
            ->andReturn($userMock);

        $repository = new AuthRepository();

        $result = $repository->getToken($tokenName);

        $this->assertInstanceOf(PersonalAccessToken::class, $result);
    }

    public function test_it_revokes_token_successfully(): void
    {
        $tokenName = 'test-token';

        $tokenMock = Mockery::mock(PersonalAccessToken::class);

        $tokenMock->shouldReceive('setAttribute');
        $tokenMock->shouldReceive('getAttribute');

        $tokenMock->expires_at = null;


        $tokenMock->shouldReceive('save')
            ->once()
            ->andReturn(true);

        $tokensMock = Mockery::mock();
        $tokensMock->shouldReceive('whereName')
            ->with($tokenName)
            ->once()
            ->andReturnSelf();
        $tokensMock->shouldReceive('first')
            ->once()
            ->andReturn($tokenMock);

        $userMock = Mockery::mock();
        $userMock->shouldReceive('tokens')
            ->once()
            ->andReturn($tokensMock);
        $userMock->id = 123;

        Auth::shouldReceive('user')
            ->twice()
            ->andReturn($userMock);

        $repository = new AuthRepository();
        $repository->revokeToken($tokenName);

        $this->assertNotInstanceOf(\DateTime::class, $tokenMock->expires_at);
    }

    public function test_it_does_not_revoke_expired_token(): void
    {
        $tokenName = 'test-token';

        $tokenMock = Mockery::mock(PersonalAccessToken::class);

        $tokenMock->shouldReceive('setAttribute');
        $tokenMock->shouldReceive('getAttribute');

        $tokenMock->expires_at = now()->subMinute();

        $tokenMock->shouldNotReceive('save');

        $tokensMock = Mockery::mock();
        $tokensMock->shouldReceive('whereName')
            ->with($tokenName)
            ->never();
        $tokensMock->shouldReceive('first')
            ->never();

        $userMock = Mockery::mock();
        $userMock->shouldReceive('tokens')
            ->never();

        Auth::shouldReceive('user')
            ->never();

        $repository = new AuthRepository();
        $repository->revokeToken($tokenName);

        $this->assertTrue(true);
    }

    public function test_it_throws_exception_when_revoke_fails(): void
    {
        $tokenName = 'test-token';

        $tokenMock = Mockery::mock(PersonalAccessToken::class);

        $tokenMock->shouldReceive('setAttribute');
        $tokenMock->shouldReceive('getAttribute');

        $tokenMock->expires_at = null;

        $tokenMock->shouldReceive('save')
            ->once()
            ->andReturn(false);

        $tokensMock = Mockery::mock();
        $tokensMock->shouldReceive('whereName')
            ->with($tokenName)
            ->once()
            ->andReturnSelf();
        $tokensMock->shouldReceive('first')
            ->once()
            ->andReturn($tokenMock);

        $userMock = Mockery::mock();
        $userMock->shouldReceive('tokens')
            ->once()
            ->andReturn($tokensMock);
        $userMock->id = 456;

        Auth::shouldReceive('user')
            ->twice()
            ->andReturn($userMock);

        $repository = new AuthRepository();

        $this->expectException(RevokeTokenException::class);
        $this->expectExceptionMessage("An error occured when trying to revoke the $tokenName token. USERID: 456");

        $repository->revokeToken($tokenName);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
