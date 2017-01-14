# Remeha Boiler connectivity using ESP8266
Connect ESP8266 directly to Remeha CV/Boiler to read data using PHP.

The current PHP code uses an Adafruit Huzzah ESP8266 to connect to and read data from a Remeha Calenta 40C boiler. The Huzzah ESP8266 was chosen as it is a 5VDC device and does not require any level shifter or additional circuits to deal with the higher voltage. It connect to the Remeha X13 connector using a 4P4C (RJ10) connector with the following pinouts:
1. Remeha............>ESP8266
2. Pin1 (GND)........>GND
3. Pin2 (RX).........>TX
4. Pin3 (TX).........>RX
5. Pin4 (VDC)........>VCC+

The ESP8266 is running and has been tested with the ESP-Link firmware/software loaded (also on GitHub)

Currently this is a 'read only' script and provides the following functionality:

1. Connects ESP to Calenta
2. Sends Hex to Calenta and reads the response (has a CRC check and discards invalid data)
3. Maps response to more 'logical' variables
4. Translates various 'bits' to provide correct messages
5. Writes information received to Domoticz server using cURL

To do:
1. Add code to collect other parameters from the Calenta (e.g. burner hours, pump hours, etc.) 
2. Add code to adjust/change parameters (e.g. pump running time, control setpoint, etc.)
3. ...basically provide similar/same functionality as the Remeha Recom software - but using a cheap ESP8266 and web interface.
