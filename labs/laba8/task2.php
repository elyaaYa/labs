<?php
function draw_calendar($month = null, $year = null) {
    $month = $month ?? date('n');
    $year = $year ?? date('Y');

    
    $holidays = ['1.1', '7.1', '23.2', '8.3', '1.5', '9.5', '12.6', '4.11'];

    $firstDay = new DateTime("$year-$month-01");
    $daysInMonth = $firstDay->format('t');
    $startWeekday = $firstDay->format('N');

    echo "<h3>" . $firstDay->format('F Y') . "</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>
            <tr style='background: #eee;'>
                <td>Пн</td><td>Вт</td><td>Ср</td><td>Чт</td><td>Пт</td>
                <td style='color:red'>Сб</td><td style='color:red'>Вс</td>
            </tr><tr>";

    
    for ($x = 1; $x < $startWeekday; $x++) echo "<td></td>";

    for ($day = 1; $day <= $daysInMonth; $day++) {
        $currentDate = new DateTime("$year-$month-$day");
        $weekday = $currentDate->format('N');
        $isHoliday = in_array("$day.$month", $holidays);
        
        $style = "";
        if ($weekday >= 6 || $isHoliday) $style = "style='color: red; font-weight: bold;'";

        echo "<td $style>$day</td>";

        if ($currentDate->format('N') == 7) echo "</tr><tr>";
    }

    echo "</tr></table>";
}

draw_calendar();
?>