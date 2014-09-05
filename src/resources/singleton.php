<?php namespace PHPSocketMaster;

trait Singleton
{
	
	static private $instance = null;
	
	/**
	 * Static Function Factory
	 * @param array $params
	 */
	/* esto queda momentaneamente frenado porque es implementado en la versión 5.6 de php y aún la estable es la 5.5 
	   de modo que esta función requiere de una version de php tan actualizada que aún no está publica estable
	static public function Factory(...$params)
	{
		if(empty($instance))
			// cambiado por bug en php #36221
			//self::$instance = new __CLASS__(...$params);
			
		return self::$instance;
	}*/
	
	// version funcional para php <= 5.5
	static public function Factory()
	{
		if(empty($instance))
		{
			// cambiado por bug en php #36221
			$className = __CLASS__;
			// dado que sigo teniendo problemas con hacer un factory correcto para versiones <= 5.5
			// vamos a hacer un par de trucos
			$firstArg = true;
			// creamos el listado de argumentos
			for($i = 0; $i<func_num_args(); $i++)
			{
				if($firstArg == true)
				{
					$args = func_get_arg($i);
					$firstArg = false;
				} else {
					$args .= ','.func_get_arg($i);
				}
			}
			// evaluamos un string que construya la clase
			eval('$NewInstance = new $className('.$args.');');
			// y esto hace esto:
			// self::$instance = new $className(func_get_arg(0),func_get_arg(1),.. etc);
			self::$instance = $NewInstance;
		}
		return self::$instance;
	}
	
	static public function get_instance() { return self::$instance; }
}