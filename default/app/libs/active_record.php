<?php

/**
 * ActiveRecord
 *
 * Esta clase es la clase padre de todos los modelos
 * de la aplicacion
 *
 * @category Kumbia
 * @package Db
 * @subpackage ActiveRecord
 */
// Carga el active record
Load::coreLib('kumbia_active_record');

class ActiveRecord extends KumbiaActiveRecord implements ArrayAccess {
    /**
     * Para llevar auditorias.
     * 
     * Es bueno que si se reescribe el metodo, se llame al parent, para 
     * que no se deje de ejecutar el log.
     * 
     */
    protected function after_save() {
        $this->log();
    }

    /**
     * Para llevar auditorias.
     * 
     * Es bueno que si se reescribe el metodo, se llame al parent, para 
     * que no se deje de ejecutar el log.
     */
    protected function after_delete() {
        $this->log();
    }

    /**
     * LLeva un control de auditorias para los cambios que se hagan en la BD.
     * 
     * Registra las consultas que alteren la data de la bd.
     * 
     * Actualmente no funciona con los INSERT INTO.
     * 
     */
    protected function log() {
        if ($this->source != 'auditorias') { //mucho ojo con esto
            //solo debemos hacer el log si la tabla no es la de auditorias;
            $tabla = $this->schema ? "$this->schema.$this->source" : $this->source;
            $sql = trim($this->db->last_sql_query());
            $tmp = explode(' ', $sql);
            $type = isset($tmp[0]) && is_string($tmp) ?$tmp[0] :'UNKNOW';
            if ($type != 'SELECT'){
                Acciones::add($type, $tabla, $sql);
            }
        }
    }

    public function offsetSet($indice, $valor) {
        if (!is_null($indice)) {
            $this->{$indice} = $valor;
        }
    }

    public function offsetExists($indice) {
        return isset($this->{$indice});
    }

    public function offsetUnset($indice) {
        //no se pueden quitar atributos.
    }

    public function offsetGet($indice) {
        return $this->offsetExists($indice) ? $this->{$indice} : NULL;
    }

}
