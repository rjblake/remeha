# Remeha Boiler connectivity using ESP8266
Connect ESP8266 directly to Remeha CV/Boiler to read data using PHP.

The current PHP code uses an Adafruit Huzzah ESP8266 to connect to and read data from a Remeha Calenta 40C boiler. The Huzzah ESP8266 was chosen as it is a 5VDC device and does not require any level shifter or additional circuits to deal with the higher voltage. It connect to the Remeha X13 connector using a 4P4C (RJ10) connector with the following pinouts:

Remeha >>>>>>>>>> ESP8266
1. Pin1 (GND)........>GND
2. Pin2 (RX).........>TX
3. Pin3 (TX).........>RX
4. Pin4 (VDC)........>VCC+

The ESP8266 is running and has been tested with the ESP-Link firmware/software loaded (also on GitHub)

Currently this is a 'read only' script and provides the following functionality:

1. Connects ESP to Calenta
2. Sends Hex to Calenta and reads the responses (for "Sample Data", "Counter Data" and "Parameters")
3. Maps response to more 'logical' variables
4. Translates various 'bits' to provide correct messages
5. Writes information received to Domoticz server using cURL
6. The calenta-xxxxx.php scripts can be run from within a browser, while the calenta.php script is best run as a daemon or in the background

To do:

1. Add code to collect other details from the Calenta (e.g. Identification (DONE), Error logs, etc.) 
2. Add code to adjust/change parameters (e.g. pump running time, control setpoint, etc.)
3. ...basically provide similar/same functionality as the Remeha Recom software - but using a cheap ESP8266 and web interface.
4. Tidy up and optimise code :) - the code is certainly not 'pretty' or optimised in anyway. There are blocks that could be rewritten to better make use of the arrays, global variables, etc. but as this grew from a simple "I want some info" to something more it grew organically without any proper design considerations. Also, it is a simple script and doesn't take long to run on even the slowest processor, so the effort to rewrite is likely not worth any gain...and almost everybody should be able to understand it without the need for classes, foreach array loops, etc. But, the winter nights are long and I might just fill my time with rewriting it all ;)
