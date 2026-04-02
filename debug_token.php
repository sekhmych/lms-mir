<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$t = Dcblogdev\MsGraph\Models\MsGraphToken::first();
if (!$t) {
    echo "No tokens found\n";
    exit;
}

echo "user_id: " . $t->user_id . "\n";
echo "email: " . $t->email . "\n";
echo "expires: " . $t->expires . "\n";
echo "now: " . time() . "\n";
echo "valid: " . ($t->expires > time() ? 'yes' : 'no') . "\n";
echo "access_len: " . strlen($t->access_token) . "\n";
echo "refresh_len: " . strlen($t->refresh_token) . "\n";

// Decode JWT payload to see scopes
$parts = explode('.', $t->access_token);
if (count($parts) >= 2) {
    $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
    echo "token_scp: " . ($payload['scp'] ?? 'NONE') . "\n";
    echo "token_aud: " . ($payload['aud'] ?? 'NONE') . "\n";
    echo "token_tid: " . ($payload['tid'] ?? 'NONE') . "\n";
    echo "token_idp: " . ($payload['idp'] ?? 'NONE') . "\n";
    echo "token_upn: " . ($payload['upn'] ?? 'NONE') . "\n";
    echo "token_unique_name: " . ($payload['unique_name'] ?? 'NONE') . "\n";
    echo "token_wids: " . json_encode($payload['wids'] ?? []) . "\n";
}

// Try to get access token directly
$msgraph = new Dcblogdev\MsGraph\MsGraph();
$user = App\Models\User::find($t->user_id);
Dcblogdev\MsGraph\MsGraph::login($user);

$accessToken = $msgraph->getAccessToken($t->user_id);

if ($accessToken instanceof \Illuminate\Http\RedirectResponse) {
    echo "ERROR: getAccessToken returned RedirectResponse!\n";
} else {
    echo "access_token_type: " . gettype($accessToken) . "\n";
    echo "token_matches_stored: " . ($accessToken === $t->access_token ? 'yes' : 'no') . "\n";

    // Try actual API call
    try {
        $response = $msgraph->get('me', [], [], $user->id);
        echo "API /me result: " . json_encode($response) . "\n";
    } catch (Exception $e) {
        echo "API /me error: " . $e->getMessage() . "\n";
    }

    // Try creating a calendar event with full error details
    try {
        $client = new \GuzzleHttp\Client();
        $eventData = [
            'subject' => 'Test Event',
            'start' => ['dateTime' => '2026-04-10T09:00:00', 'timeZone' => 'Asia/Almaty'],
            'end' => ['dateTime' => '2026-04-10T10:00:00', 'timeZone' => 'Asia/Almaty'],
        ];
        $res = $client->post('https://graph.microsoft.com/v1.0/me/events', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ],
            'json' => $eventData,
        ]);
        echo "Calendar event created: " . $res->getBody()->getContents() . "\n";
    } catch (\GuzzleHttp\Exception\ClientException $e) {
        echo "Calendar STATUS: " . $e->getResponse()->getStatusCode() . "\n";
        echo "Calendar HEADERS:\n";
        foreach ($e->getResponse()->getHeaders() as $name => $values) {
            echo "  $name: " . implode(', ', $values) . "\n";
        }
        echo "Calendar BODY: " . $e->getResponse()->getBody()->getContents() . "\n";
    } catch (Exception $e) {
        echo "Calendar event error: " . $e->getMessage() . "\n";
    }
}
