<?php
class tad_JsObject
{
    protected $in;
    protected $out;

    public function toObject($arr)
    {
        if (!is_array($arr)) {
            throw new InvalidArgumentException("Argument must be an array", 1);
            
        }
        $out = json_encode($arr);
        // remove quotes around functions
        $out = preg_replace("/(\"\\s*)(function.*?})(\")/ui", "$2", $out);
        // remove quote escaping
        $out = preg_replace("/(\\\\+(\"|'))/ui", "\"", $out);
        return $out;
    }

    public static function on($arr)
    {
        $instance = new self();
        $instance->setIn($arr);
        return $instance;
    }

    public function getOut()
    {
        return $this->out;
    }

    public function setIn($value)
    {
        $this->out = $this->toObject($value);
        $this->in = $value;
        return $this;
    }

    public function printOut($objectName, $echo = true, $doNotWrap = false)
    {
        $prefix = '<script type="text/javascript">' . "\n\t" . '//<![CDATA[' . "\n\t\t";
        $var = sprintf('var %s = %s;', $objectName, $this->out);
        $postfix = "\n\t" . '//]]' . "\n" . '</script>';
        $out = '';
        if ($doNotWrap) {
            $out = $var;
        } else {
            $out = $prefix . $var . $postfix;
        }
        if (!$echo) {
            return $out;
        }
        echo $out;
    }

    public function localize($handle, $objectName)
    {
        global $wp_scripts;
        $out = $this->printOut($objectName, false, true);

        if (!$wp_scripts) {
            return;
        }
        $wp_scripts->add_data($handle, 'data', $out);
    }
}
