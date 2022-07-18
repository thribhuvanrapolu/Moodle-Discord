<?php

// require __DIR__ . '/../../config.php';


// require_once('/opt/lampp/htdocs/moodle/my/index.php');

// include '/opt/lampp/htdocs/moodle/my/index.php';


class block_simplehtml extends block_base {
    public function init() {
        $this->title = 'Discord-Login';
    }
    // The PHP tag and the curly bracket for the class definition 
    // will only be closed after there is another function added in the next section.

    public function get_content() {

        require_once('/opt/lampp/htdocs/moodle/blocks/simplehtml/library/lib.php');
        global $USER;

        if ($this->content !== null) {
          return $this->content;
        }

        $this->content   =  new stdClass;
        // $this->content->text   = "$USER->username";        

        
        if(check_already_login($USER->id)=="False")
        {
            $this->content->text   = '<a href=Discord-Moodle-oAuth/index.php>Click here to link account</a>';
        }
        else
        {
            $this->content->text="ACCOUNT LINKED:";
            $this->content->footer=check_already_login($USER->id)."\n<a href=Discord-Moodle-oAuth/logout.php>Click here to unlink this account</a> "; 
        }
        
        return $this->content;
    }
}   