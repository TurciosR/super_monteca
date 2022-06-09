<?php
include '0.1conf.php';
include_once '_conexion_new.php';
set_time_limit(0);
$sql_p = _query("SELECT * FROM  $serverO.categoria");
$table="categoria";
_query("TRUNCATE $table");
while ($row=_fetch_array($sql_p)) {
  // code...
  $form_data = array(
    'id_categoria' => $row['id_categoria'],
    'nombre_cat' => $row['nombre'],
    'descripcion' => $row['descripcion'],
  );
  _insert($table,$form_data);
}

$sql_p = _query("SELECT * FROM  $serverO.proveedor");
$table="proveedor";
_query("TRUNCATE $table");
while ($row=_fetch_array($sql_p)) {
  // code...
  $form_data = array(
    'id_proveedor' => $row['id_proveedor'],
    'id_sucursal' => 1,
    'nombre' => $row['nombre_proveedor'],
    'categoria' => 1,
    'contacto' => $row['nombre_contacto'],
    'nit' => $row['nit'],
    'dui' => $row['dui'],
    'direccion' => $row['direccion'],
    'telefono1' => $row['telefono1'],
    'telefono2' => $row['telefono2'],
    'email' => $row['email'],
    'nombreche' => $row['nombre_contacto']
  );
  _insert($table,$form_data);
}

$sql_p = _query("SELECT * FROM  $serverO.cliente");
$table="cliente";
_query("TRUNCATE $table");
_query("ALTER TABLE $table DROP IF EXISTS latitud");
_query("ALTER TABLE $table DROP IF EXISTS longitud");
_query("ALTER TABLE `$table` ADD `latitud` DOUBLE NOT NULL AFTER `inactivo`");
_query("ALTER TABLE `$table` ADD `longitud` DOUBLE NOT NULL AFTER `latitud`");

while ($row=_fetch_array($sql_p)) {
  // code...
  $form_data = array(
    'id_cliente' => $row['id_cliente'],
    'categoria' => 1,
    'nombre' => $row['nombre']." ".$row['apellido'],
    'direccion' => $row['direccion'],
    'telefono1' => $row['telefono1'],
    'telefono2' => $row['telefono2'],
    'nit' => $row['nit'],
    'dui' => $row['dui'],
    'nrc' => $row['nrc'],
    'email' => $row['email'],
    'id_sucursal' => 1,
  );
  _insert($table,$form_data);
}

$sql_p = _query("SELECT * FROM  $serverO.sucursal");
$table="sucursal";
_query("TRUNCATE $table");
while ($row=_fetch_array($sql_p)) {
  // code...
  $form_data = array(
    'id_sucursal' => $row['id_sucursal'],
    'descripcion' => $row['descripcion'],
    'direccion' => $row['direccion'],
    'telefono1' => $row['telefono'],
    'casa_matriz' => $row['casa_matriz'],
    'iva' => 13,
    'monto_retencion1' => 100,
    'monto_percepcion' => 100,
    'vigencia_factura' => 1,
    'vigencia_pedido' => 1,
    'serie_cof' => '18NU000F',
    'desde_cof' => 1,
    'hasta_cof' => 5000,
    'serie_ccf' => '18UN000C',
    'desde_ccf' => 1,
    'hasta_ccf' => 5000,

  );
  _insert($table,$form_data);
}

$sql_p = _query("SELECT * FROM  $serverO.empleado");
$table="empleado";
_query("TRUNCATE $table");
while ($row=_fetch_array($sql_p)) {
  // code...
  $form_data = array(
    'id_empleado' => $row['id_empleado'],
    'nombre' => $row['nombre'],
    'apellido' => $row['apellido'],
    'nit' => $row['nit'],
    'dui' => $row['dui'],
    'direccion' => $row['direccion'],
    'telefono1' => $row['telefono1'],
    'telefono2' => $row['telefono2'],
    'email' => $row['email'],
    'salariobase' => $row['salariobase'],
    'id_tipo_empleado' => 3,
  );
  _insert($table,$form_data);
}

$sql_p = _query("SELECT * FROM $serverO.presentacion");
$table="presentacion";
_query("TRUNCATE $table");
while ($row=_fetch_array($sql_p)) {
  // code...
  $form_data = array(
    'id_presentacion' => $row['id_presentacion'],
    'id_sucursal' => 0,
    'nombre' => $row['descripcion'],
    'descripcion' => $row['descrip_corta'],

  );
  _insert($table,$form_data);
}


$sql_p = _query("SELECT * FROM  $serverO.usuario");
$table="usuario";

_query("TRUNCATE $table");
while ($row=_fetch_array($sql_p)) {
  // code...
  $form_data = array(
    'id_sucursal' => $row['id_sucursal'],
    'id_usuario' => $row['id_usuario'],
    'id_empleado' => $row['id_empleado'],
    'nombre' => $row['nombre'],
    'usuario' => $row['usuario'],
    'password' => $row['password'],
    'admin' => $row['tipo_usuario'],

  );
  _insert($table,$form_data);
}

$sql_p = _query("SELECT * FROM  $serverO.producto");
$table="producto";
_query("TRUNCATE $table");

while ($row=_fetch_array($sql_p)) {
  // code...
  $form_data = array(
    'id_producto' => $row['id_producto'],
    'barcode' => $row['barcode'],
    'descripcion' => $row['descripcion'],
    'marca' => $row['marca'],
    'perecedero' => $row['perecedero'],
    'exento' => $row['exento'],
    'estado' => 1,

    'id_categoria' => $row['id_categoria'],
  );
  _insert($table,$form_data);
}


$sql_p = _query("SELECT
$serverO.producto.id_producto,
$serverO.producto.unidad,
$serverO.stock.pv_base,
$serverO.stock.precio_semi_mayoreo,
$serverO.stock.precio_mayoreo,
$serverO.stock.ultimo_precio_compra,
$serverO.producto.id_presentacion
FROM  $serverO.producto left JOIN $serverO.stock on $serverO.stock.id_producto=$serverO.producto.id_producto");
$table="presentacion_producto";
_query("TRUNCATE $table");
while ($row=_fetch_array($sql_p)) {
  // code...
  $form_data = array(
    'id_pp'=> $row['id_producto'],
    'id_producto' => $row['id_producto'],
    'descripcion' => "1x1",
    'id_presentacion' => $row['id_presentacion'],
    'unidad' => 1,
    'precio' => $row['pv_base'],
    'precio1' => $row['precio_semi_mayoreo'],
    'precio2' => $row['precio_mayoreo'],
    'costo' => $row['ultimo_precio_compra'],
    'activo' => 1,
  );
  _insert($table,$form_data);
}

$sql_p = _query("SELECT * FROM $serverO.stock");
$table="stock";
_query("TRUNCATE $table");
while ($row=_fetch_array($sql_p)) {
  $form_data = array(
    'id_sucursal'=> 1,
    'id_stock'=> $row['id_producto'],
    'id_producto' => $row['id_producto'],
    'stock' => $row['stock'],
    'stock_local' => $row['stock'],
    'precio_unitario'=> $row['pv_base'],
    'costo_unitario' =>$row['ultimo_precio_compra'],
    'create_date' => date("Y-m-d"),
    'update_date' => date("Y-m-d")
  );
  _insert($table,$form_data);
}

$sql_p = _query("SELECT * FROM $serverO.stock");
$table="stock_ubicacion";
_query("TRUNCATE $table");
while ($row=_fetch_array($sql_p)) {
  $form_data = array(
    'id_sucursal'=> 1,
    'id_su'=> $row['id_producto'],
    'id_producto' => $row['id_producto'],
    'cantidad' => $row['stock'],
    'id_ubicacion' => 1,
    'id_estante'=> 0,
    'id_posicion' => 0,
  );
  _insert($table,$form_data);
}

$sql_p = _query("SELECT * FROM $serverO.stock");
$table="lote";
_query("TRUNCATE $table");
while ($row=_fetch_array($sql_p)) {
  $form_data = array(
    'id_sucursal'=> 1,
    'id_lote'=> $row['id_producto'],
    'id_producto' => $row['id_producto'],
    'fecha_entrada' => date("Y-m-d"),
    'numero' => 1,
    'cantidad' => $row['stock'],
    'precio' => $row['pv_base'],
    'id_presentacion' => $row['id_producto'],
    'vencimiento' => "0000-00-00",
    'estado' => 'VIGENTE',
    'referencia' => 0,

  );
  _insert($table,$form_data);
}


$sql_p = _query("SELECT * FROM $serverO.factura");
$table="factura";
_query("TRUNCATE $table");
while ($row=_fetch_array($sql_p)) {
  $form_data = array(
    'id_sucursal'=> 1,
    'id_factura'=> $row['id_factura'],
    'id_cliente'=> $row['id_cliente'],
    'fecha' => $row['fecha'],
    'numero_doc' => $row['numero_doc'],
    'subtotal' => $row['subtotal'],
    'sumas' => $row['sumas'],
    'iva' => $row['iva'],
    'total' => $row['total'],
    'id_usuario' => $row['id_usuario'],
    'anulada' => $row['anulada'],
    'id_empleado' => $row['id_empleado'],
    'finalizada' => $row['finalizada'],
    'impresa' => $row['impresa'],
    'tipo' => $row['tipo'],
    'num_fact_impresa' => $row['num_fact_impresa'],
    'hora' => $row['hora'],

  );
  _insert_s($table,$form_data);
}

$sql_p = _query("SELECT * FROM $serverO.factura_detalle");
$table="factura_detalle";
_query("TRUNCATE $table");
while ($row=_fetch_array($sql_p)) {
  $form_data = array(
    'id_sucursal'=> 1,
    'id_factura'=> $row['id_factura'],
    'id_prod_serv' => $row['id_prod_serv'],
    'cantidad' => $row['cantidad'],
    'precio_venta' => $row['precio_venta'],
    'subtotal' => $row['subtotal'],
    'id_empleado' => $row['id_empleado'],
    'tipo_prod_serv' => $row['tipo_prod_serv'],
    'fecha' => $row['fecha'],
    'hora' => $row['hora'],
    'id_presentacion' => $row['id_prod_serv'],
  );
  _insert_s($table,$form_data);
}

/*UPDATE mon_new_sis.producto JOIN pos_mon.stock  on pos_mon.stock.id_producto = mon_new_sis.producto.id_producto
set mon_new_sis.producto.minimo = pos_mon.stock.stock_minimo*/
 ?>
