#include "Config.h"
#include "ServerConnection.h"
#include "Display.h"
#include "HE_Sensor.h"
#include "TempSensor.h"

bool pillTaken[NUM_SLOTS] = {false};
int pillQuantities[NUM_SLOTS] = {0};

unsigned long lastPollTime = 0;
const unsigned long pollInterval = 10000;

void setup() {
    Serial.begin(115200);
    setupSensors();
    setupDisplay();
    setupWiFi();
}

void loop() {
    maintainWiFiConnection();

    unsigned long currentMillis = millis();
    if (currentMillis - lastPollTime >= pollInterval) {
        // Update quantities from server and refresh display
        updateDisplayWithQuantities(pillQuantities);
        lastPollTime = currentMillis;
    }

    // Track pill-taking events
    checkPillsTaken(pillQuantities);

    // Read temperature and send full update
    float temp = getTemperature();
    sendDataToServer(temp, pillTaken, NUM_SLOTS);

    delay(1000); 
    
}