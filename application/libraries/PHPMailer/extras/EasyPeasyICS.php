<?php

class EasyPeasyICS
{
    protected $calendarName;
    protected $events = array();
    public function __construct($calendarName = "")
    {
        $this->calendarName = $calendarName;
    }

    public function addEvent($start, $end, $summary = '', $description = '', $url = '', $uid = '')
    {
        if (empty($uid)) {
            $uid = md5(uniqid(mt_rand(), true)) . '@EasyPeasyICS';
        }
        $event = array(
            'start' => gmdate('Ymd', $start) . 'T' . gmdate('His', $start) . 'Z',
            'end' => gmdate('Ymd', $end) . 'T' . gmdate('His', $end) . 'Z',
            'summary' => $summary,
            'description' => $description,
            'url' => $url,
            'uid' => $uid
        );
        $this->events[] = $event;
        return $event;
    }

    public function getEvents()
    {
        return $this->events;
    }

    public function clearEvents()
    {
        $this->events = array();
    }

    public function getName()
    {
        return $this->calendarName;
    }

    public function setName($name)
    {
        $this->calendarName = $name;
    }

    public function render($output = true)
    {
        $ics = 'BEGIN:VCALENDAR METHOD:PUBLISH VERSION:2.0 X-WR-CALNAME:' . $this->calendarName . ' PRODID:-//hacksw/handcal//NONSGML v1.0//EN';

        foreach ($this->events as $event) {
            $ics .= 'BEGIN:VEVENT UID:' . $event['uid'] . 'DTSTAMP:' . gmdate('Ymd') . 'T' . gmdate('His') . 'Z DTSTART:' . $event['start'] . ' DTEND:' . $event['end'] . 'SUMMARY:' . str_replace("\n", "\\n", $event['summary']) . '
DESCRIPTION:' . str_replace("\n", "\\n", $event['description']) . '
URL;VALUE=URI:' . $event['url'] . '
END:VEVENT';
        }

        $ics .= '
END:VCALENDAR';

        if ($output) {

            $filename = $this->calendarName;

            if (strpos($filename, ' ') !== false) {
                $filename = '"'.$filename.'"';
            }
            header('Content-type: text/calendar; charset=utf-8');
            header('Content-Disposition: inline; filename=' . $filename . '.ics');
            echo $ics;
        }
        return $ics;
    }
}
