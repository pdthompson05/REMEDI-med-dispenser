#ifndef DISPLAY_H
#define DISPLAY_H

#include <MD_Parola.h>
#include <MD_MAX72XX.h>
#include <SPI.h>
#include <Arduino.h>

#define DATA_PIN   13
#define CLK_PIN    3
#define CS_PIN     15
#define MAX_DEVICES 4
#define HARDWARE_TYPE MD_MAX72XX::FC16_HW

MD_Parola display = MD_Parola(HARDWARE_TYPE, DATA_PIN, CLK_PIN, CS_PIN, MAX_DEVICES);

void setupDisplay() {
    display.begin();
    display.setIntensity(5);
    display.displayClear();
}

void displayPillStatus() {
    if (pillTaken) {
        display.displayClear();
        display.displayText("Pill Taken!", PA_CENTER, 100, 1000, PA_SCROLL_LEFT, PA_SCROLL_LEFT);
        display.displayAnimate();
    }
}

#endif
