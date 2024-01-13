# ICS PHP Class

## Overview
The `ICS` class, developed by Tim David Saxen (https://www.nezmal.de, ds@netzmal.de), is a PHP utility for creating iCalendar (ICS) files. This class facilitates the addition of multiple events to an ICS file, with support for various event properties such as description, start and end dates, location, summary, URL, and timezone.

## Features
- Create ICS files with multiple events.
- Customize events with descriptions, date-times, locations, summaries, and more.
- Support for individual event timezones.
- Easy to use and integrate into existing PHP applications.

## Requirements
- PHP 5.4 or higher.

## Installation
Simply include the `ICS.php` file in your PHP project:
```php
require_once 'path/to/ICS.php';
$ics = new ICS(['timezone' => 'America/New_York']);
$ics->addEvent([
    'description' => 'This is a description for an event.',
    'dtstart' => '2024-01-12 10:00:00',
    'dtend' => '2024-01-12 12:00:00',
    'location' => '123 Main St, Anytown, USA',
    'summary' => 'Event Summary',
    'url' => 'http://example.com',
    'timezone' => 'Europe/Berlin', // Optional: Specific timezone for this event
    'uid' => '1'
]);
echo $ics->build();
```

## Documentation
For detailed information about all available methods and their usage, please refer to the inline comments within the `ICS` class file.

## Contribution
Feel free to fork, modify, and make pull requests to this repository. For any major changes, please open an issue first to discuss what you would like to change.

## License
This project is open-sourced under the MIT License.

