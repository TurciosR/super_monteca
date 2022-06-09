<?php
include_once ('_core.php');
include ('num2letras.php');
function initial(){
	$id_movimiento = $_REQUEST['id_movimiento'];
	$x = uniqid();
	//permiso del script
	$sql = _query("SELECT producto.descripcion, ubicacion.descripcion as origen,est.descripcion as eo ,pos.posicion as po,ubi.descripcion as destino,estante.descripcion as ed,posicion.posicion as pd,movimiento_stock_ubicacion.cantidad,presentacion_producto.unidad,presentacion.nombre FROM movimiento_stock_ubicacion JOIN producto ON producto.id_producto=movimiento_stock_ubicacion.id_producto LEFT JOIN stock_ubicacion ON stock_ubicacion.id_su=movimiento_stock_ubicacion.id_origen LEFT JOIN ubicacion ON stock_ubicacion.id_ubicacion = ubicacion.id_ubicacion LEFT JOIN stock_ubicacion AS su ON su.id_su=movimiento_stock_ubicacion.id_destino LEFT JOIN ubicacion as ubi ON ubi.id_ubicacion=su.id_ubicacion LEFT JOIN estante ON estante.id_estante=su.id_estante LEFT JOIN posicion ON posicion.id_posicion=su.id_posicion LEFT JOIN estante AS est ON est.id_estante=stock_ubicacion.id_estante LEFT JOIN posicion as pos ON stock_ubicacion.id_posicion=pos.id_posicion JOIN presentacion_producto ON movimiento_stock_ubicacion.id_presentacion=presentacion_producto.id_pp JOIN presentacion ON presentacion.id_presentacion=presentacion_producto.id_presentacion WHERE movimiento_stock_ubicacion.id_sucursal=$_SESSION[id_sucursal] AND movimiento_stock_ubicacion.id_mov_prod=$id_movimiento");
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	?>
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title">Imprimir movimiento</h4>
	</div>
	<div class="modal-body">
		<div class="wrapper wrapper-content  animated fadeInRight">
			<div class="row" id="row1">
				<div class="col-lg-12">
						<table	class="table table-bordered table-striped" id="tableview">
							<thead>
								<tr>
									<th class="col-lg-4" rowspan="2">Producto </th>
									<th class="col-lg-3" colspan="3">Origen</th>
									<th class="col-lg-3" colspan="3">Destino</th>

									<th class="col-lg-1" rowspan="2">Presentación</th>
									<th class="col-lg-1" rowspan="2">Cantidad</th>
								</tr>
								<tr>
									<th class="col-lg-1">Ubicación</th>
									<th class="col-lg-1">Estante </th>
									<th class="col-lg-1">Posición </th>
									<th class="col-lg-1">Ubicación</th>
									<th class="col-lg-1">Estante</th>
									<th class="col-lg-1">Posición</th>
								</tr>
							</thead>
							<tbody>
								<?php
								while($row=_fetch_array($sql))
								{
									?>
									<tr>
										<td><?php echo $row['descripcion'] ?></td>
										<td><?php echo $row['origen'] ?></td>
										<td><?php echo $row['eo'] ?></td>
										<td><?php echo $row['po'] ?></td>
										<td><?php echo $row['destino'] ?></td>
										<td><?php echo $row['ed'] ?></td>
										<td><?php echo $row['pd'] ?></td>
										<td><?php echo $row['nombre']."($row[unidad])" ?></td>
										<td><?php echo $row['cantidad']/$row['unidad']; ?></td>
									</tr>
									<?php
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type='button' class='btn btn-primary <?=$x ?>' >Imprimir</button>
			<button type='button' class='btn btn-default' data-dismiss='modal'>Cerrar</button>
		</div>

		<script type="text/javascript">
			$(document).on('click', '.<?=$x ?>', function(event) {
				var datoss = "process=imprimir" + "&id_movimiento=" + <?=$id_movimiento ?>;
				$.ajax({
					type: "POST",
					url: "imprimir_descargo.php",
					data: datoss,
					dataType: 'json',
					success: function(datos) {
						var sist_ope = datos.sist_ope;
						var dir_print = datos.dir_print;
						var shared_printer_win = datos.shared_printer_win;
						var shared_printer_pos = datos.shared_printer_pos;

						if (sist_ope == 'win') {
							$.post("http://" + dir_print + "printvalewin1.php", {
								datosvale: datos.movimiento,
								shared_printer_win: shared_printer_win,
								shared_printer_pos: shared_printer_pos,
							})
						} else {
							$.post("http://" + dir_print + "printvale1.php", {
								datosvale: datos.movimiento
							});
						}

					}
				});
			});
		</script>
	<?php
}

function  imprimir(){
		$id_movimiento=$_REQUEST['id_movimiento'];

		$sql = _query("SELECT producto.descripcion,movimiento_producto_detalle.cantidad,presentacion_producto.unidad,presentacion.nombre FROM movimiento_producto JOIN movimiento_producto_detalle ON movimiento_producto.id_movimiento=movimiento_producto_detalle.id_movimiento JOIN producto on movimiento_producto_detalle.id_producto = producto.id_producto JOIN presentacion_producto ON presentacion_producto.id_pp = movimiento_producto_detalle.id_presentacion JOIN presentacion ON presentacion.id_presentacion=presentacion_producto.id_presentacion WHERE movimiento_producto.id_movimiento=$id_movimiento");


		$id_sucursal=$_SESSION['id_sucursal'];
		$id_sucursal=1;
		//directorio de script impresion cliente
		$sql_dir_print="SELECT *  FROM config_dir WHERE id_sucursal='$id_sucursal'";
		//$sql_dir_print="SELECT * FROM `config_dir` WHERE `id_sucursal`=1 ";
		$result_dir_print=_query($sql_dir_print);
		$row0=_fetch_array($result_dir_print);
		$dir_print=$row0['dir_print_script'];
		$shared_printer_win=$row0['shared_printer_matrix'];
		$shared_printer_pos=$row0['shared_printer_pos'];

		$sql_pos="SELECT *  FROM sucursal  WHERE id_sucursal='$id_sucursal' ";
		$result_pos=_query($sql_pos);
		$row1=_fetch_array($result_pos);

		$info_mov="DESCARGO"."\n";

		$info_mov.="$row1[descripcion]\n";
		$info_mov.="$row1[direccion]\n";
		$info_mov.="$row1[giro]\n";

		$sql_pos="SELECT * FROM movimiento_producto WHERE movimiento_producto.id_movimiento=$id_movimiento ";
		$result_pos=_query($sql_pos);
		$row1=_fetch_array($result_pos);

		$info_mov.="FECHA: ".ED($row1['fecha'])." ".hora($row1['hora'])."\n";

		$info_mov.=str_pad("DESCRIPCION",35," ",STR_PAD_RIGHT)."".str_pad(" CANT",5," ",STR_PAD_LEFT)."\n";
		$info_mov.=str_pad("-",40,"-",STR_PAD_RIGHT)."\n";
		$i=0;
		$j=0;
		while ($row=_fetch_array($sql)) {
			// code...
				$i++;
				$j=$j+round($row['cantidad']/$row['unidad'],2);
				$info_mov.=str_pad(substr($row['descripcion'],0,35),35," ",STR_PAD_RIGHT);
				$info_mov.=str_pad(round($row['cantidad']/$row['unidad'],2) ,5," ",STR_PAD_LEFT)."\n";
				$info_mov.=str_pad("  ".$row['nombre']."(".($row['unidad']).")" ,40," ",STR_PAD_RIGHT)."\n";
		}

		$info_mov.=str_pad("Items: $i",40," ",STR_PAD_RIGHT)."\n";
		$info_mov.=str_pad("Cantidad: $j",40," ",STR_PAD_RIGHT)."\n";



		//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
		$info = $_SERVER['HTTP_USER_AGENT'];
		if(strpos($info, 'Windows') == TRUE)
		$so_cliente='win';
		else
		$so_cliente='lin';
		$nreg_encode['shared_printer_win'] =$shared_printer_win;
		$nreg_encode['shared_printer_pos'] =$shared_printer_pos;
		$nreg_encode['dir_print'] =$dir_print;
		$nreg_encode['movimiento'] =$info_mov;
		$nreg_encode['sist_ope'] =$so_cliente;
		echo json_encode($nreg_encode);


}

if (! isset ( $_REQUEST ['process'] )) {
	initial();
} else {
	if (isset ( $_REQUEST ['process'] )) {
		switch ($_REQUEST ['process']) {
			case 'formDelete' :
				initial();
				break;
				case 'imprimir':
				imprimir();
				break;
			}
		}
	}

	?>
