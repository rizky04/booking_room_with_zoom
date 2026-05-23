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

        try {
            $token    = $this->getAccessToken();
            $duration = $booking->duration_minutes;

            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/users/me/meetings", [
                    'topic'      => $booking->title,
                    'type'       => 2,
                    'start_time' => $booking->date->format('Y-m-d') . 'T' . $booking->start_time . ':00',
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
                    'booking_id'     => $booking->id,
                    'zoom_meeting_id' => (string) $data['id'],
                    'zoom_uuid'      => $data['uuid'] ?? null,
                    'host_id'        => $data['host_id'] ?? null,
                    'topic'          => $data['topic'],
                    'join_url'       => $data['join_url'],
                    'start_url'      => $data['start_url'] ?? null,
                    'password'       => $data['password'] ?? null,
                    'host_email'     => $data['host_email'] ?? null,
                    'duration'       => $data['duration'],
                    'start_time'     => $data['start_time'],
                ]);
            }

            Log::error('Zoom meeting creation failed', [
                'booking_id' => $booking->id,
                'response'   => $response->json(),
            ]);
        } catch (\Exception $e) {
            Log::error('Zoom meeting creation exception', [
                'booking_id' => $booking->id,
                'error'      => $e->getMessage(),
            ]);
        }

        return null;
    }

    public function deleteMeeting(string $meetingId): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        try {
            $token    = $this->getAccessToken();
            $response = Http::withToken($token)->delete("{$this->baseUrl}/meetings/{$meetingId}");
            return $response->successful() || $response->status() === 404;
        } catch (\Exception $e) {
            Log::error('Zoom meeting deletion failed', ['meeting_id' => $meetingId, 'error' => $e->getMessage()]);
            return false;
        }
    }

    private function getAccessToken(): string
    {
        return Cache::remember('zoom_access_token', config('zoom.cache_ttl', 3500), function () {
            $clientId     = config('zoom.client_id');
            $clientSecret = config('zoom.client_secret');
            $accountId    = config('zoom.account_id');

            $response = Http::withBasicAuth($clientId, $clientSecret)
                ->asForm()
                ->post(config('zoom.token_url'), [
                    'grant_type' => 'account_credentials',
                    'account_id' => $accountId,
                ]);

            if (!$response->successful()) {
                throw new \Exception('Failed to get Zoom access token: ' . $response->body());
            }

            return $response->json('access_token');
        });
    }
}
