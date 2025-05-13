#ifndef DISPLAY_H
#define DISPLAY_H

#include <MD_Parola.h>
#include <MD_MAX72XX.h>
#include <SPI.h>
#include <Arduino.h>
#include "Config.h"

#define DATA_PIN   14
#define CLK_PIN    18
#define CS_PIN     17
#define MAX_DEVICES 4
#define HARDWARE_TYPE MD_MAX72XX::FC16_HW

extern MD_Parola display;

void setupDisplay();
void showPillTakenMessage();
void updateDisplayWithQuantities(int pillQuantities[]);

#endif