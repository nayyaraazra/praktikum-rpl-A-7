<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    /**
     * Test happy case: user has a seller role
     */
    public function test_should_return_true_when_user_is_seller()
    {
        // Arrange
        $user = new User();
        $user->roles = ['seller', 'buyer'];

        // Act
        $result = $user->isSeller();

        // Assert
        $this->assertTrue($result, 'User should be identified as seller');
    }

    /**
     * Test edge case: user roles array is completely empty or null
     */
    public function test_should_return_false_when_user_has_no_roles()
    {
        // Arrange
        $user = new User();
        $user->roles = [];

        // Act
        $result = $user->isSeller();

        // Assert
        $this->assertFalse($result, 'User with no roles should not be a seller');
    }

    /**
     * Test happy case: user has a buyer role
     */
    public function test_should_return_true_when_user_is_buyer()
    {
        // Arrange
        $user = new User();
        $user->roles = ['buyer'];

        // Act
        $result = $user->isBuyer();

        // Assert
        $this->assertTrue($result, 'User should be identified as buyer');
    }
}
