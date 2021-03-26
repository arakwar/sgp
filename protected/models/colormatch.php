<?

/** 
* ColorMatch class
* Version       : 1.0
* Author        : Jerrett Taylor
* Last Updated  : March 1, 2003
*/

class ColorMatch
{
    var $default_color = '';
    var $colors = Array();
    
    /**
    * Constructor. Generates matching colors if passed a color
    */
    function ColorMatch($_color='')
    {
        // Constructor. If passed a color, generate our matching colors
        if ($_color) $this->Generate($_color);    
    }
    
    /**
    * returns requested color,or default if color is invalid
    * @param $_type type of color to return
    * @param $_pos position/number of color to return
    * @returns hexidecimal color
    */
    function GetColor($_type,$_pos)
    {
        return ($this->colors[$_type][$_pos]) ? $this->colors[$_type][$_pos] : $this->default_color;
    }
    
    /**
    * converts a hexidecimal color to HSV Color Model
    * @param $hex hexidecimal color
    * @returns array( hue, saturation, value )
    */
    function hex2hsv ($_hex)
    {
        $rgb = hexdec(str_replace("#","",$_hex));
        list ($_red,$_green,$_blue) = array(($rgb >> 16) & 0xFF, ($rgb >> 8) & 0xFF, $rgb & 0xFF);
    
        // get highest and lowest colors
        $max = max($_red,$_green,$_blue);
        $min = min($_red,$_green,$_blue);
        
        $v = $max;          // 'value' is always the highest value from RGB
        $delta = $max-$min; // get midway between highest and lowest, this is our delta!
        if ($max == 0)  return Array('0','0','0'); // this is black, if the biggest value is 0
    
        $s = 100*($delta/$max);  // 'saturation' is our delta divided by max 
        
        // see which color is most dominant
        if ($_red == $max)          $h = @round(( $_green - $_blue ) / $delta);     // between yellow & magenta
        elseif ($_green == $max)    $h = @round(2 + ( $_blue - $_red ) / $delta);   // between cyan & yellow
        else                        $h = @round(4 + ( $_red - $_green ) / $delta);  // between magenta & cyan
    
        $h*=60; // move into primary/secondary color group
        
        // we can't be having negatives.. if it's in negatives, add 360 (full circle)
        $h = ($h < 0) ? $h+=360 : $h;
        return Array(ceil($h),ceil($s),ceil(100*($v/255)));
    }
    
    /** 
    * converts a HSV color to hexidecimal 
    * @param $_hue HSV Hue
    * @param $_sat HSV saturation
    * @param $_val HSV value
    * @returns hexidecimal color (with #)
    */
    function hsv2hex($_hue, $_sat, $_val) 
    {
        // safegaurds against invalid values
        if ($_hue < 0 ) $_hue+=359; elseif ($_hue > 359) $_hue-=359;
        if ($_val > 100) $_val=100; elseif ($_val < 0) $_val = 0; 
        if ($_sat > 100) $_sat=100; elseif ($_sat < 0) $_sat=0;
        if ($_sat == 0) { $_val = floor($_val*2.55);     return '#'.str_pad(dechex($_val),2,'0',STR_PAD_LEFT).str_pad(dechex($_val),2,'0',STR_PAD_LEFT).str_pad(dechex($_val),2,'0',STR_PAD_LEFT); } // this is grey
    
        $_hue/=60;              // move hue into 1-6 (primary & secondary colors)
        $_sat/=100; $_val/=100; // divide by 100 so we are dealing with proper 0.0 - 0.1 
        $factor = $_hue-floor($_hue); // get fractional part of the hue
    
        // math to get into the 255 range of things from the _sat and _val
        $color1 = ceil($_val * (1-$_sat)*255);
        $color2 = ceil($_val * (1-($_sat * $factor))*255);
        $color3 = ceil($_val * (1-($_sat * (1-$factor)))*255);
        $_val = ceil($_val*255);
    
        // return rgb based on which primary/secondary color group we are in
        switch (floor($_hue))
        {
            case 0: $red = $_val; $green = $color3; $blue = $color1; break;
            case 1: $red = $color2; $green = $_val; $blue = $color1; break;
            case 2: $red = $color1; $green = $_val; $blue = $color3; break;
            case 3: $red = $color1; $green = $color2; $blue = $_val; break;
            case 4: $red = $color3; $green = $color1; $blue = $_val; break;
            case 5: $red = $_val; $green = $color1; $blue = $color2; break;
        }
        return '#'.str_pad(dechex($red),2,'0',STR_PAD_LEFT).str_pad(dechex($green),2,'0',STR_PAD_LEFT).str_pad(dechex($blue),2,'0',STR_PAD_LEFT);
    }
    
    /**
    * Gets matching colors. Returns triad colors, complementary colors, Analog colors
    * as well as modifications to the color tint, shade, and saturation.
    * @param $_hex hexidecimal color
    */
    function Generate($_hex)
    {
        $this->default_color = $_hex;
        list($_hue,$_sat,$_val) = $this->hex2hsv($_hex);
        
        // complementary color, and 2 variations in sat/val
        $this->colors['complimentary'][1] = $this->hsv2hex($_hue-180,$_sat,$_val);
        $this->colors['complimentary'][2] = $this->hsv2hex($_hue-180,$_sat+10,$_val-20);
        $this->colors['complimentary'][3] = $this->hsv2hex($_hue-180,$_sat-50,$_val-50);
        
        // triad colors
        $this->colors['triad'][1] = $_hex;
        $this->colors['triad'][2] = $this->hsv2hex($_hue-120,$_sat,$_val);
        $this->colors['triad'][3] = $this->hsv2hex($_hue+120,$_sat,$_val);
        
        // set some vars for modification
        $hue = $_hue-45;
        $val = ($_val > 75) ? $_val-=25 : $_val;
        $val = ($val < 25)  ? $val+=25 : $_val;
        $sat = $_sat;
        
        // get us some colors!
        for ($i=1;$i<=5;$i++)
        {
            $sat-=15; $val+=5; $hue+=30;    // Move along the hsv "color sphere" thingamagig
            $this->colors['analogue'][$i]       = $this->hsv2hex($hue,$_sat,$_val);
            $this->colors['tint'][$i]         = $this->hsv2hex($_hue,$_sat,$val);

            $this->colors['shade'][$i]        = $this->hsv2hex($_hue,$sat,$_val);
            $this->colors['saturation'][$i]   = $this->hsv2hex($_hue,$sat,$val);
        }
        
        // this is a set built from the sat, tint and analog results. 
        // this could be usefull for generating a theme
        $this->colors['set'][1] = $_hex;
        $this->colors['set'][2] = $this->colors['saturation'][5];
        $this->colors['set'][3] = $this->colors['saturation'][4];
        $this->colors['set'][4] = $this->colors['tint'][5];
        $this->colors['set'][5] = $this->colors['tint'][3];
        $this->colors['set'][6] = $this->colors['tint'][1];
        $this->colors['set'][7] = $this->colors['analogue'][3];    
        $this->colors['set'][8] = $this->colors['analogue'][5]; 
    }
    
    /**
    * Dumps out a list of all available colors. 
    */
    function ShowColors()
    {
     echo '<style type="text/css">body { background-color:'.$_POST['color'].'}</style>';        
     echo '<span style="position: absolute; top: 90px; left:15px; width: 190px;">';
        foreach($this->colors as $block => $cblock) 
        {
            echo '<span style="height: 10px; width: 190px;"> </span>';
            foreach($cblock as $key =>  $color)
            {
                echo '<span style="width: 190px;">
                    <span style="width: 110px; border: 1px solid black; border-right: 0px; display: table-cell; color: #0000ff;padding: 4px; font: 10px verdana; color:#000; background-color:#FFF; font-weight:bold;">'.$block.' '.$key.' </span>
                    <span style="width: 70px; display: table-cell; border: 1px solid black; padding: 4px; background-color: '.$color.'; text-align:center;font: 10px verdana;  color: #000;  text-transform: uppercase;">'.$color.'</span>
                </span>';    
            }
        }
        echo '</span>';    
    }
}
?>