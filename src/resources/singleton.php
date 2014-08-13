<?php

trait Singleton
{
	
	static private $instance = null;
	
	/**
	 * Static Function Factory
	 * @param array $params
	 */
	static public function Factory(...$params)
	{
		if(!empty($instance))
			self::$instance = new __CLASS__(...$params);
		return self::$instance;
	}
	
}