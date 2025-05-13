#include <WiFi.h>
#include <HTTPClient.h>
#include <WiFiClientSecure.h>
#include "secrets.h"  // includes ssid, password, rootCACertificate

const char* serverURL = "https://section-three.it313communityprojects.website/src/routes/device/new_status.php";

// Example values
int device_id = 1;
float temp = 23.7;

void setup() {
  Serial.begin(115200);
  setupWiFi();
  sendStatusToServer(temp, device_id);
}

void loop() {
  // Optionally repeat every X seconds
  delay(60000); // every 60 seconds
  sendStatusToServer(temp, device_id);
}

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

void sendStatusToServer(float temperature, int deviceId) {
  if (WiFi.status() == WL_CONNECTED) {
    WiFiClientSecure client;
    client.setCACert(rootCACertificate);

    HTTPClient https;
    if (https.begin(client, serverURL)) {
      https.addHeader("Content-Type", "application/x-www-form-urlencoded");

      String postData = "device_id=" + String(deviceId) + "&temp=" + String(temperature, 2);

      int httpCode = https.POST(postData);

      if (httpCode > 0) {
        String response = https.getString();
        Serial.println("✅ Server Response: " + response);
      } else {
        Serial.printf("❌ POST failed. HTTP error: %s\n", https.errorToString(httpCode).c_str());
      }

      https.end();
    } else {
      Serial.println("❌ HTTPS begin failed");
    }
  } else {
    Serial.println("❌ WiFi not connected");
  }
}