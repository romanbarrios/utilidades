<?php
/**
 * Clase para crear un archivo de cache,
 * para aplicaciones de alto trafico se
 * recomienda el uso de otras herramientas
 * como memcached para disminuir la lectura
 * y escritura en disco duro.
 * 
 * Creado por: Roman Barrios
 * Fecha: 05-09-2015
 */
define("SALTO_LINEA_TEXTO",
'
'); // Para usar en saltos de linea como texto plano
class Filecache_Class {
	/**
	 * Crea el objeto del archivo a utilizar
	 */
	private $filecache;
	public $arreglo_cache;
	
	/**
	 * Se le asigna la ruta del archivo
	 * segun la key enviada.
	 *
	 * @param: $key nombre de la key
	 */
	public function archivo($key) {
		$this->filecache = '../cache/' . $key . '.tmp.php';
		return TRUE;
	}
	
	/**
	 * Funcion para leer archivo de cache.
	 * Si existe lo retorna, en caso de no
	 * existir devuelve un FALSE
	 *
	 * @param: $key nombre de la key
	 * @cachetime: tiempo maximo del cache en segundos.
	 * por defecto 60 segundos.
	 */
	public function leer($key,$cachetime = 60) {
		// Se obtiene la ruta a usar
		$this->archivo($key);
		/**
		 * Se valida si el cache aun es valido,
		 * si es asi lo devuelve y retorna un TRUE
		 * en caso contrario devuelve FALSE y empieza
		 * a almacenar en bufer el resultado html
		 */
		if ( file_exists($this->filecache) && time() - $cachetime < filemtime($this->filecache) ) {
			include($this->filecache);
			return TRUE;
		}
		else {
			ob_start(); // Inicia el buffer
			return FALSE;
		}
	}
	
	/**
	 * Funcion para guardar archivo de cache.
	 * Si existe lo retorna, en caso de no
	 * existir devuelve un FALSE
	 *
	 * @param: 
	 *	$key: nombre de la key
	 *	$salvar: true guarda false no
	 */
	public function guardar($key, $salvar = false) {
		// Se obtiene la ruta a usar
		$this->archivo($key);
		// Guarda solo cuando se habilito salvar
		if ( $salvar ) {
			// Se hace el cache hacia el archivo
			$cached = fopen($this->filecache, 'w');
			fwrite($cached, ob_get_contents());
			fclose($cached);
		}
		ob_end_flush(); // Retorna la salida 
		return TRUE;
	}
	
    	/**
	 * Metodo para obtener variables php por un arreglo
	 * Si existe lo retorna, en caso de no
	 * existir devuelve un FALSE
	 *
	 * @param: $key nombre de la key
	 * @cachetime: tiempo maximo del cache en segundos.
	 * por defecto 60 segundos.
	 */
	public function leerArreglo($key,$cachetime = 60) {
		// Se obtiene la ruta a usar
		$this->archivo($key);
		
		/**
		 * Se valida si el cache aun es valido,
		 * si es asi lo devuelve y retorna un TRUE
		 * en caso contrario devuelve FALSE y empieza
		 * a almacenar en bufer el resultado html
		 */
		if ( file_exists($this->filecache) && time() - $cachetime < filemtime($this->filecache) ) {
			// Obtiene el arreglo
			$this->arreglo_cache = array();	
			// Obtiene las lineas
			$lines = file($this->filecache, FILE_SKIP_EMPTY_LINES );
			foreach ($lines as $line_num => $line) {
    			$this->arreglo_cache[] = $line;
			}
			return TRUE;
		}
		else {
			return FALSE;
		}
	}
	
	/**
	 * Funcion para guardar arreglo archivo de cache.
	 * Si existe lo retorna, en caso de no
	 * existir devuelve un FALSE
	 *
	 * @param: $key nombre de la key
	 */
	public function guardarArreglo($key,$arreglo) {
		// Se obtiene la ruta a usar
		$this->archivo($key);
		// Se hace el cache hacia el archivo
		$cached = fopen($this->filecache, 'w');
		$tamano = count($arreglo);
		// Esibe una linea por cada elimento del cache
		for ( $i = 0 ; $i < $tamano ; $i++ ) {
            		fwrite($cached, $arreglo[$i] . SALTO_LINEA_TEXTO );
        	}
		fclose($cached);
		return TRUE;
	}
    
    
	/**
	 * Metodo para obtener variables php por un arreglo
	 * Si existe lo retorna, en caso de no
	 * existir devuelve un FALSE
         * 
    	 * Esta version permite leer los arreglos directamente
     	 * si los mismos fueron guardados tipo JSON
	 *
	 * @param: $key nombre de la key
	 * @cachetime: tiempo maximo del cache en segundos.
	 * por defecto 60 segundos.
	 */
	public function leerArregloJson($key,$cachetime = 60) {
		// Se obtiene la ruta a usar
		$this->archivo($key); 
		
		/**
		 * Se valida si el cache aun es valido,
		 * si es asi lo devuelve y retorna un TRUE
		 * en caso contrario devuelve FALSE y empieza
		 * a almacenar en bufer el resultado html
		 */
		if ( file_exists($this->filecache) && time() - $cachetime < filemtime($this->filecache) ) {
			// Obtiene el arreglo
			$this->arreglo_cache = array();	            
		    	$archivo_cache = fopen($this->filecache, 'r');
		    	$this->arreglo_cache = json_decode(fgets($archivo_cache),TRUE);
		    	fclose($archivo_cache);
				return TRUE;
		}
		else {
			return FALSE;
		}
	}
	
	/**
	 * Funcion para guardar arreglo archivo de cache.
	 * Si existe lo retorna, en caso de no
	 * existir devuelve un FALSE
    	 * Este metodo permite guardar los arreglos en cache JSONs
	 *
	 * @param: $key nombre de la key
	 */
	public function guardarArregloJson($key,$arreglo) {
		// Se obtiene la ruta a usar
		$this->archivo($key);
		// Se hace el cache hacia el archivo
		$cached = fopen($this->filecache, 'w');        
        	fwrite($cached, json_encode($arreglo) );
		fclose($cached);
		return TRUE;
	}
	
	/**
	 * Metodo para eliminar archivo del cache
	 *
	 * @param: $key nombre de la key
	 */
	function eliminarCache( $key ) {
		// Se obtiene la ruta a usar
		$this->archivo($key);
		// Si existe lo elimina
		if ( file_exists($this->filecache) ) {
           	 unlink($this->filecache);
        	}
	}
		
	
    
}
