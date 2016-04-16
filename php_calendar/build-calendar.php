<!DOCTYPE html>
<html>
<head>
  <title></title>

  <style>
    /* calendar */
    table.calendar { border-left: 1px solid #999; }
    tr.calendar-row {  }
    td.calendar-day { min-height:80px; font-size:11px; position:relative; } * html div.calendar-day { height:80px; }
    td.calendar-day:hover { background:#eceff5; }
    td.calendar-day-np  { background:#eee; min-height:80px; } * html div.calendar-day-np { height:80px; }
    td.calendar-day-head { background:#ccc; font-weight:bold; text-align:center; width:120px; padding:5px; border-bottom:1px solid #999; border-top:1px solid #999; border-right:1px solid #999; }
    div.day-number    { background:#999; padding:5px; color:#fff; font-weight:bold; float:right; margin:-5px -5px 0 0; width:20px; text-align:center; }
    
    /* shared */
    td.calendar-day, td.calendar-day-np { width:120px; padding:5px; border-bottom:1px solid #999; border-right:1px solid #999; }
  </style>
  
</head>
<body>

<?php

/** 
 * Draws a calendar.
 * 
 * Example usage:
 *   echo "<h2>June 2015</h2>";
 *   echo draw_calendar(6, 2015);
 * 
 * Example usage (with events):
 *   $events = [
 *     5 => [
 *       'text' => "An event for the 5 july 2015",
 *       'href' => "http://example.com/link/to/event"
 *     ],
 *     23 => [
 *       'text' => "An event for the 23 july 2015",
 *       'href' => "/link/to/event"
 *     ],
 *   ];
 *   echo "<h2>July 2015</h2>";
 *   echo draw_calendar(7, 2015, $events);
 *
 * 
 * References:
 *   - http://davidwalsh.name/php-calendar
 * 
 * @param $month (Integer) The month, e.g. 7.
 * @param $year (Integer) The year, e.g. 2015.
 * @param $events (Array) An array of events where the key is the day's event,
 * the value is an array with 'text' and 'link'.
 * @return (String) The calendar's html.
 */
function draw_calendar($month, $year, $events) {

  // CSS classes
  $css_cal = 'calendar';
  $css_cal_row = 'calendar-row';
  $css_cal_day_head = 'calendar-day-head';
  $css_cal_day = 'calendar-day';
  $css_cal_day_number = 'day-number';
  $css_cal_day_blank = 'calendar-day-np';
  $css_cal_day_event = 'calendar-day-event';
  $css_cal_event = 'calendar-event';

  // Table headings
  $headings = ['M', 'T', 'W', 'T', 'F', 'S', 'S'];

  // Start: draw table
  $calendar = 
    "<table cellpadding='0' cellspacing='0' class='{$css_cal}'>";
  $calendar .= 
    "<tr class='{$css_cal_row}'>" .
    "<td class='{$css_cal_day_head}'>" .
    implode("</td><td class='{$css_cal_day_head}'>", $headings) .
    "</td>" .
    "</tr>";

  // Days and weeks vars now
  $running_day = date('N', mktime(0, 0, 0, $month, 1, $year));
  $days_in_month = date('t', mktime(0, 0, 0, $month, 1, $year));
  $days_in_this_week = 1;
  $day_counter = 0;
  $dates_array = [];

  // Row for week one
  $calendar .= "<tr class='{$css_cal_row}'>";

  // Print "blank" days until the first of the current week
  for ($x = 1; $x < $running_day; $x++) {
    $calendar .= "<td class='${css_cal_day_blank}'> </td>";
    $days_in_this_week++;
  }

  // Keep going with days...
  for ($day = 1; $day <= $days_in_month; $day++) {
    
    // Check if there is an event today
    $day_event = false;
    if (isset($events) && isset($events[$day])) {
      $day_event = true;
    }

    // Day cell
    $calendar .= $day_event ? 
      "<td class='{$css_cal_day} {$css_cal_day_event}'>" :
      "<td class='{$css_cal_day}'>";
    
    // Add the day number
    $calendar .= "<div class='{$css_cal_day_number}'>" . $day . "</div>";

    // Insert an event for this day
    if ($day_event) {
      $calendar .= 
        "<div class='{$css_cal_event}'>" .
        "<a href='{$events[$day]['href']}'>{$events[$day]['text']}</a>" .
        "</div>";
    }

    // New row
    $calendar .= "</td>";
    if ($running_day == 7) {
      $calendar .= "</tr>";
      if (($day_counter + 1) != $days_in_month) {
        $calendar .= "<tr class='{$css_cal_row}'>";
      }
      $running_day = 0;
      $days_in_this_week = 0;
    }
    
    $days_in_this_week++;
    $running_day++;
    $day_counter++;
  
  } // for $day

  // Finish the rest of the days in the week
  if ($days_in_this_week < 8) {
    for ($x = 1; $x <= (8 - $days_in_this_week); $x++) {
      $calendar .= "<td class='{$css_cal_day_blank}'> </td>";
    }
  }

  // Final row
  $calendar .= "</tr>";

  // End the table
  $calendar .= '</table>';
  
  // All done, return result
  return $calendar;
} // function draw_calendar

// // Sample usages
// echo '<h2>June 2015</h2>';
// echo draw_calendar(6, 2015);

// Sample usages with events
$events = [
 5 => [
   'text' => "An event for the 5 july 2015",
   'href' => "http://example.com/link/to/event"
 ],
 23 => [
   'text' => "An event for the 23 july 2015",
   'href' => "/link/to/event"
 ],
];
echo "<h2>July 2015</h2>";
echo draw_calendar(7, 2015, $events);

?>

</body>
</html>

