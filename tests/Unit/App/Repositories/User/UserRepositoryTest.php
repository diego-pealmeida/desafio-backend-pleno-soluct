<?php

namespace Tests\Unit\App\Repositories\User;

use App\Data\UserData;
use App\Exceptions\User\CreateException;
use App\Models\User;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Hash;
use Mockery;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{
    private string $userPassword = 'secret';
    private string $userPasswordHashed = 'hashed';
    private array $userArray = [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'secret'
    ];

    public function test_it_finds_user_by_email(): void
    {
        $userMock = Mockery::mock(User::class);

        $userMock->shouldReceive('whereRaw')
            ->with('LOWER(email) = ?', 'john@example.com')
            ->once()
            ->andReturnSelf();
        $userMock->shouldReceive('first')
            ->once()
            ->andReturn(new User(['email' => 'john@example.com']));

        $repository = new UserRepository($userMock);
        $user = $repository->findByEmail('john@example.com');

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('john@example.com', $user->email);
    }

    public function test_it_returns_null_if_email_not_found()
    {
        $mock = Mockery::mock(User::class);
        $mock->shouldReceive('whereRaw')->andReturnSelf();
        $mock->shouldReceive('first')->andReturn(null);

        $repository = new UserRepository($mock);
        $this->assertNull($repository->findByEmail('missing@example.com'));
    }

    public function test_it_creates_a_user_successfully()
    {
        $data = Mockery::mock(UserData::class);

        $data->shouldReceive('toArray')
            ->once()
            ->andReturn($this->userArray);

        Hash::shouldReceive('make')
            ->with($this->userPassword)
            ->once()
            ->andReturn($this->userPasswordHashed);

        $userMock = Mockery::mock(User::class);

        $userMock->shouldReceive('fill')
            ->with($this->userArray)
            ->once()
            ->andReturnSelf();
        $userMock->shouldReceive('getAttribute')
            ->with('password')
            ->twice()
            ->andReturn($this->userPassword, $this->userPasswordHashed);
        $userMock->shouldReceive('setAttribute')
            ->with('password', $this->userPasswordHashed)
            ->once();
        $userMock->shouldReceive('save')
            ->once()
            ->andReturn(true);

        $repository = new UserRepository($userMock);

        $user = $repository->create($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($this->userPasswordHashed, $user->password);
    }

    public function test_is_created_user_a_throw()
    {
        $data = Mockery::mock(UserData::class);

        $data->shouldReceive('toArray')
            ->once()
            ->andReturn($this->userArray);

        $userMock = Mockery::mock(User::class);

        $userMock->shouldReceive('fill')
            ->with($this->userArray)
            ->once()
            ->andReturnSelf();
        $userMock->shouldReceive('getAttribute')
            ->with('password')
            ->once()
            ->andReturn($this->userPassword);
        $userMock->shouldReceive('setAttribute')
            ->with('password', $this->userPasswordHashed)
            ->once();
        $userMock->shouldReceive('save')
            ->once()
            ->andReturn(false);

        $repository = new UserRepository($userMock);

        $this->expectException(CreateException::class);
        $this->expectExceptionMessage('An error occured when trying to create the user');

        $repository->create($data);
    }

    function tearDown(): void
    {
        Mockery::close();
    }
}
