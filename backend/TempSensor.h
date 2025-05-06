#ifndef TEMP_SENSOR_H
#define TEMP_SENSOR_H

#include <Arduino.h>

#define TMP36_PIN 4

// TMP36 properties: 500mV at 25°C and 20mV/°C
#define TMP36_VREF 5.0  // Voltage reference for Arduino (5V)
#define TMP36_VOFFSET 0.5  // 500mV offset at 25°C
#define TMP36_SLOPE 0.02  // 20mV/°C

void setupTempSensor() {
    pinMode(TMP36_PIN, INPUT);
}

float getTemperature() {
    // Read the analog value from the TMP36 sensor (0-1023 range)
    int analogValue = analogRead(TMP36_PIN);

    // Convert the analog value to a voltage
    float voltage = analogValue * (TMP36_VREF / 1023.0);

    // Convert the voltage to temperature
    float temperature = (voltage - TMP36_VOFFSET) / TMP36_SLOPE;

    return temperature;
}

#endif