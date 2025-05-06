#ifndef SERVER_CONNECTION_H
#define SERVER_CONNECTION_H

#include <WiFi.h>
#include <WiFiClientSecure.h>
#include <HTTPClient.h>

extern const char* ssid;
extern const char* password;
extern const char* serverURL;
extern const char* rootCACertificate;


void setupWiFi();
void maintainWiFiConnection();
void sendDataToServer(float temperature, int magnet);
void checkServerForPillUpdate();

#endif