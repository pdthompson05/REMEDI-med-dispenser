#ifndef HE_SENSOR_H
#define HE_SENSOR_H

#include "Config.h"
#include "Display.h"

extern bool pillTaken[NUM_SLOTS]; // Declared in .ino

const int hallSensorPins[NUM_SLOTS] = {19, 23, 5, 26}; // GPIOs for slots

unsigned long openTime[NUM_SLOTS] = {0};
bool slotPreviouslyOpen[NUM_SLOTS] = {false};

void setupSensors() {
    for (int i = 0; i < NUM_SLOTS; i++) {
        pinMode(hallSensorPins[i], INPUT_PULLUP); // Prevent floating pin issues
    }
}

void checkPillsTaken(int pillQuantities[]) {
    for (int i = 0; i < NUM_SLOTS; i++) {
        int sensorState = digitalRead(hallSensorPins[i]);

        // Debug output to monitor state
        Serial.printf("Slot %d sensor = %s\n", i, sensorState == HIGH ? "HIGH" : "LOW");

        // LOW = magnet present = slot closed
        // HIGH = magnet absent = slot open
        if (sensorState == LOW) {
            // Magnet is present, so slot is closed
            slotPreviouslyOpen[i] = false;
            openTime[i] = 0;
        } else {
            // Magnet is removed (slot opened)
            if (!slotPreviouslyOpen[i]) {
                openTime[i] = millis();
                slotPreviouslyOpen[i] = true;
            } else if (millis() - openTime[i] > 3000 && !pillTaken[i]) {
                pillTaken[i] = true;
                Serial.printf("Pill taken in slot %d (open > 3s)\n", i);
                showPillTakenMessage();
                updateDisplayWithQuantities(pillQuantities);
            }
        }
    }
}

#endif // HE_SENSOR_H