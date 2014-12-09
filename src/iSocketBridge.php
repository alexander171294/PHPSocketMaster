<?php namespace PHPSocketMaster;


// requerimos la clase socketMaster
require('class/SocketBridge.php');

/**
 * @abstract interface iSocketBridge
 * @author Alexander
 * @version 1.0
 * interface de SocketBridge
 * 
 * @example none
 */
interface iSocketBridge
{
	
	/**
	 * Function set_SocketEventReceptor
	 * @return none
	 * WARN!!: esta función es únicamente simbólica para cumplir con los requisitos
	 * del atributo, pero se maneja a dicho atributo como solo lectura por lo que esta función
	 * emite una excepcion catchable, de igual manera como la anterior no es necesario llamarla
	 * directamente, también al setear este atributo como si de un público se tratara, será llamada esta función
	 */
	public function set_SocketEventReceptor($val);
}
