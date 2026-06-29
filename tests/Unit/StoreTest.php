<?php

namespace Tests\Unit;

// P10 Unit Test - AAA Pattern (Arrange, Act, Assert)

use Tests\TestCase;
use App\Models\Store;
use Carbon\Carbon;

class StoreTest extends TestCase
{
    /**
     * Clean up after tests
     */
    protected function tearDown(): void
    {
        Carbon::setTestNow(); // Reset the mocked time
        parent::tearDown();
    }

    /**
     * Test happy case: store open every day, during open hours
     */
    public function test_should_return_true_when_store_is_open_everyday_and_within_operating_hours()
    {
        // Arrange
        $store = new Store([
            'operating_hours' => 'Setiap hari, 08:00 - 17:00'
        ]);
        
        // Mock current time to 12:00 (noon) on any day (e.g. 2023-10-10)
        $now = Carbon::create(2023, 10, 10, 12, 0, 0, 'Asia/Jakarta');
        Carbon::setTestNow($now);

        // Act
        $result = $store->isOpen();

        // Assert
        $this->assertTrue($result, 'Store should be open at 12:00');
    }

    /**
     * Test edge case: store empty operating hours
     */
    public function test_should_return_true_for_empty_operating_hours()
    {
        // Arrange
        $store = new Store([
            'operating_hours' => ''
        ]);

        // Act
        $result = $store->isOpen();

        // Assert
        $this->assertTrue($result, 'Store should default to open when operating_hours is empty');
    }

    /**
     * Test edge case: store closed because it is outside operating hours (days restriction)
     */
    public function test_should_return_false_when_store_is_closed_on_weekends()
    {
        // Arrange
        $store = new Store([
            'operating_hours' => 'Senin-Jumat, 08:00 - 17:00'
        ]);
        
        // Mock current time to Saturday (2023-10-14 is a Saturday)
        $now = Carbon::create(2023, 10, 14, 12, 0, 0, 'Asia/Jakarta');
        Carbon::setTestNow($now);

        // Act
        $result = $store->isOpen();

        // Assert
        $this->assertFalse($result, 'Store should be closed on Saturday');
    }

    /**
     * Test edge case: store closed because it is past operating hours time
     */
    public function test_should_return_false_when_time_is_past_operating_hours()
    {
        // Arrange
        $store = new Store([
            'operating_hours' => 'Senin-Jumat, 08:00 - 17:00'
        ]);
        
        // Mock current time to Monday 18:00 (2023-10-16 is Monday)
        $now = Carbon::create(2023, 10, 16, 18, 0, 0, 'Asia/Jakarta');
        Carbon::setTestNow($now);

        // Act
        $result = $store->isOpen();

        // Assert
        $this->assertFalse($result, 'Store should be closed at 18:00');
    }
    /**
     * Test happy case: store is verified
     */
    public function test_should_return_true_when_store_is_verified()
    {
        // Arrange
        $store = new Store([
            'verification_status' => 'disetujui'
        ]);

        // Act
        $result = $store->isVerified();

        // Assert
        $this->assertTrue($result, 'Store should be verified');
    }

    /**
     * Test edge case: store is not verified yet
     */
    public function test_should_return_false_when_store_is_not_verified()
    {
        // Arrange
        $store = new Store([
            'verification_status' => 'menunggu_verifikasi'
        ]);

        // Act
        $result = $store->isVerified();

        // Assert
        $this->assertFalse($result, 'Store should not be verified');
    }
}
