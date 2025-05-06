#ifndef HE_SENSOR_H
#define HE_SENSOR_H


const int hallSensorPin = 18;
bool pillTaken = false;

unsigned long openTime = 0;
bool slotPreviouslyOpen = false;

void setupSensors() {
    pinMode(hallSensorPin, INPUT_PULLUP);
}

void checkPillTaken() {
    int sensorState = digitalRead(hallSensorPin);

    if (sensorState == HIGH) {
        if (!slotPreviouslyOpen) {
            // Slot just opened
            openTime = millis();
            slotPreviouslyOpen = true;
        } else {
            // Still open, check duration
            if (millis() - openTime > 3000) { // 3 seconds
                if (!pillTaken) {
                    pillTaken = true;
                    Serial.println("✅ Pill taken confirmed (open > 3s)");
                }
            }
        }
    } else {
        // Slot closed
        slotPreviouslyOpen = false;
        openTime = 0;
        pillTaken = false; // Reset for next detection cycle
    }
}

#endif // HE_SENSOR_H