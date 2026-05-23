<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\ZoomMeeting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZoomService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('zoom.base_url', 'https://api.zoom.us/v2');
    }

    public function isConfigured(): bool
    {
        return !empty(config('zoom.client_id'))
            && !empty(config('zoom.client_secret'))
            && !empty(config('zoom.account_id'));
    }

    public function createMeeting(Booking $booking): ?ZoomMeeting
    {
        if (!$this->isConfigured()) {
            Log::info('Zoom not configured, skipping meeting creation for booking ' . $booking->id);
            return null;
        }

        $accountIndex = $this->selectAccount($booking);

        try {
            $token    = $this->getAccessToken($accountIndex);
            $duration = $booking->duration_minutes;

            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/users/me/meetings", [
                    'topic'      => $booking->title,
                    'type'       => 2,
                    'start_time' => $booking->date->format('Y-m-d') . 'T' . substr($booking->start_time, 0, 5) . ':00',
                    'duration'   => $duration,
                    'timezone'   => config('app.timezone', 'Asia/Jakarta'),
                    'agenda'     => $booking->description,
                    'settings'   => [
                        'host_video'        => true,
                        'participant_video'  => false,
                        'join_before_host'  => true,
                        'mute_upon_entry'   => true,
                        'waiting_room'      => false,
                        'auto_recording'    => 'none',
                    ],
                ]);

            if ($response->successful()) {
                $data = $response->json();

                return ZoomMeeting::create([
                    'booking_id'      => $booking->id,
                    'account_index'   => $accountIndex,
                    'zoom_meeting_id' => (string) $data['id'],
                    'zoom_uuid'       => $data['uuid'] ?? null,
                    'host_id'         => $data['host_id'] ?? null,
                    'topic'           => $data['topic'],
                    'join_url'        => $data['join_url'],
                    'start_url'       => $data['start_url'] ?? null,
                    'password'        => $data['password'] ?? null,
                    'host_email'      => $data['host_email'] ?? null,
                    'duration'        => $data['duration'],
                    'start_time'      => $data['start_time'],
                ]);
            }

            Log::error('Zoom meeting creation failed', [
                'booking_id'    => $booking->id,
                'account_index' => $accountIndex,
                'response'      => $response->json(),
            ]);
        } catch (\Exception $e) {
            Log::error('Zoom meeting creation exception', [
                'booking_id'    => $booking->id,
                'account_index' => $accountIndex,
                'error'         => $e->getMessage(),
            ]);
        }

        return null;
    }

    public function deleteMeeting(string $meetingId, int $accountIndex = 1): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        try {
            $token    = $this->getAccessToken($accountIndex);
            $response = Http::withToken($token)->delete("{$this->baseUrl}/meetings/{$meetingId}");
            return $response->successful() || $response->status() === 404;
        } catch (\Exception $e) {
            Log::error('Zoom meeting deletion failed', ['meeting_id' => $meetingId, 'error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Pilih akun yang tidak sedang dipakai pada slot waktu booking tersebut.
     * Prioritas akun 1; jika akun 1 sudah ada meeting yang bentrok, pakai akun 2.
     */
    private function selectAccount(Booking $booking): int
    {
        $account1Busy = ZoomMeeting::whereHas('booking', function ($q) use ($booking) {
            $q->where('date', $booking->date->toDateString())
              ->where('id', '!=', $booking->id)
              ->whereIn('status', ['pending', 'confirmed'])
              ->where('start_time', '<', $booking->end_time)
              ->where('end_time', '>', $booking->start_time);
        })->where('account_index', 1)->exists();

        if (!$account1Busy) {
            return 1;
        }

        // Cek apakah akun 2 dikonfigurasi
        $account2Configured = !empty(config('zoom.accounts.2.client_id'))
            && !empty(config('zoom.accounts.2.client_secret'))
            && !empty(config('zoom.accounts.2.account_id'));

        if ($account2Configured) {
            return 2;
        }

        // Fallback ke akun 1 jika akun 2 tidak dikonfigurasi
        return 1;
    }

    private function getAccessToken(int $accountIndex = 1): string
    {
        $cacheKey = "zoom_access_token_{$accountIndex}";

        return Cache::remember($cacheKey, config('zoom.cache_ttl', 3500), function () use ($accountIndex) {
            $account = config("zoom.accounts.{$accountIndex}");

            $response = Http::withBasicAuth($account['client_id'], $account['client_secret'])
                ->asForm()
                ->post(config('zoom.token_url'), [
                    'grant_type' => 'account_credentials',
                    'account_id' => $account['account_id'],
                ]);

            if (!$response->successful()) {
                throw new \Exception("Failed to get Zoom access token for account {$accountIndex}: " . $response->body());
            }

            return $response->json('access_token');
        });
    }
}
