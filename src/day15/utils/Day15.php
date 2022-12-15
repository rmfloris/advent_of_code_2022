<?php

namespace day15\utils;
use common\LoadInput;

class Day15 {

    private array $inputData;
    private array $map;
    private array $count = [];

    public function __construct($filename) {
        $this->inputData = $this->parseData($filename);
        $this->createMap();
        // $this->fillMap();
    }

    private function parseData($filename) {
        return (new LoadInput)->loadFileToLines($filename);
    }

    private function fillMap() {
        foreach($this->sensor as $position => $sensor) {
            $distance = $sensor["distance"];
            [$xPos, $yPos] = json_decode($position);
            // echo $distance;

            $yCorrection = 0;
            $max = false;
            for($x=$xPos-$distance; $x<=$xPos+$distance+1;$x++) {
                for($y=$yPos-$yCorrection;$y<=$yPos+$yCorrection;$y++) {
                    $key = $this->getKey($x, $y);
                    if(!isset($this->map[$key])) {
                        // $this->map[$key] = "#";
                        (isset($this->count[$y]) ? $this->count[$y] += 1 : $this->count[$y] = 1);
                    }
                }
                ($max ? $yCorrection-- : $yCorrection++);
                if($yCorrection >= $distance) {
                    $max = true;
                }
            }
        }
    }

    private function createMap() {
        foreach($this->inputData as $line) {
            preg_match_all("#([+-])?(\d+)#", $line, $positions);

            $keySensor = $this->getKey($positions[0][0], $positions[0][1]);
            $keyBeacon = $this->getKey($positions[0][2], $positions[0][3]);
            $this->map[$keySensor] = "S";
            $this->map[$keyBeacon] = "B";
            $this->sensor[$keySensor] = array(
                "x" => $positions[0][0],
                "y" => $positions[0][1],
                "distance" => $this->calculateDistance($keySensor, $keyBeacon),
                "beacon" => $keyBeacon
            );
        }
    }

    private function calculateDistance($position1, $position2) {
        [$x1, $y1] = json_decode($position1);
        [$x2, $y2] = json_decode($position2);
        return abs($x1 - $x2) + abs($y1 - $y2);
    }

    // public function printGraph() {
    //     $table = "<table>";
    //     $table .= "<tr><th></th>";
    //     foreach(range(-2, 25) as $header) {
    //         $table .= "<th>". $header ."</th>";
    //     }
    //     $table .= "</tr>";
        
    //     for($y = 0; $y <= 22; $y++) {
    //         $table .= "<tr>";
    //         $table .= "<td>". $y ."</td>";
    //         for($x = -2; $x <= 25; $x++) {
    //             $value = $this->map[$this->getKey($x, $y)] ?? ".";
    //             $table .= "<td>". $value ."</td>";
    //         }
    //     }
    //     return $table;
    // }

    private function getKey($x, $y) {
        return json_encode([(string)$x, (string)$y]);
    }

    public function getPositionsAt($row) {
        return $this->fillRow($row);
        // return $this->count[$row];
    }

    private function fillRow($row) {
        $score = 0;

        foreach($this->sensor as $key => $sensorData) {
            ["x" => $x, "y" => $y, "distance" => $distance] = $sensorData;

            if($y >= ($row-$distance) && $y <= ($row+$distance) ) {
                $numberOfYPositions = (($distance - abs($row-$y))*2)+1;
                $adjustX = $numberOfYPositions / 2;
                echo $key ." - ". $x ." - ". $y ." - ". $distance ." - " .$numberOfYPositions ."<br>";
                
                for($i=$x-$adjustX; $i<=$x+$adjustX;$i++){
                    // echo "i: ". $i ."<br>";
                    $key = $this->getKey($i, $row);
                    // if(!isset($this->map[$key])) {
                        // $this->map[$key] = "#";
                        $score++;
                    // }
                }
                
            }
        }
        return $score;
    }
}