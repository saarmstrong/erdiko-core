<?php
/**
 * Container
 * Base view layer object
 * 
 * @category   Erdiko
 * @package    Core
 * @copyright  Copyright (c) 2014, Arroyo Labs, http://www.arroyolabs.com
 * @author	   John Arroyo
 */
namespace erdiko\core;


class Container
{
    protected $_template = null;
    protected $_data = null;
    protected $_defaultTemplate = 'default';
    protected $_templateFolder = null;

    /**
     * Constructor
     * @param string $template, Theme Object (Contaier)
     * @param mixed $data
     */
    public function __construct($template = null, $data = null)
    {
        $template = ($template === null) ? $this->_defaultTemplate : $template;
        $this->setTemplate($template);
        $this->setData($data);
    }
	
    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
    	$this->_template = $template.".php";
    }

    /**
     * @param mixed $data, data injected into the view
     */
    public function setData($data)
    {
        $this->_data = $data;
    }

    /**
     *
     */
    public function getTemplateFolder()
    {
        return APPROOT.'/'.$this->_templateFolder.'/';
    }

    /**
     * Get rendered template file
     * Accepts one of the types of template files in this order:
     * php (.php), html/mustache (.html), markdown (.md)
     * 
     * @param string $filename, file without extension
     * @param array $data, associative array of data
     * @throws \Exception, template file does not exist
     */
    public function getTemplateFile($filename, $data)
    {
        // error_log("getTemplateFile($filename)");

        if (is_file($filename.'.php'))
        {
            ob_start();
            include $filename.'.php';
            return ob_get_clean();

        } elseif (is_file($filename.'.html')) {
            $file = file_get_contents($filename.'.html');
            $m = new \Mustache_Engine;
            return $m->render($file, $data); 

        } elseif (is_file($filename.'.md')) {
            $parsedown = new \Parsedown();
            return $parsedown->text(file_get_contents($filename.'.md'));
        
        }
        
        throw new \Exception("Template file does not exist");
    }

    /**
     *
     */
    public function toHtml()
    {
        $filename = $this->getTemplateFolder().$this->_template;
        $data = (is_subclass_of($this->_data, 'erdiko\core\Container')) ? $this->_data->toHtml() : $this->_data;

        error_log("filename: $filename");

        return $this->getTemplateFile($filename, $data);
    }
}