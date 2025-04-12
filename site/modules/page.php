<?php
class Page {
    private $template;

    public function __construct($template) {
        $this->template = file_get_contents($template);
    }

    public function Render($data) {
        $result = $this->template;
        
        foreach ($data as $key => $value) {
            $result = str_replace("{{" . $key . "}}", $value, $result);
        }
        
        return $result;
    }
}