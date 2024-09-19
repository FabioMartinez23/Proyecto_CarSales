<?php

class Paginacion{
    public $pagina_actual = 0;
    public $paginacion = 5;

    /**
     * Get the value of pagina_actual
     */ 
    public function getPagina_actual()
    {
        return $this->pagina_actual;
    }

    /**
     * Set the value of pagina_actual
     *
     * @return  self
     */ 
    public function setPagina_actual($pagina_actual)
    {
        $this->pagina_actual = $pagina_actual;

        return $this;
    }

    /**
     * Get the value of paginacion
     */ 
    public function getPaginacion()
    {
        return $this->paginacion;
    }

    /**
     * Set the value of paginacion
     *
     * @return  self
     */ 
    public function setPaginacion($paginacion)
    {
        $this->paginacion = $paginacion;

        return $this;
    }
}

?>