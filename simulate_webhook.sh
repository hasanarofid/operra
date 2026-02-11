#!/bin/bash

# Configuration
# Configuration
URL="http://127.0.0.1:8000/api/webhook/whatsapp"
PHONE_NUMBER_ID="62881026697527"
SENDER_NUMBER="628123456789"
MESSAGE="Halo, ini adalah simulasi pesan dari Antigravity @ $(date)"

# JSON Payload (Meta Cloud API Format)
PAYLOAD=$(cat <<EOF
{
  "object": "whatsapp_business_account",
  "entry": [
    {
      "id": "123456789",
      "changes": [
        {
          "value": {
            "messaging_product": "whatsapp",
            "metadata": {
              "display_phone_number": "OPERRA_DEMO",
              "phone_number_id": "$PHONE_NUMBER_ID"
            },
            "contacts": [
              {
                "profile": { "name": "Simulation Tester" },
                "wa_id": "$SENDER_NUMBER"
              }
            ],
            "messages": [
              {
                "from": "$SENDER_NUMBER",
                "id": "wamid.simulated_$(date +%s)",
                "timestamp": "$(date +%s)",
                "text": { "body": "$MESSAGE" },
                "type": "text"
              }
            ]
          },
          "field": "messages"
        }
      ]
    }
  ]
}
EOF
)

# Execute request
echo "Sending simulated webhook to $URL..."
curl -X POST "$URL" \
     -H "Content-Type: application/json" \
     -d "$PAYLOAD"

echo -e "\n\nSimulation complete."
