<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    protected $primaryKey = 'id_store';

    protected $fillable = [
        'id_user',
        'store_name',
        'store_category',
        'store_logo',
        'description',
        'address',
        'verification_status',
        'operating_hours',
        'district',
        'instagram',
        'whatsapp',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'id_store', 'id_store');
    }

    public function paymentAccounts(): HasMany
    {
        return $this->hasMany(PaymentAccount::class, 'id_store', 'id_store');
    }

    public function isVerified(): bool
    {
        return $this->verification_status === 'disetujui';
    }

    public function isOpen(): bool
    {
        if (empty($this->operating_hours)) {
            return true; // default to open if not set
        }

        try {
            $now = \Carbon\Carbon::now('Asia/Jakarta');
            
            $daysOfWeek = [
                0 => 'min', // Sunday
                1 => 'sen', // Monday
                2 => 'sel', // Tuesday
                3 => 'rab', // Wednesday
                4 => 'kam', // Thursday
                5 => 'jum', // Friday
                6 => 'sab', // Saturday
            ];
            $currentDayCode = $daysOfWeek[$now->dayOfWeek];
            
            $str = preg_replace('/\s+/', ' ', trim($this->operating_hours));
            
            $times = $this->parseTimeRange($str);
            if (!$times) {
                return true; 
            }
            list($openTimeStr, $closeTimeStr) = $times;
            
            $parts = explode(',', $str);
            $daysPart = count($parts) > 1 ? $parts[0] : $str;
            $lowerDays = strtolower($daysPart);
            
            list($openEveryDay, $openDays) = $this->parseOpenDays($lowerDays);
            
            if (!$openEveryDay) {
                if (!in_array($currentDayCode, $openDays)) {
                    return false; 
                }
            }
            
            $currentMinutes = $now->hour * 60 + $now->minute;
            
            return $this->isTimeWithinRange($currentMinutes, $openTimeStr, $closeTimeStr);
        } catch (\Exception $e) {
            return true;
        }
    }

    /**
     * Parse open and close times from the operating hours string.
     *
     * @param string $str
     * @return array|null [openTimeStr, closeTimeStr] or null
     */
    private function parseTimeRange(string $str): ?array
    {
        preg_match_all('/(\d{2}[:\.]\d{2})/', $str, $matches);
        if (empty($matches[0]) || count($matches[0]) < 2) {
            return null;
        }

        return [
            str_replace('.', ':', $matches[0][0]),
            str_replace('.', ':', $matches[0][1])
        ];
    }

    /**
     * Parse the active days that the store is open.
     *
     * @param string $lowerDays
     * @return array [bool openEveryDay, array openDays]
     */
    private function parseOpenDays(string $lowerDays): array
    {
        if (str_contains($lowerDays, 'setiap hari') || str_contains($lowerDays, 'setiap-hari')) {
            return [true, []];
        }

        $weekdayMapping = [
            'senin' => 'sen', 'sen' => 'sen',
            'selasa' => 'sel', 'sel' => 'sel',
            'rabu' => 'rab', 'rab' => 'rab',
            'kamis' => 'kam', 'kam' => 'kam',
            'jumat' => 'jum', "jum'at" => 'jum', 'jum' => 'jum',
            'sabtu' => 'sab', 'sab' => 'sab',
            'minggu' => 'min', 'min' => 'min'
        ];

        $weekdayOrder = ['sen', 'sel', 'rab', 'kam', 'jum', 'sab', 'min'];
        $rangePattern = '/(senin|selasa|rabu|kamis|jumat|sabtu|minggu|sen|sel|rab|kam|jum|sab|min)\s*[\-–]\s*(senin|selasa|rabu|kamis|jumat|sabtu|minggu|sen|sel|rab|kam|jum|sab|min)/u';

        if (preg_match($rangePattern, $lowerDays, $rangeMatch)) {
            $startKey = $weekdayMapping[$rangeMatch[1]] ?? null;
            $endKey = $weekdayMapping[$rangeMatch[2]] ?? null;
            if ($startKey && $endKey) {
                $startIndex = array_search($startKey, $weekdayOrder);
                $endIndex = array_search($endKey, $weekdayOrder);
                if ($startIndex !== false && $endIndex !== false) {
                    $openDays = [];
                    $i = $startIndex;
                    while (true) {
                        $openDays[] = $weekdayOrder[$i];
                        if ($i === $endIndex) {
                            break;
                        }
                        $i = ($i + 1) % 7;
                    }
                    return [false, $openDays];
                }
            }
            return [false, []];
        }

        $words = preg_split('/[\s,\/]+/', $lowerDays);
        $openDays = [];
        foreach ($words as $w) {
            $cleanWord = preg_replace('/[^a-z\']/i', '', $w);
            if (isset($weekdayMapping[$cleanWord])) {
                $dayVal = $weekdayMapping[$cleanWord];
                if (!in_array($dayVal, $openDays)) {
                    $openDays[] = $dayVal;
                }
            }
        }

        return [false, $openDays];
    }

    /**
     * Check if a given time falls within the open and close minutes.
     *
     * @param int $currentMinutes
     * @param string $openTimeStr
     * @param string $closeTimeStr
     * @return bool
     */
    private function isTimeWithinRange(int $currentMinutes, string $openTimeStr, string $closeTimeStr): bool
    {
        list($openH, $openM) = explode(':', $openTimeStr);
        $openMinutes = (int)$openH * 60 + (int)$openM;

        list($closeH, $closeM) = explode(':', $closeTimeStr);
        $closeMinutes = (int)$closeH * 60 + (int)$closeM;

        if ($openMinutes <= $closeMinutes) {
            return $currentMinutes >= $openMinutes && $currentMinutes <= $closeMinutes;
        } else {
            return $currentMinutes >= $openMinutes || $currentMinutes <= $closeMinutes;
        }
    }
}
