#include "ServerConnection.h"
#include "Display.h" // For accessing display object

const char* ssid = "ITMajor";
const char* password = "itmajorcs461";
const char* serverURL = "https://section-three.it313communityprojects.website/src/routes/device/update.php";

// SSL certificate
const char* rootCACertificate = \
"-----BEGIN CERTIFICATE-----\n" \
"MIIEVzCCAj+gAwIBAgIRALBXPpFzlydw27SHyzpFKzgwDQYJKoZIhvcNAQELBQAw\n" \
"TzELMAkGA1UEBhMCVVMxKTAnBgNVBAoTIEludGVybmV0IFNlY3VyaXR5IFJlc2Vh\n" \
"cmNoIEdyb3VwMRUwEwYDVQQDEwxJU1JHIFJvb3QgWDEwHhcNMjQwMzEzMDAwMDAw\n" \
"WhcNMjcwMzEyMjM1OTU5WjAyMQswCQYDVQQGEwJVUzEWMBQGA1UEChMNTGV0J3Mg\n" \
"RW5jcnlwdDELMAkGA1UEAxMCRTYwdjAQBgcqhkjOPQIBBgUrgQQAIgNiAATZ8Z5G\n" \
"h/ghcWCoJuuj+rnq2h25EqfUJtlRFLFhfHWWvyILOR/VvtEKRqotPEoJhC6+QJVV\n" \
"6RlAN2Z17TJOdwRJ+HB7wxjnzvdxEP6sdNgA1O1tHHMWMxCcOrLqbGL0vbijgfgw\n" \
"gfUwDgYDVR0PAQH/BAQDAgGGMB0GA1UdJQQWMBQGCCsGAQUFBwMCBggrBgEFBQcD\n" \
"ATASBgNVHRMBAf8ECDAGAQH/AgEAMB0GA1UdDgQWBBSTJ0aYA6lRaI6Y1sRCSNsj\n" \
"v1iU0jAfBgNVHSMEGDAWgBR5tFnme7bl5AFzgAiIyBpY9umbbjAyBggrBgEFBQcB\n" \
"AQQmMCQwIgYIKwYBBQUHMAKGFmh0dHA6Ly94MS5pLmxlbmNyLm9yZy8wEwYDVR0g\n" \
"BAwwCjAIBgZngQwBAgEwJwYDVR0fBCAwHjAcoBqgGIYWaHR0cDovL3gxLmMubGVu\n" \
"Y3Iub3JnLzANBgkqhkiG9w0BAQsFAAOCAgEAfYt7SiA1sgWGCIpunk46r4AExIRc\n" \
"MxkKgUhNlrrv1B21hOaXN/5miE+LOTbrcmU/M9yvC6MVY730GNFoL8IhJ8j8vrOL\n" \
"pMY22OP6baS1k9YMrtDTlwJHoGby04ThTUeBDksS9RiuHvicZqBedQdIF65pZuhp\n" \
"eDcGBcLiYasQr/EO5gxxtLyTmgsHSOVSBcFOn9lgv7LECPq9i7mfH3mpxgrRKSxH\n" \
"pOoZ0KXMcB+hHuvlklHntvcI0mMMQ0mhYj6qtMFStkF1RpCG3IPdIwpVCQqu8GV7\n" \
"s8ubknRzs+3C/Bm19RFOoiPpDkwvyNfvmQ14XkyqqKK5oZ8zhD32kFRQkxa8uZSu\n" \
"h4aTImFxknu39waBxIRXE4jKxlAmQc4QjFZoq1KmQqQg0J/1JF8RlFvJas1VcjLv\n" \
"YlvUB2t6npO6oQjB3l+PNf0DpQH7iUx3Wz5AjQCi6L25FjyE06q6BZ/QlmtYdl/8\n" \
"ZYao4SRqPEs/6cAiF+Qf5zg2UkaWtDphl1LKMuTNLotvsX99HP69V2faNyegodQ0\n" \
"LyTApr/vT01YPE46vNsDLgK+4cL6TrzC/a4WcmF5SRJ938zrv/duJHLXQIku5v0+\n" \
"EwOy59Hdm0PT/Er/84dDV0CSjdR/2XuZM3kpysSKLgD1cKiDA+IRguODCxfO9cyY\n" \
"Ig46v9mFmBvyH04=\n" \
"-----END CERTIFICATE-----\n";

void setupWiFi() {
    WiFi.setAutoReconnect(true);
    WiFi.persistent(true);
    WiFi.begin(ssid, password);

    Serial.print("Connecting to WiFi");
    int attempts = 0;
    while (WiFi.status() != WL_CONNECTED && attempts < 20) {
        delay(500);
        Serial.print(".");
        attempts++;
    }

    if (WiFi.status() == WL_CONNECTED) {
        Serial.println("\n✅ Connected to WiFi!");
        Serial.print("IP Address: ");
        Serial.println(WiFi.localIP());
    } else {
        Serial.println("\n❌ Failed to connect to WiFi.");
    }
}

void maintainWiFiConnection() {
    if (WiFi.status() != WL_CONNECTED) {
        Serial.println("WiFi disconnected. Reconnecting...");
        WiFi.disconnect();
        WiFi.begin(ssid, password);
    }
}

void sendDataToServer(float temperature, int magnet) {
    if (WiFi.status() == WL_CONNECTED) {
        WiFiClientSecure client;
        client.setCACert(rootCACertificate);

        HTTPClient https;
        if (https.begin(client, serverURL)) {
            https.addHeader("Content-Type", "application/x-www-form-urlencoded");

            String postData = "device_id=1&temp=" + String(temperature) + "&magnet=" + String(magnet);
            int httpResponseCode = https.POST(postData);

            if (httpResponseCode > 0) {
                String response = https.getString();
                Serial.println("✅ Server Response: " + response);
            } else {
                Serial.print("❌ POST Failed. HTTP error: ");
                Serial.println(httpResponseCode);
            }

            https.end();
        } else {
            Serial.println("❌ Unable to connect to server");
        }
    } else {
        Serial.println("❌ WiFi not connected");
    }
}

void checkServerForPillUpdate() {
    if (WiFi.status() == WL_CONNECTED) {
        WiFiClientSecure client;
        client.setCACert(rootCACertificate);

        String fullURL = String(serverURL);
        fullURL.replace("update.php", "get_pill_status.php");
        fullURL += "?device_id=1";

        HTTPClient https;
        if (https.begin(client, fullURL)) {
            int httpCode = https.GET();
            if (httpCode > 0) {
                String payload = https.getString();
                Serial.println("📥 Server Poll Response: " + payload);

                // Basic JSON parsing to extract pill count
                int index = payload.indexOf("pills_remaining");
                if (index != -1) {
                    int valueStart = payload.indexOf(":", index) + 1;
                    int valueEnd = payload.indexOf("}", valueStart);
                    int pills = payload.substring(valueStart, valueEnd).toInt();
                    
                    // You can pass this to a display function
                    Serial.println("💊 Pills remaining: " + String(pills));
                }

            } else {
                Serial.print("❌ GET failed, code: ");
                Serial.println(httpCode);
            }

            https.end();
        } else {
            Serial.println("❌ HTTPS begin failed");
        }
    } else {
        Serial.println("❌ WiFi not connected");
    }
}