// ===== File: PollServer.h =====
#ifndef POLL_SERVER_H
#define POLL_SERVER_H

#include <WiFiClientSecure.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>

// Use your actual server URL here
const char* scheduleURL = "https://section-three.it313communityprojects.website/src/routes/device/schedule.php?device_id=123"; 

extern MD_Parola display;  // Reference display from Display.h

void pollServerForSchedule() {
  if (WiFi.status() == WL_CONNECTED) {
    WiFiClientSecure client;
    client.setCACert(rootCACertificate); // Ensure this cert is available globally or passed in

    HTTPClient https;
    if (https.begin(client, scheduleURL)) {
      int httpCode = https.GET();
      if (httpCode == 200) {
        String payload = https.getString();

        StaticJsonDocument<200> doc;
        DeserializationError error = deserializeJson(doc, payload);

        if (!error && doc["status"] == "success") {
          int pillsDue = doc["pills_due"];
          Serial.print("Pills due: ");
          Serial.println(pillsDue);

          display.displayClear();
          display.displayScroll(("Pills: " + String(pillsDue)).c_str(), PA_CENTER, PA_SCROLL_LEFT, 100);
          while (!display.displayAnimate()) delay(50);
        } else {
          Serial.println("JSON parsing failed or status != success");
        }
      } else {
        Serial.print("GET failed: ");
        Serial.println(httpCode);
      }
      https.end();
    } else {
      Serial.println("HTTPS begin failed");
    }
  } else {
    Serial.println("WiFi not connected");
  }
}

#endif