<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Extended controller
 * 
 * Let model / library enable autoload
 *
 * @author visvoy@gmail.com
 * @license free
 */
class MY_Controller extends CI_Controller {
    
    // anti: Indirect modification of overloaded property
    public $benchmark;
    public $hooks;
    public $config;
    public $log;
    public $utf8;
    public $uri;
    public $router;
    public $output;
    public $security;
    public $input;
    public $lang;
    public $load;

	/**
	 * Let model / library enable autoload
	 *
	 * @param string $key 
	 * @return object
	 */
    public function __get($key)
    {
        if ('db' == $key) {
            $this->load->database();
            return $this->db;
        }
        
        if (substr($key, 0, 3) == 'db_') {
            $this->{$key} = $this->load->database($key, true);
            return $this->{$key};
        }
        
        $suffix = strrchr($key, '_');
        
        if ('_model' == $suffix or '_mdl' == $suffix) {
            if (strpos(substr($key, 0, -strlen($suffix)), '_')) {
                list($dir, $class) = explode('_', $key, 2);

                // The first naming case is to resolve class conflicts (when you have two class Api_user or else)
                // application/models/Teacher/Teacher_memo_mdl.php : class CI_Teacher_memo_mdl : $this->teacher_memo_mdl
                // application/models/Teacher/Memo_mdl.php : class CI_Memo_mdl : $this->teacher_memo_mdl
            
                foreach (array(
                    APPPATH . 'models/' . $dir . '/' . ucfirst($key) . '.php' => $dir . '/' . $key,
                    APPPATH . 'models/' . $dir . '/' . ucfirst($class) . '.php' => $dir . '/' . $class,
                ) as $file_name => $load_path) {
                    if (file_exists($file_name)) {
                        $this->load->model($load_path, $key);
                        return $this->{$key};
                    }
                }
            }
            
            $this->load->model($key);
            return $this->{$key};
        }
        
        if ($suffix) {
            list($dir, $class) = explode('_', $key, 2);
            
            // The first naming case is to resolve class conflicts (when you have two class Api_user or else)
            // application/libraries/Student/Student_work.php : class CI_Student_work : $this->student_work
            // application/libraries/Student/Work.php : class CI_Work : $this->student_work
            // system/libraries/Student/Student_work.php : class CI_Student_work : $this->student_work
            // system/libraries/Student/Work.php : class CI_Work : $this->student_work
            
            foreach (array(
                APPPATH . 'libraries/' . $dir . '/' . ucfirst($key) . '.php' => $dir . '/' . $key,
                APPPATH . 'libraries/' . $dir . '/' . ucfirst($class) . '.php' => $dir . '/' . $class,
                BASEPATH . 'libraries/' . $dir . '/' . ucfirst($key) . '.php' => $dir . '/' . $key,
                BASEPATH . 'libraries/' . $dir . '/' . ucfirst($class) . '.php' => $dir . '/' . $class,
            ) as $file_name => $load_path) {
                if (file_exists($file_name)) {
                    $this->load->library($load_path, '', $key);
                    return $this->{$key};
                }
            }
        }
        
        $this->load->library($key);
        return $this->{$key};
    }
    
} // class MY_Controller