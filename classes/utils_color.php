<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains the editor class for the assignfeedback_editpdfplus plugin
 *
 * @package   assignfeedback_editpdfplus
 * @copyright  2018 Université de Lausanne
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignfeedback_editpdfplus;

/**
 * This class performs crud operations on colors.
 *
 * No capability checks are done - they should be done by the calling class.
 *
 * @package   assignfeedback_editpdfplus
 * @copyright 2018 Marion Chardon
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class utils_color {

    const DEFAULTCOLOR = "default";

    /**
     * Get RGB color from label of color or hexa code
     * 
     * @param string $color Color name or Hexa code
     * @return array()
     */
    public static function getColorRGB($color = self::DEFAULTCOLOR) {
        if (substr($color, 0, 1) == '#') {
            $hexaCode = self::hex2RGB($color);
            if ($hexaCode) {
                return $hexaCode;
            }
            $color = self::DEFAULTCOLOR;
        }
        return self::colorNameToRGB($color);
    }

    /**
     * Convert a hexa decimal color code to its RGB equivalent
     *
     * @param string $hexStrRaw (hexadecimal color value)
     * @param boolean $returnAsString (if set true, returns the value separated by the separator character. Otherwise returns associative array)
     * @param string $seperator (to separate RGB values. Applicable only if second parameter is true.)
     * @return array or string (depending on second parameter. Returns False if invalid hex color value)
     */
    public static function hex2RGB($hexStrRaw, $returnAsString = false, $seperator = ',') {
        // Gets a proper hex string
        $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStrRaw);
        $rgbArray = array();
        //If a proper hex code, convert using bitwise operation. No overhead... faster
        if (strlen($hexStr) == 6) {
            $colorVal = hexdec($hexStr);
            $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
            $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
            $rgbArray['blue'] = 0xFF & $colorVal;
        } elseif (strlen($hexStr) == 3) {
            //if shorthand notation, need some string manipulations
            $rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
            $rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
            $rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
        } else {
            //Invalid hex color code
            return false;
        }
        // returns the rgb string or the associative array
        return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray;
    }

    /**
     * Make association between color name and RGB association
     * 
     * @param type $colorName Color Name like red
     * @return array RGB representation
     */
    public static function colorNameToRGB($colorName) {
        $colorNameLower = strtolower($colorName);
        if (isset(self::$colorsMap[$colorNameLower])) {
            return self::$colorsMap[$colorNameLower];
        }
        return self::$colorsMap[self::DEFAULTCOLOR];
    }

    /**
     * Map between color name and RGB code
     * @var array()
     */
    static $colorsMap = array(
        "wheat" => array(251, 221, 126),
        "very dark purple" => array(42, 1, 52),
        "bottle green" => array(4, 74, 5),
        "watermelon" => array(253, 70, 89),
        "deep sky blue" => array(13, 117, 248),
        "fire engine red" => array(254, 0, 2),
        "yellow ochre" => array(203, 157, 6),
        "pumpkin orange" => array(251, 125, 7),
        "pale olive" => array(185, 204, 129),
        "light lilac" => array(237, 200, 255),
        "lightish green" => array(97, 225, 96),
        "carolina blue" => array(138, 184, 254),
        "mulberry" => array(146, 10, 78),
        "shocking pink" => array(254, 2, 162),
        "auburn" => array(154, 48, 1),
        "bright lime green" => array(101, 254, 8),
        "celadon" => array(190, 253, 183),
        "pinkish brown" => array(177, 114, 97),
        "poo brown" => array(136, 95, 1),
        "bright sky blue" => array(2, 204, 254),
        "celery" => array(193, 253, 149),
        "dirt brown" => array(131, 101, 57),
        "strawberry" => array(251, 41, 67),
        "dark lime" => array(132, 183, 1),
        "copper" => array(182, 99, 37),
        "medium brown" => array(127, 81, 18),
        "muted green" => array(95, 160, 82),
        "robin's egg" => array(109, 237, 253),
        "bright aqua" => array(11, 249, 234),
        "bright lavender" => array(199, 96, 255),
        "ivory" => array(255, 255, 203),
        "very light purple" => array(246, 206, 252),
        "light navy" => array(21, 80, 132),
        "pink red" => array(245, 5, 79),
        "olive brown" => array(100, 84, 3),
        "poop brown" => array(122, 89, 1),
        "mustard green" => array(168, 181, 4),
        "ocean green" => array(61, 153, 115),
        "very dark blue" => array(0, 1, 51),
        "dusty green" => array(118, 169, 115),
        "light navy blue" => array(46, 90, 136),
        "minty green" => array(11, 247, 125),
        "adobe" => array(189, 108, 72),
        "barney" => array(172, 29, 184),
        "jade green" => array(43, 175, 106),
        "bright light blue" => array(38, 247, 253),
        "light lime" => array(174, 253, 108),
        "dark khaki" => array(155, 143, 85),
        "orange yellow" => array(255, 173, 1),
        "ocre" => array(198, 156, 4),
        "maize" => array(244, 208, 84),
        "faded pink" => array(222, 157, 172),
        "british racing green" => array(5, 72, 13),
        "sandstone" => array(201, 174, 116),
        "mud brown" => array(96, 70, 15),
        "light sea green" => array(152, 246, 176),
        "robin egg blue" => array(138, 241, 254),
        "aqua marine" => array(46, 232, 187),
        "dark sea green" => array(17, 135, 93),
        "soft pink" => array(253, 176, 192),
        "orangey brown" => array(177, 96, 2),
        "cherry red" => array(247, 2, 42),
        "burnt yellow" => array(213, 171, 9),
        "brownish grey" => array(134, 119, 95),
        "camel" => array(198, 159, 89),
        "purplish grey" => array(122, 104, 127),
        "marine" => array(4, 46, 96),
        "greyish pink" => array(200, 141, 148),
        "pale turquoise" => array(165, 251, 213),
        "pastel yellow" => array(255, 254, 113),
        "bluey purple" => array(98, 65, 199),
        "canary yellow" => array(255, 254, 64),
        "faded red" => array(211, 73, 78),
        "sepia" => array(152, 94, 43),
        "coffee" => array(166, 129, 76),
        "bright magenta" => array(255, 8, 232),
        "mocha" => array(157, 118, 81),
        "ecru" => array(254, 255, 202),
        "purpleish" => array(152, 86, 141),
        "cranberry" => array(158, 0, 58),
        "darkish green" => array(40, 124, 55),
        "brown orange" => array(185, 105, 2),
        "dusky rose" => array(186, 104, 115),
        "melon" => array(255, 120, 85),
        "sickly green" => array(148, 178, 28),
        "silver" => array(197, 201, 199),
        "purply blue" => array(102, 26, 238),
        "purpleish blue" => array(97, 64, 239),
        "hospital green" => array(155, 229, 170),
        "shit brown" => array(123, 88, 4),
        "mid blue" => array(39, 106, 179),
        "amber" => array(254, 179, 8),
        "easter green" => array(140, 253, 126),
        "soft blue" => array(100, 136, 234),
        "cerulean blue" => array(5, 110, 238),
        "golden brown" => array(178, 122, 1),
        "bright turquoise" => array(15, 254, 249),
        "red pink" => array(250, 42, 85),
        "red purple" => array(130, 7, 71),
        "greyish brown" => array(122, 106, 79),
        "vermillion" => array(244, 50, 12),
        "russet" => array(161, 57, 5),
        "steel grey" => array(111, 130, 138),
        "lighter purple" => array(165, 90, 244),
        "bright violet" => array(173, 10, 253),
        "prussian blue" => array(0, 69, 119),
        "slate green" => array(101, 141, 109),
        "dirty pink" => array(202, 123, 128),
        "dark blue green" => array(0, 82, 73),
        "pine" => array(43, 93, 52),
        "yellowy green" => array(191, 241, 40),
        "dark gold" => array(181, 148, 16),
        "bluish" => array(41, 118, 187),
        "darkish blue" => array(1, 65, 130),
        "dull red" => array(187, 63, 63),
        "pinky red" => array(252, 38, 71),
        "bronze" => array(168, 121, 0),
        "pale teal" => array(130, 203, 178),
        "military green" => array(102, 124, 62),
        "barbie pink" => array(254, 70, 165),
        "bubblegum pink" => array(254, 131, 204),
        "pea soup green" => array(148, 166, 23),
        "dark mustard" => array(168, 137, 5),
        "shit" => array(127, 95, 0),
        "medium purple" => array(158, 67, 162),
        "very dark green" => array(6, 46, 3),
        "dirt" => array(138, 110, 69),
        "dusky pink" => array(204, 122, 139),
        "red violet" => array(158, 1, 104),
        "lemon yellow" => array(253, 255, 56),
        "pistachio" => array(192, 250, 139),
        "dull yellow" => array(238, 220, 91),
        "dark lime green" => array(126, 189, 1),
        "denim blue" => array(59, 91, 146),
        "teal blue" => array(1, 136, 159),
        "lightish blue" => array(61, 122, 253),
        "purpley blue" => array(95, 52, 231),
        "light indigo" => array(109, 90, 207),
        "swamp green" => array(116, 133, 0),
        "brown green" => array(112, 108, 17),
        "dark maroon" => array(60, 0, 8),
        "hot purple" => array(203, 0, 245),
        "dark forest green" => array(0, 45, 4),
        "faded blue" => array(101, 140, 187),
        "drab green" => array(116, 149, 81),
        "light lime green" => array(185, 255, 102),
        "snot green" => array(157, 193, 0),
        "yellowish" => array(250, 238, 102),
        "light blue green" => array(126, 251, 179),
        "bordeaux" => array(123, 0, 44),
        "light mauve" => array(194, 146, 161),
        "ocean" => array(1, 123, 146),
        "marigold" => array(252, 192, 6),
        "muddy green" => array(101, 116, 50),
        "dull orange" => array(216, 134, 59),
        "steel" => array(115, 133, 149),
        "electric purple" => array(170, 35, 255),
        "fluorescent green" => array(8, 255, 8),
        "yellowish brown" => array(155, 122, 1),
        "blush" => array(242, 158, 142),
        "soft green" => array(111, 194, 118),
        "bright orange" => array(255, 91, 0),
        "lemon" => array(253, 255, 82),
        "purple grey" => array(134, 111, 133),
        "acid green" => array(143, 254, 9),
        "pale lavender" => array(238, 207, 254),
        "violet blue" => array(81, 10, 201),
        "light forest green" => array(79, 145, 83),
        "burnt red" => array(159, 35, 5),
        "khaki green" => array(114, 134, 57),
        "cerise" => array(222, 12, 98),
        "faded purple" => array(145, 110, 153),
        "apricot" => array(255, 177, 109),
        "dark olive green" => array(60, 77, 3),
        "grey brown" => array(127, 112, 83),
        "green grey" => array(119, 146, 111),
        "true blue" => array(1, 15, 204),
        "pale violet" => array(206, 174, 250),
        "periwinkle blue" => array(143, 153, 251),
        "light sky blue" => array(198, 252, 255),
        "blurple" => array(85, 57, 204),
        "green brown" => array(84, 78, 3),
        "bluegreen" => array(1, 122, 121),
        "bright teal" => array(1, 249, 198),
        "brownish yellow" => array(201, 176, 3),
        "pea soup" => array(146, 153, 1),
        "forest" => array(11, 85, 9),
        "barney purple" => array(160, 4, 152),
        "ultramarine" => array(32, 0, 177),
        "purplish" => array(148, 86, 140),
        "puke yellow" => array(194, 190, 14),
        "bluish grey" => array(116, 139, 151),
        "dark periwinkle" => array(102, 95, 209),
        "dark lilac" => array(156, 109, 165),
        "reddish" => array(196, 66, 64),
        "light maroon" => array(162, 72, 87),
        "dusty purple" => array(130, 95, 135),
        "terra cotta" => array(201, 100, 59),
        "avocado" => array(144, 177, 52),
        "marine blue" => array(1, 56, 106),
        "teal green" => array(37, 163, 111),
        "slate grey" => array(89, 101, 109),
        "lighter green" => array(117, 253, 99),
        "electric green" => array(33, 252, 13),
        "dusty blue" => array(90, 134, 173),
        "golden yellow" => array(254, 198, 21),
        "bright yellow" => array(255, 253, 1),
        "light lavender" => array(223, 197, 254),
        "umber" => array(178, 100, 0),
        "poop" => array(127, 94, 0),
        "dark peach" => array(222, 126, 93),
        "jungle green" => array(4, 130, 67),
        "eggshell" => array(255, 255, 212),
        "denim" => array(59, 99, 140),
        "yellow brown" => array(183, 148, 0),
        "dull purple" => array(132, 89, 126),
        "chocolate brown" => array(65, 25, 0),
        "wine red" => array(123, 3, 35),
        "neon blue" => array(4, 217, 255),
        "dirty green" => array(102, 126, 44),
        "light tan" => array(251, 238, 172),
        "ice blue" => array(215, 255, 254),
        "cadet blue" => array(78, 116, 150),
        "dark mauve" => array(135, 76, 98),
        "very light blue" => array(213, 255, 255),
        "grey purple" => array(130, 109, 140),
        "pastel pink" => array(255, 186, 205),
        "very light green" => array(209, 255, 189),
        "dark sky blue" => array(68, 142, 228),
        "evergreen" => array(5, 71, 42),
        "dull pink" => array(213, 134, 157),
        "aubergine" => array(61, 7, 52),
        "mahogany" => array(74, 1, 0),
        "reddish orange" => array(248, 72, 28),
        "deep green" => array(2, 89, 15),
        "vomit green" => array(137, 162, 3),
        "purple pink" => array(224, 63, 216),
        "dusty pink" => array(213, 138, 148),
        "faded green" => array(123, 178, 116),
        "camo green" => array(82, 101, 37),
        "pinky purple" => array(201, 76, 190),
        "pink purple" => array(219, 75, 218),
        "brownish red" => array(158, 54, 35),
        "dark rose" => array(181, 72, 93),
        "mud" => array(115, 92, 18),
        "brownish" => array(156, 109, 87),
        "emerald green" => array(2, 143, 30),
        "pale brown" => array(177, 145, 110),
        "dull blue" => array(73, 117, 156),
        "burnt umber" => array(160, 69, 14),
        "medium green" => array(57, 173, 72),
        "clay" => array(182, 106, 80),
        "light aqua" => array(140, 255, 219),
        "light olive green" => array(164, 190, 92),
        "brownish orange" => array(203, 119, 35),
        "dark aqua" => array(5, 105, 107),
        "purplish pink" => array(206, 93, 174),
        "dark salmon" => array(200, 90, 83),
        "greenish grey" => array(150, 174, 141),
        "jade" => array(31, 167, 116),
        "ugly green" => array(122, 151, 3),
        "dark beige" => array(172, 147, 98),
        "emerald" => array(1, 160, 73),
        "pale red" => array(217, 84, 77),
        "light magenta" => array(250, 95, 247),
        "sky" => array(130, 202, 252),
        "light cyan" => array(172, 255, 252),
        "yellow orange" => array(252, 176, 1),
        "reddish purple" => array(145, 9, 81),
        "reddish pink" => array(254, 44, 84),
        "orchid" => array(200, 117, 196),
        "dirty yellow" => array(205, 197, 10),
        "orange red" => array(253, 65, 30),
        "deep red" => array(154, 2, 0),
        "orange brown" => array(190, 100, 0),
        "cobalt blue" => array(3, 10, 167),
        "neon pink" => array(254, 1, 154),
        "rose pink" => array(247, 135, 154),
        "greyish purple" => array(136, 113, 145),
        "raspberry" => array(176, 1, 73),
        "aqua green" => array(18, 225, 147),
        "salmon pink" => array(254, 123, 124),
        "tangerine" => array(255, 148, 8),
        "brownish green" => array(106, 110, 9),
        "red brown" => array(139, 46, 22),
        "greenish brown" => array(105, 97, 18),
        "pumpkin" => array(225, 119, 1),
        "pine green" => array(10, 72, 30),
        "charcoal" => array(52, 56, 55),
        "baby pink" => array(255, 183, 206),
        "cornflower" => array(106, 121, 247),
        "blue violet" => array(93, 6, 233),
        "chocolate" => array(61, 28, 2),
        "greyish green" => array(130, 166, 125),
        "scarlet" => array(190, 1, 25),
        "green yellow" => array(201, 255, 39),
        "dark olive" => array(55, 62, 2),
        "sienna" => array(169, 86, 30),
        "pastel purple" => array(202, 160, 255),
        "terracotta" => array(202, 102, 65),
        "aqua blue" => array(2, 216, 233),
        "sage green" => array(136, 179, 120),
        "blood red" => array(152, 0, 2),
        "deep pink" => array(203, 1, 98),
        "grass" => array(92, 172, 45),
        "moss" => array(118, 153, 88),
        "pastel blue" => array(162, 191, 254),
        "bluish green" => array(16, 166, 116),
        "green blue" => array(6, 180, 139),
        "dark tan" => array(175, 136, 74),
        "greenish blue" => array(11, 139, 135),
        "pale orange" => array(255, 167, 86),
        "vomit" => array(162, 164, 21),
        "forrest green" => array(21, 68, 6),
        "dark lavender" => array(133, 103, 152),
        "dark violet" => array(52, 1, 63),
        "purple blue" => array(99, 45, 233),
        "dark cyan" => array(10, 136, 138),
        "olive drab" => array(111, 118, 50),
        "pinkish" => array(212, 106, 126),
        "cobalt" => array(30, 72, 143),
        "neon purple" => array(188, 19, 254),
        "light turquoise" => array(126, 244, 204),
        "apple green" => array(118, 205, 38),
        "dull green" => array(116, 166, 98),
        "wine" => array(128, 1, 63),
        "powder blue" => array(177, 209, 252),
        "off white" => array(255, 255, 228),
        "electric blue" => array(6, 82, 255),
        "dark turquoise" => array(4, 92, 90),
        "blue purple" => array(87, 41, 206),
        "azure" => array(6, 154, 243),
        "bright red" => array(255, 0, 13),
        "pinkish red" => array(241, 12, 69),
        "cornflower blue" => array(81, 112, 215),
        "light olive" => array(172, 191, 105),
        "grape" => array(108, 52, 97),
        "greyish blue" => array(94, 129, 157),
        "purplish blue" => array(96, 30, 249),
        "yellowish green" => array(176, 221, 22),
        "greenish yellow" => array(205, 253, 2),
        "medium blue" => array(44, 111, 187),
        "dusty rose" => array(192, 115, 122),
        "light violet" => array(214, 180, 252),
        "midnight blue" => array(2, 0, 53),
        "bluish purple" => array(112, 59, 231),
        "red orange" => array(253, 60, 6),
        "dark magenta" => array(150, 0, 86),
        "greenish" => array(64, 163, 104),
        "ocean blue" => array(3, 113, 156),
        "coral" => array(252, 90, 80),
        "cream" => array(255, 255, 194),
        "reddish brown" => array(127, 43, 10),
        "burnt sienna" => array(176, 78, 15),
        "brick" => array(160, 54, 35),
        "sage" => array(135, 174, 115),
        "grey green" => array(120, 155, 115),
        "white" => array(255, 255, 255),
        "robin's egg blue" => array(152, 239, 249),
        "moss green" => array(101, 139, 56),
        "steel blue" => array(90, 125, 154),
        "eggplant" => array(56, 8, 53),
        "light yellow" => array(255, 254, 122),
        "leaf green" => array(92, 169, 4),
        "light grey" => array(216, 220, 214),
        "puke" => array(165, 165, 2),
        "pinkish purple" => array(214, 72, 215),
        "sea blue" => array(4, 116, 149),
        "pale purple" => array(183, 144, 212),
        "slate blue" => array(91, 124, 153),
        "blue grey" => array(96, 124, 142),
        "hunter green" => array(11, 64, 8),
        "fuchsia" => array(237, 13, 217),
        "crimson" => array(140, 0, 15),
        "pale yellow" => array(255, 255, 132),
        "ochre" => array(191, 144, 5),
        "mustard yellow" => array(210, 189, 10),
        "light red" => array(255, 71, 76),
        "cerulean" => array(4, 133, 209),
        "pale pink" => array(255, 207, 220),
        "deep blue" => array(4, 2, 115),
        "rust" => array(168, 60, 9),
        "light teal" => array(144, 228, 193),
        "slate" => array(81, 101, 114),
        "goldenrod" => array(250, 194, 5),
        "dark yellow" => array(213, 182, 10),
        "dark grey" => array(54, 55, 55),
        "army green" => array(75, 93, 22),
        "grey blue" => array(107, 139, 164),
        "seafoam" => array(128, 249, 173),
        "puce" => array(165, 126, 82),
        "spring green" => array(169, 249, 113),
        "dark orange" => array(198, 81, 2),
        "sand" => array(226, 202, 118),
        "pastel green" => array(176, 255, 157),
        "mint" => array(159, 254, 176),
        "light orange" => array(253, 170, 72),
        "bright pink" => array(254, 1, 177),
        "chartreuse" => array(193, 248, 10),
        "deep purple" => array(54, 1, 63),
        "dark brown" => array(52, 28, 2),
        "taupe" => array(185, 162, 129),
        "pea green" => array(142, 171, 18),
        "puke green" => array(154, 174, 7),
        "kelly green" => array(2, 171, 46),
        "seafoam green" => array(122, 249, 171),
        "blue green" => array(19, 126, 109),
        "khaki" => array(170, 166, 98),
        "burgundy" => array(97, 0, 35),
        "dark teal" => array(1, 77, 78),
        "brick red" => array(143, 20, 2),
        "royal purple" => array(75, 0, 110),
        "plum" => array(88, 15, 65),
        "mint green" => array(143, 255, 159),
        "gold" => array(219, 180, 12),
        "baby blue" => array(162, 207, 254),
        "yellow green" => array(192, 251, 45),
        "bright purple" => array(190, 3, 253),
        "dark red" => array(132, 0, 0),
        "pale blue" => array(208, 254, 254),
        "grass green" => array(63, 155, 11),
        "navy" => array(1, 21, 62),
        "aquamarine" => array(4, 216, 178),
        "burnt orange" => array(192, 78, 1),
        "neon green" => array(12, 255, 12),
        "bright blue" => array(1, 101, 252),
        "rose" => array(207, 98, 117),
        "light pink" => array(255, 209, 223),
        "mustard" => array(206, 179, 1),
        "indigo" => array(56, 2, 130),
        "lime" => array(170, 255, 50),
        "sea green" => array(83, 252, 161),
        "periwinkle" => array(142, 130, 254),
        "dark pink" => array(203, 65, 107),
        "olive green" => array(103, 122, 4),
        "peach" => array(255, 176, 124),
        "pale green" => array(199, 253, 181),
        "light brown" => array(173, 129, 80),
        "hot pink" => array(255, 2, 141),
        "black" => array(51, 51, 51), //array(0, 0, 0),
        "lilac" => array(206, 162, 253),
        "navy blue" => array(0, 17, 70),
        "royal blue" => array(5, 4, 170),
        "beige" => array(230, 218, 166),
        "salmon" => array(255, 121, 108),
        "olive" => array(110, 117, 14),
        "maroon" => array(101, 0, 33),
        "bright green" => array(1, 255, 7),
        "dark purple" => array(53, 6, 62),
        "mauve" => array(174, 113, 129),
        "forest green" => array(6, 71, 12),
        "aqua" => array(19, 234, 201),
        "cyan" => array(0, 255, 255),
        "tan" => array(209, 178, 111),
        "dark blue" => array(0, 3, 91),
        "lavender" => array(199, 159, 239),
        "turquoise" => array(6, 194, 172),
        "dark green" => array(3, 53, 0),
        "violet" => array(154, 14, 234),
        "light purple" => array(191, 119, 246),
        "lime green" => array(137, 254, 5),
        "grey" => array(146, 149, 145),
        "sky blue" => array(117, 187, 253),
        "yellow" => array(255, 207, 53), //array(255, 255, 20),
        "yellowlemon" => array(255, 255, 0), //array(255, 255, 20),
        "magenta" => array(194, 0, 120),
        "light green" => array(150, 249, 123),
        "orange" => array(255, 207, 53), //array(249, 115, 6),
        "teal" => array(2, 147, 134),
        "light blue" => array(149, 208, 252),
        "red" => array(229, 0, 0),
        "brown" => array(101, 55, 0),
        "pink" => array(255, 129, 192),
        "blue" => array(125, 159, 211), //array(3, 67, 223),
        "green" => array(153, 202, 62), //array(21, 176, 26),
        "purple" => array(126, 30, 156),
        self::DEFAULTCOLOR => array(239, 69, 64)
    );

}
