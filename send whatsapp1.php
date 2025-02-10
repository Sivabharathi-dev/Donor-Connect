
use Twilio\Rest\Client;

function sendWhatsAppReminder($recipientNumber, $message) {
    
    $accountSid = '--Your twilio account Sid-- '; 
    $authToken = '--Your twilio auth token--';  
    $twilioNumber = 'whatsapp:+YOUR_TWILIO_WHATSAPP_NUMBER';  

    $client = new Client($accountSid, $authToken);

    try {
        $client->messages->create(
            'whatsapp:' . $recipientNumber, 
            [
                'from' => $twilioNumber,
                'body' => $message
            ]
        );
    } catch (Exception $e) {
      
        error_log('Error sending WhatsApp message: ' . $e->getMessage());
    }
}
?>