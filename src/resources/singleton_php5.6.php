<?php namespace PHPSocketMaster;

trait Singleton
{
	
	static private $instance = null;
	
	/**
	 * Static Function Factory
	 * @param array $params
	 */
	final static public function Factory(...$params)
	{
		if(empty($instance))
		{
			//siguen sin arreglar el bug #36221 (los de php xD)
			$className = __CLASS__;
			// cambiado por bug en php #36221
			self::$instance = new $className(...$params);
		}	
		return self::$instance;
	}
	
	final static public function get_instance() { return self::$instance; }
	
	final private function __wakeup() {}
	final private function __clone() {}
}