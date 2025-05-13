#include "Display.h"

MD_Parola display = MD_Parola(HARDWARE_TYPE, DATA_PIN, CLK_PIN, CS_PIN, MAX_DEVICES);

void setupDisplay() {
    display.begin();
    display.setIntensity(5);
    display.displayClear();
    display.setTextAlignment(PA_LEFT);
}

void showPillTakenMessage() {
    display.displayClear();
    display.displayText("Pill Taken!", PA_CENTER, 100, 2000, PA_SCROLL_LEFT, PA_SCROLL_LEFT);
    while (!display.displayAnimate()) {}  // Wait for scroll animation to finish
    display.displayClear();
    display.setTextAlignment(PA_LEFT);
}

void updateDisplayWithQuantities(int pillQuantities[]) {
    display.displayClear();

    char buf[MAX_DEVICES + 1];
    for (int i = 0; i < MAX_DEVICES; i++) {
        int q = pillQuantities[i];
        buf[i] = (q >= 0 && q <= 9) ? ('0' + q) : '-';
    }
    buf[MAX_DEVICES] = '\0';

    display.setTextAlignment(PA_LEFT);
    display.print(buf);
}
