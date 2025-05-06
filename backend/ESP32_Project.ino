#include "ServerConnection.h"
#include "Display.h"
#include "HE_Sensor.h"
#include "TempSensor.h"

unsigned long lastPollTime = 0; // To track last server poll time
const unsigned long pollInterval = 10000; // 10 seconds interval for polling the server

void setup() {
    Serial.begin(115200);
    setupSensors();
    setupDisplay();
    setupWiFi();
}

void loop() {
    maintainWiFiConnection();
    // Check if it's time to poll the server for updates
    unsigned long currentMillis = millis();
    if (currentMillis - lastPollTime >= pollInterval) {
        // It's time to poll, so check for updates
        checkServerForPillUpdate();
        lastPollTime = currentMillis; // Update last poll time
    }

    // Continue with other tasks in the loop
    checkPillTaken(); // Check pill taken status
    displayPillStatus(); // Display pill status
    float temp = getTemperature(); // Read temperature from sensor
    sendDataToServer(temp, (pillTaken ? 1 : 0)); // Send data to the server
    delay(1000); // Small delay to allow for smoother execution
}