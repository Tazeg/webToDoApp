<?php
//----------------------------------------------------------------------
// JeffProd - Web ToDo app
//----------------------------------------------------------------------
// AUTHOR	: Jean-Francois GAZET
// WEB 		: http://www.jeffprod.com
// TWITTER	: @JeffProd
// MAIL		: jeffgazet@gmail.com
// LICENCE	: GNU GENERAL PUBLIC LICENSE Version 3, June 2007
//----------------------------------------------------------------------

class Template
	{
	private $_tag;
	private $_txt;
	private $_templateFile;
	
	public function __construct($templateFile)
		{
		$this->_templateFile=$templateFile;
		$this->_tag=array();
		$this->_txt=array();
		}
	
	public function assign($tag,$txt)
		{
		$this->_tag[]=$tag;
		$this->_txt[]=$txt;
		}

	public function render()
		{
		ob_start();
		include($this->_templateFile);
		$template=ob_get_contents();
		ob_end_clean();
		$template=str_replace($this->_tag,$this->_txt,$template);		
		echo $template;
		}
	}
