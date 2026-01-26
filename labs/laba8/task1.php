<table border="1" style="border-collapse: collapse; text-align: center;">
    <?php
    for ($i = 0; $i <= 10; $i++) {
        echo "<tr>";
        for ($j = 0; $j <= 10; $j++) {
            $result = $i * $j;
            echo "<td style='padding: 5px; width: 30px;'>$result</td>";
        }
        echo "</tr>";
    }
    ?>
</table>