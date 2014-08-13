<?php namespace PHPSocketMaster;

trait Singleton
{
	
	static private $instance = null;
	
	/**
	 * Static Function Factory
	 * @param array $params
	 */
	/* esto queda momentaneamente frenado porque es implementado en la versi�n 5.6 de php y a�n la estable es la 5.5 
	   de modo que esta funci�n requiere de una version de php tan actualizada que a�n no est� publica estable
	static public function Factory(...$params)
	{
		if(empty($instance))
			// cambiado por bug en php #36221
			//self::$instance = new __CLASS__(...$params);
			
		return self::$instance;
	}*/
	
	// version funcional para php 5.5
	static public function Factory()
	{
		if(empty($instance))
		{
			// cambiado por bug en php #36221
			$className = __CLASS__;
			self::$instance = new $className(func_get_args());
		}
		return self::$instance;
	}
	
}