#### TCPDF

La libreria requiere tener acceso con permisos de lectura y escritura en `C:\Windows\temp`.
En Nuestros servidores no tiene problema de escribir sobre la carpeta, pero si para borrar el archivo.

Para evitar un error se debe comentar el siguente bloque de codigo dentro de la libreria

`vendor\tecnickcom\tcpdf\tcpdf.php`

```php
if ($destroyall and !$preserve_objcopy && isset($this->file_id)) {
    self::$cleaned_ids[$this->file_id] = true;
    // remove all temporary files
    if ($handle = @opendir(K_PATH_CACHE)) {
        while (false !== ($file_name = readdir($handle))) {
            if (strpos($file_name, '__tcpdf_' . $this->file_id . '_') === 0) {
                unlink(K_PATH_CACHE . $file_name);
            }
        }
        closedir($handle);
    }
    if (isset($this->imagekeys)) {
        foreach ($this->imagekeys as $file) {
            if (strpos($file, K_PATH_CACHE) === 0 && TCPDF_STATIC::file_exists($file)) {
                @unlink($file);
            }
        }
    }
} 
```