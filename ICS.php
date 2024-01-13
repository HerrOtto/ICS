<?php

/**
 * Class ICS v2024-01-13
 * Tim David Saxen; https://www.nezmal.de; ds@netzmal.de; MIT License
 * 
 * This PHP class is used for creating ICS (iCalendar) files.
 * It allows adding multiple events with various properties such as
 * description, start and end date, location, summary, URL, and timezone.
 *
 */

class ICS {

    // Constant to define the date format in ICS files.
    const DT_FORMAT = 'Ymd\THis\Z';

    // Array to store the events
    protected $events = [];

    // Default timezone for the events
    protected $defaultTimezone = 'UTC';

    // List of allowed properties for each event.
    private $available_eventProperties = array(
        'description',
        'dtend',
        'dtstart',
        'location',
        'summary',
        'url',
        'uid', // Unique ID of this event
        'timezone' // Include timezone in the list of available properties
    );

    /**
     * Constructor to initialize the ICS object.
     * @param array $props Properties to set upon object creation.
     */
    public function __construct($props = []) {
        if (isset($props['timezone'])) {
            $this->defaultTimezone = $props['timezone'];
        }
    }

    /**
     * Adds an event to the ICS.
     * @param array $props Associative array of event properties.
     * @return bool Returns true if the event is added successfully or false on failure.
     */
    public function addEvent($props) {
        $event = [];
        // Loop through each provided property and add to event if valid.
        foreach ($props as $key => $value) {
            if (!in_array($key, $this->available_eventProperties)) {
                continue; // Skip non-available properties
            }
            if (in_array($key, ['dtstart', 'dtend', 'dtstamp'])) {
                // Format timestamp for date-related fields
                $formattedTimestamp = $this->formatTimestamp($value, $props['timezone'] ?? $this->defaultTimezone);
                if ($formattedTimestamp === false) {
                    // Consider logging the error here
                    return false;
                }
                $event[$key] = $formattedTimestamp;
            } else {
                // Escape strings for other fields
                $event[$key] = $this->escapeString($value);
            }
        }
        // Set default timezone and uid if not provided
        $event['timezone'] = $event['timezone'] ?? $this->defaultTimezone;
        $event['uid'] = $event['uid'] ?? uniqid();
        // Add the event to the events array.
        $this->events[] = $event;
        return true;
    }

    /**
     * Builds the ICS properties for all events.
     * @return array An array of ICS file lines.
     */
    public function build() {
        // Array to hold ICS file lines, starting with the header.
        $ics_props = array(
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//hacksw/handcal//NONSGML v1.0//EN',
            'CALSCALE:GREGORIAN',
        );

        // Process each event and add its details to the ICS file.
        foreach ($this->events as $event) {
            $ics_props[] = 'BEGIN:VEVENT';
            // Add each property of the event to the ICS content.
            foreach ($event as $k => $v) {
                if ($k == 'timezone') {
                    // Exclude timezone from direct ICS output
                    continue;
                } else {
                    if (in_array($k, ['dtstart', 'dtend', 'dtstamp'])) {
                        // Format timestamp with timezone
                        $v = $this->formatTimestamp($v, $event['timezone']);
                    }
                    $ics_props[] = strtoupper($k) . ':' . $v;
                }
            }
            // Add default timestamp and unique ID for the event.
            $ics_props[] = 'DTSTAMP:' . $this->formatTimestamp('now', 'UTC');
            $ics_props[] = 'END:VEVENT';
        }

        // Add footer to the ICS file.
        $ics_props[] = 'END:VCALENDAR';

        return implode("\n", $ics_props);
    }

    /**
     * Formats a timestamp, converting it to UTC.
     * @param string $timestamp The timestamp to format.
     * @param string $timezone The timezone of the timestamp.
     * @return string|false The formatted timestamp in UTC or false on failure.
     */
    private function formatTimestamp($timestamp, $timezone) {
        try {
            // Create DateTime object with the specified timezone.
            $dt = new DateTime($timestamp, new DateTimeZone($timezone));

            // Convert the time to UTC.
            $dt->setTimeZone(new DateTimeZone('UTC'));

            // Return the formatted date in UTC.
            return $dt->format(self::DT_FORMAT);
        } catch (Exception $e) {
            // Return false if there's an error in date formatting.
            return false;
        }
    }

    /**
     * Escapes special characters in a string.
     * @param string $str The string to escape.
     * @return string The escaped string.
     */
    private function escapeString($str) {
        // Replace special characters with escaped versions.
        $str = str_replace("\\", "\\\\", $str); // Escape backslashes
        $str = str_replace("\n", "\\n", $str);  // Escape newlines
        $str = str_replace(",", "\\,", $str);    // Escape commas
        $str = str_replace(";", "\\;", $str);    // Escape semicolons

        return $str;
    }
}
