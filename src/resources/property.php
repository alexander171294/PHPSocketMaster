<?php

trait Property // mi hermosa clase property
{
    // llamando a funciones setters
    Public function __set($property, $value)
    {
        if(is_callable(array($this, 'set_'.$property)))
            return call_user_func(array($this, 'set_'.$property), $value);
        else
        // no hay funcion getter para este atributo (o no existe el atributo)
            throw new exception('The atribute $'.$property.' not exist for set');
    }

    // llamando a funciones getters
    Public function __get($property)
    {
        if(is_callable(array($this, 'get_'.$property)))
            return call_user_func(array($this, 'get_'.$property));
        elseif(is_callable(array(parent, 'get_'.$property)))
            return call_user_func(array(parent, 'get_'.$property));
        else
        // no hay funcion getter para este atributo (o no existe el atributo)
            throw new exception('The atribute $'.$property.' not exist for get');
    }
}