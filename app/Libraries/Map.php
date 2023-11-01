<?php
namespace App\Libraries;

class Map {

    /**
     * Generates a map for the postcode location and caches it for repeat use
     * @param $postcode the postcode to show
     */
    public static function get($postcode) {
        $postcode = strtolower(str_replace([' ', '%20', '.'], '', $postcode));
        $path = "/files/mapCache";
        if (!file_exists("$path/$postcode.png")) {
            $mapURL = "https://maps.googleapis.com/maps/api/staticmap?center=$postcode&zoom=15&size=180x120&scale=2&maptype=roadmap&sensor=false&%7Clabel:X%7C$postcode&output=embed&key=AIzaSyC4jZOMt0RfJqDl5BUEID795hDDV78DDy0";
            file_put_contents("$path/$postcode.png", file_get_contents($mapURL));
        }
        return file_get_contents("$path/$postcode.png");
    }
}
