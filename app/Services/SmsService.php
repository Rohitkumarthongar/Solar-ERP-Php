<?php

namespace App\Services;

use App\Models\SmsConfiguration;
use App\Models\SmsTemplate;
use App\Models\SmsLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private ?SmsConfiguration $config;

    public function __construct()
    {
        $this->config = SmsConfiguration::where('is_active', true)->first();
    }

    public function send(string $to, string $message, string $type = 'general', $relatedType = null, $relatedId = null): bool
    {
        $to = preg_replace('/[^0-9+]/', '', $to);
        if (!str_starts_with($to, '+')) {
            $to = '+91' . ltrim($to, '0');
        }

        $log = SmsLog::create([
            'to_number'    => $to,
            'message'      => $message,
            'type'         => $type,
            'related_type' => $relatedType,
            'related_id'   => $relatedId,
            'status'       => 'pending',
        ]);

        if (!$this->config || !$this->config->is_active) {
            $log->update(['status' => 'failed', 'error_message' => 'No active SMS configuration']);
            return false;
        }

        try {
            $result = match ($this->config->provider) {
                'twilio'    => $this->sendViaTwilio($to, $message),
                'msg91'     => $this->sendViaMsg91($to, $message),
                'fast2sms'  => $this->sendViaFast2Sms($to, $message),
                'textlocal' => $this->sendViaTextLocal($to, $message),
                default     => false,
            };

            $log->update(['status' => $result ? 'sent' : 'failed']);
            return $result;
        } catch (\Exception $e) {
            $log->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            Log::error('SMS send error: ' . $e->getMessage());
            return false;
        }
    }

    public function sendFromTemplate(string $type, string $to, string $toName, array $replacements = [], $relatedType = null, $relatedId = null): bool
    {
        $template = SmsTemplate::where('type', $type)->where('is_active', true)->first();
        if (!$template) return false;

        $message = $template->message;
        foreach ($replacements as $key => $value) {
            $message = str_replace('{' . $key . '}', $value, $message);
        }

        return $this->send($to, $message, $type, $relatedType, $relatedId);
    }

    private function sendViaTwilio(string $to, string $message): bool
    {
        $response = Http::withBasicAuth($this->config->account_sid, $this->config->auth_token)
            ->asForm()
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$this->config->account_sid}/Messages.json", [
                'To'   => $to,
                'From' => $this->config->from_number,
                'Body' => $message,
            ]);

        return $response->successful() && isset($response->json()['sid']);
    }

    private function sendViaMsg91(string $to, string $message): bool
    {
        $mobile = ltrim(preg_replace('/[^0-9]/', '', $to), '91');
        $response = Http::withHeaders(['authkey' => $this->config->api_key])
            ->get('https://api.msg91.com/api/sendhttp.php', [
                'mobiles'  => '91' . $mobile,
                'message'  => $message,
                'sender'   => $this->config->sender_id ?? 'SOLRTP',
                'route'    => '4',
                'country'  => '91',
            ]);

        return $response->successful();
    }

    private function sendViaFast2Sms(string $to, string $message): bool
    {
        $mobile = ltrim(preg_replace('/[^0-9]/', '', $to), '91');
        $response = Http::withHeaders(['authorization' => $this->config->api_key])
            ->get('https://www.fast2sms.com/dev/bulkV2', [
                'variables_values' => $message,
                'route'            => 'q',
                'numbers'          => $mobile,
            ]);

        return $response->successful() && ($response->json()['return'] ?? false);
    }

    private function sendViaTextLocal(string $to, string $message): bool
    {
        $response = Http::asForm()->post('https://api.textlocal.in/send/', [
            'apikey'  => $this->config->api_key,
            'numbers' => $to,
            'message' => $message,
            'sender'  => $this->config->sender_id ?? 'SOLRTP',
        ]);

        $data = $response->json();
        return isset($data['status']) && $data['status'] === 'success';
    }
}