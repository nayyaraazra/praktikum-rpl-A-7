<?php

namespace Tests\Unit;

use App\Models\Store;
use Carbon\Carbon;
use Tests\TestCase;

class StoreTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow(); // Reset Carbon time after each test
        parent::tearDown();
    }

    /**
     * Test when operating hours is empty.
     */
    public function test_store_is_open_by_default_when_operating_hours_empty(): void
    {
        $store = new Store();
        $store->operating_hours = '';
        $this->assertTrue($store->isOpen());

        $store->operating_hours = null;
        $this->assertTrue($store->isOpen());
    }

    /**
     * Test store is open every day at different times.
     */
    public function test_store_open_every_day(): void
    {
        $store = new Store();
        $store->operating_hours = 'Setiap Hari, 08:00 - 20:00';

        // Monday at 10:00 (Open)
        Carbon::setTestNow(Carbon::parse('2026-06-15 10:00:00', 'Asia/Jakarta')); // 2026-06-15 is a Monday
        $this->assertTrue($store->isOpen());

        // Monday at 21:00 (Closed)
        Carbon::setTestNow(Carbon::parse('2026-06-15 21:00:00', 'Asia/Jakarta'));
        $this->assertFalse($store->isOpen());

        // Sunday at 12:00 (Open)
        Carbon::setTestNow(Carbon::parse('2026-06-21 12:00:00', 'Asia/Jakarta')); // 2026-06-21 is a Sunday
        $this->assertTrue($store->isOpen());
    }

    /**
     * Test store with range of days, e.g., Monday - Friday.
     */
    public function test_store_open_weekday_range(): void
    {
        $store = new Store();
        $store->operating_hours = 'Senin - Jumat, 09:00 - 17:00';

        // Monday at 12:00 (Open)
        Carbon::setTestNow(Carbon::parse('2026-06-15 12:00:00', 'Asia/Jakarta'));
        $this->assertTrue($store->isOpen());

        // Saturday at 12:00 (Closed)
        Carbon::setTestNow(Carbon::parse('2026-06-20 12:00:00', 'Asia/Jakarta')); // 2026-06-20 is a Saturday
        $this->assertFalse($store->isOpen());
    }

    /**
     * Test store with list of days, e.g., Senin, Rabu, Jumat.
     */
    public function test_store_open_specific_days_list(): void
    {
        $store = new Store();
        $store->operating_hours = 'Senin, Rabu, Jumat, 09.00 - 17.00';

        // Monday at 12:00 (Open)
        Carbon::setTestNow(Carbon::parse('2026-06-15 12:00:00', 'Asia/Jakarta'));
        $this->assertTrue($store->isOpen());

        // Tuesday at 12:00 (Closed)
        Carbon::setTestNow(Carbon::parse('2026-06-16 12:00:00', 'Asia/Jakarta')); // 2026-06-16 is a Tuesday
        $this->assertFalse($store->isOpen());
    }

    /**
     * Test overnight store hours, e.g., 22:00 to 04:00.
     */
    public function test_store_overnight_hours(): void
    {
        $store = new Store();
        $store->operating_hours = 'Setiap Hari, 22:00 - 04:00';

        // 23:00 (Open)
        Carbon::setTestNow(Carbon::parse('2026-06-15 23:00:00', 'Asia/Jakarta'));
        $this->assertTrue($store->isOpen());

        // 02:00 (Open)
        Carbon::setTestNow(Carbon::parse('2026-06-16 02:00:00', 'Asia/Jakarta'));
        $this->assertTrue($store->isOpen());

        // 12:00 (Closed)
        Carbon::setTestNow(Carbon::parse('2026-06-15 12:00:00', 'Asia/Jakarta'));
        $this->assertFalse($store->isOpen());
    }
}
