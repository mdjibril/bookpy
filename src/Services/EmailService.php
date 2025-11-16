<?php
namespace App\Services;

use GuzzleHttp\Client;

class EmailService
{
    protected string $mode;
    protected ?string $resendApiKey;
    protected array $config;

    public function __construct(array $config = null)
    {
        $this->config = $config ?? [];
        // Determine mode: terminal (dev), resend (Resend API) or smtp
        $this->mode = $this->config['mode'] ?? $_ENV['MAIL_MODE'] ?? 'terminal';
        $this->resendApiKey = $this->config['resend_api_key'] ?? $_ENV['RESEND_API_KEY'] ?? null;
    }

    /**
     * Send an email. attachments is an array of ['filename' => string, 'content' => binary]
     */
    public function send(string $to, string $subject, string $body, array $attachments = []): bool
    {
        if ($this->mode === 'terminal') {
            // Print email to stdout / server log for development testing
            $out = "\n" . str_repeat('=', 40) . "\n";
            $out .= "=== EMAIL (terminal mode) ===\n";
            $out .= "To: {$to}\n";
            $out .= "From: " . ($_ENV['MAIL_FROM'] ?? 'not-set') . "\n";
            $out .= "Subject: {$subject}\n";
            $out .= str_repeat('-', 40) . "\n";
            $out .= strip_tags(str_replace('<br />', "\n", $body)) . "\n"; // Clean up HTML for console

            if (!empty($attachments)) {
                $out .= "\nAttachments:\n";
                foreach ($attachments as $att) {
                    $name = $att['filename'] ?? 'attachment';
                    $out .= "- {$name} (".(isset($att['content']) ? strlen($att['content']) : '0')." bytes)\n";
                }
            }
            $out .= str_repeat('=', 40) . "\n";
            // Use error_log so it appears in PHP dev server output
            error_log($out);
            return true;
        }

        if ($this->mode === 'resend') {
            if (empty($this->resendApiKey)) {
                error_log("Resend API key not configured");
                return false;
            }

            try {
                $client = new Client(['base_uri' => 'https://api.resend.com']);
                
                $payload = [
                    "from" => $this->config['from'] ?? $_ENV['MAIL_FROM'] ?? 'no-reply@example.com',
                    "to" => [$to],
                    "subject" => $subject,
                    "html" => $body,
                ];
    
                // Attachments handling: Resend requires base64 attachments in "attachments" array
                if (!empty($attachments)) {
                    $payload['attachments'] = [];
                    foreach ($attachments as $att) {
                        $payload['attachments'][] = [
                            'filename' => $att['filename'],
                            'content' => base64_encode($att['content']),
                        ];
                    }
                }
    
                $response = $client->post('/emails', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->resendApiKey,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => $payload,
                ]);
    
                return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;

            } catch (\Exception $e) {
                error_log("Resend send error: " . $e->getMessage());
                return false;
            }
        }

        // Fallback: try PHP mail() as a simple SMTP fallback
        if (!empty($attachments)) {
            error_log("Attachments are not supported in 'mail' mode.");
        }
        $headers = "From: " . ($this->config['from'] ?? $_ENV['MAIL_FROM'] ?? 'no-reply@example.com') . "\r\n";
        $ok = mail($to, $subject, $body, $headers);
        if (!$ok) {
            error_log("mail() failed for {$to}");
        }
        return $ok;
    }
}