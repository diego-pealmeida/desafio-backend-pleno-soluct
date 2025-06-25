<?php

namespace Tests\Unit\App\Services\Auth;

use App\Data\AccessTokenData;
use App\Data\LoginData;
use App\Exceptions\Auth\InvalidCredentialsException;
use App\Models\User;
use App\Repositories\Auth\Repository as AuthRepository;
use App\Repositories\User\Repository as UserRepository;
use App\Services\Auth\AuthService;
use Illuminate\Support\Facades\Hash;
use Mockery;
use PHPUnit\Framework\TestCase;

class AuthServiceTest extends TestCase
{
    public function test_login_successful()
    {
        $loginData = new LoginData(email: 'user@example.com', password: 'secret', remember_me: false);

        $userMock = Mockery::mock(User::class, ['password' => 'hashed_password']);

        $userMock->shouldReceive('setAttribute');
        $userMock->shouldReceive('getAttribute');

        $userMock->shouldReceive('createToken')
            ->once()
            ->with(User::API_TOKEN_NAME, ['*'], Mockery::type('Carbon\Carbon'))
            ->andReturn((object)['plainTextToken' => 'token123']);

        $userRepositoryMock = Mockery::mock(UserRepository::class);
        $userRepositoryMock->shouldReceive('findByEmail')
            ->with('user@example.com')
            ->once()
            ->andReturn($userMock);

        Hash::shouldReceive('check')
            ->with($loginData->password, $userMock->password)
            ->once()
            ->andReturn(true);

        $authRepositoryMock = Mockery::mock(AuthRepository::class);

        $service = new AuthService($authRepositoryMock, $userRepositoryMock);

        $result = $service->login($loginData);

        $this->assertInstanceOf(AccessTokenData::class, $result);
        $this->assertEquals('token123', $result->access_token);
        $this->assertNotNull($result->expires_at);
    }

    public function test_login_successful_with_remember_me()
    {
        $loginData = new LoginData(email: 'user@example.com', password: 'secret', remember_me: true);

        $userMock = Mockery::mock(User::class, ['password' => 'hashed_password']);

        $userMock->shouldReceive('setAttribute');
        $userMock->shouldReceive('getAttribute');

        $userMock->shouldReceive('createToken')
            ->once()
            ->with(User::API_TOKEN_NAME, ['*'], null)
            ->andReturn((object)['plainTextToken' => 'token456']);

        $userRepositoryMock = Mockery::mock(UserRepository::class);
        $userRepositoryMock->shouldReceive('findByEmail')
            ->with('user@example.com')
            ->once()
            ->andReturn($userMock);

        Hash::shouldReceive('check')
            ->with($loginData->password, $userMock->password)
            ->once()
            ->andReturn(true);

        $authRepositoryMock = Mockery::mock(AuthRepository::class);

        $service = new AuthService($authRepositoryMock, $userRepositoryMock);

        $result = $service->login($loginData);

        $this->assertInstanceOf(AccessTokenData::class, $result);
        $this->assertEquals('token456', $result->access_token);
        $this->assertNull($result->expires_at);
    }

    public function test_login_throws_invalid_credentials_exception_when_user_not_found()
    {
        $loginData = new LoginData(email: 'notfound@example.com', password: 'secret', remember_me: false);

        $userRepositoryMock = Mockery::mock(UserRepository::class);
        $userRepositoryMock->shouldReceive('findByEmail')
            ->with('notfound@example.com')
            ->once()
            ->andReturn(null);

        $authRepositoryMock = Mockery::mock(AuthRepository::class);

        $service = new AuthService($authRepositoryMock, $userRepositoryMock);

        $this->expectException(InvalidCredentialsException::class);
        $this->expectExceptionMessage('email or password is invalid');

        $service->login($loginData);
    }

    public function test_login_throws_invalid_credentials_exception_when_password_is_wrong()
    {
        $loginData = new LoginData(email: 'user@example.com', password: 'wrongpassword', remember_me: false);

        $userMock = Mockery::mock(User::class, ['password' => 'hashed_password']);

        $userMock->shouldReceive('setAttribute');
        $userMock->shouldReceive('getAttribute');

        $userRepositoryMock = Mockery::mock(UserRepository::class);
        $userRepositoryMock->shouldReceive('findByEmail')
            ->with('user@example.com')
            ->once()
            ->andReturn($userMock);

        Hash::shouldReceive('check')
            ->with($loginData->password, $userMock->password)
            ->once()
            ->andReturn(false);

        $authRepositoryMock = Mockery::mock(AuthRepository::class);

        $service = new AuthService($authRepositoryMock, $userRepositoryMock);

        $this->expectException(InvalidCredentialsException::class);
        $this->expectExceptionMessage('email or password is invalid');

        $service->login($loginData);
    }

    public function test_revoke_token_when_token_exists()
    {
        $authRepositoryMock = Mockery::mock(AuthRepository::class);
        $authRepositoryMock->shouldReceive('exists')
            ->with(User::API_TOKEN_NAME)
            ->once()
            ->andReturn(true);
        $authRepositoryMock->shouldReceive('revokeToken')
            ->with(User::API_TOKEN_NAME)
            ->once();

        $userRepositoryMock = Mockery::mock(UserRepository::class);

        $service = new AuthService($authRepositoryMock, $userRepositoryMock);

        $service->revokeToken();

        $this->assertTrue(true);
    }

    public function test_revoke_token_when_token_does_not_exist()
    {
        $authRepositoryMock = Mockery::mock(AuthRepository::class);
        $authRepositoryMock->shouldReceive('exists')
            ->with(User::API_TOKEN_NAME)
            ->once()
            ->andReturn(false);

        $userRepositoryMock = Mockery::mock(UserRepository::class);

        $service = new AuthService($authRepositoryMock, $userRepositoryMock);

        $service->revokeToken();

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
