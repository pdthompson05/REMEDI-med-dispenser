#ifndef SERVER_CONNECTION_H
#define SERVER_CONNECTION_H

#include <WiFi.h>
#include <WiFiClientSecure.h>
#include <HTTPClient.h>
#include "Config.h"

extern const char* ssid;
extern const char* password;
extern const char* serverURL;
extern const char* rootCACertificate;

void setupWiFi();
void maintainWiFiConnection();
void sendDataToServer(float temperature, bool pillTaken[], int numSlots);
void getPillQuantitiesFromServer(int quantities[]);

#endif