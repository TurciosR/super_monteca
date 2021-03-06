$(document).ready(function() {
  $('.select').select2();

  searchFilter();
  $("#origen").change(function(){
    $("#mostrardatos").html("");
    searchFilter();
  });
  $('#producto_buscar').on('keyup', function(event) {
     searchFilter();
   });
});
$(function() {
  //binding event click for button in modal form
  $(document).on("click", "#btnDelete", function(event) {
    deleted();
  });
  // Clean the modal form
  $(document).on('hidden.bs.modal', function(e) {
    var target = $(e.target);
    target.removeData('bs.modal').find(".modal-content").html('');
  });
});
function searchFilter(page_num)
{
  page_num = page_num ? page_num : 0;
  var producto_buscar = $('#producto_buscar').val();
  var origen = $('#origen').val();
  //var limite = $('#limite').val();
  getData(producto_buscar,origen,page_num)
}

function getData(producto_buscar,origen,page_num){
  var sortBy = 'asc';//$('#sortBy').val();
  var records = 50;//$('#records').val();
  urlprocess = "admin_movimiento_pendiente.php";
  $.ajax({
    type: 'POST',
    url: urlprocess,
    data: {
      process: 'traerdatos',
      page: page_num,
      producto_buscar: producto_buscar,
      origen: origen,
      sortBy: sortBy,
      records: records
    },
    beforeSend: function()
    {
      $('.loading-overlay').show();
    },
    success: function(html)
    {
        $('#mostrardatos').find('input:checkbox:not(:checked)').closest('tr').remove();
        $('#mostrardatos').append(html);
        $(".sel").select2();
        $(".cant").numeric({negative:false,decimalPlaces:4});
    }
  });
  $.ajax({
    type: 'POST',
    url: "admin_movimiento_pendiente.php",
    data: {
      process: 'traerpaginador',
      page: page_num,
      producto_buscar: producto_buscar,
      origen: origen,
      sortBy: sortBy,
      records: records
    },
    success: function(value)
    {
      $('#paginador').html(value);
    }
  });
}

//Evento que se activa al perder el foco en precio de venta y cantidad:
$(document).on("blur", "#inventable", function() {
  totales();
});
$(document).on("keyup", ".precio_compra, .precio_venta", function() {
  totales();
});
$(document).on("keyup", ".cant", function() {
  var tr  = $(this).parents("tr");
  var unidad = parseInt(tr.find(".unidad").val());
  var exis = parseFloat(tr.find(".exis").text());
  var exis_ubi = parseFloat(tr.find(".exis_ubi").text());
  var cant = parseFloat($(this).val());
  var tot = cant * unidad;

  console.log(tot+" "+exis+" "+exis_ubi);
  if(tot > exis)
  {
    $(this).val(0);
  }
  if (tot>exis_ubi) {
    $(this).val(0);
  }
  totales();
});
// Evento que selecciona la fila y la elimina de la tabla
$(document).on("click", ".Delete", function()
{
  $(this).parents("tr").remove();
  totales();
});
$(document).on("click", ".cheke", function()
{
  var tr = $(this).parents("tr");
  if($(this).is(":checked"))
  {
    tr.find(".cant").attr("readOnly", false);
  }
  else
  {
    tr.find(".cant").attr("readOnly", true);
  }
});
//Calcular Totales del grid
function totales()
{
  var subtotal = 0;
  var total = 0;
  var totalcantidad = 0;
  var subcantidad = 0;
  var total_dinero = 0;
  var total_cantidad = 0;
  $("#inventable>tbody tr").each(function()
  {
    var compra = $(this).find(".precio_compra").val();
    var unidad = $(this).find(".unidad").val();
    var venta = $(this).find(".precio_venta").val();
    var cantidad = parseInt($(this).find(".cant").val());
    var vence = $(this).find(".vence").val();
    subtotal = compra * cantidad;
    if (isNaN(cantidad) == true)
    {
      cantidad = 0;
    }
    totalcantidad += cantidad;
    if (isNaN(subtotal) == true)
    {
      subtotal = 0;
    }
    total += subtotal;
  });
  if (isNaN(total) == true)
  {
    total = 0;
  }
  total_dinero = round(total,2);
  total_cantidad = round(totalcantidad,2);
  total_dinero = round(total,2);
  total_cantidad = round(totalcantidad,2);

  $('#total_dinero').html("<strong>" + total_dinero + "</strong>");
  $('#totcant').html(total_cantidad);

}
// actualize table
$(document).one("click", "#submit1", function()
{
  $('#submit1').attr('disabled', true);
  senddata();
});

function senddata()
{
  //Calcular los valores a guardar de cada item del inventario
  var i = 0;
  var error  = false;
  var datos = "";
  var id = $("select#tipo_entrada option:selected").val(); //get the value

  $("#inventable>tbody tr").each(function()
  {
    var id_prod = $(this).find(".id_p").text();
    var cant = $(this).find(".cant").val();
    if (cant != "" && parseInt(cant)>0)
    {
      datos += id_prod + "|" + cant + "#";
      i = i + 1;
    }
    else
    {
      error = true;
    }
  });

  var total = $('#total_dinero').text();
  var concepto = $('#concepto').val();
  var fecha1 = $('#fecha1').val();
  var destino = $('#destino').val();

  var dataString =
  {
    'process': "pass",
    'datos': datos,
    'cuantos': i,
  }
  if (!error)
  {

  }
  else
  {
    display_notify('Warning', 'Falta completar alguna cantidad!');
  }
}
function reload1()
{
  location.href = "ingreso_inventario.php";
}

$(document).on("click", "#generar", function (event) {
  var array_json = new Array();
  i=0;
  error=false;
	id_origen = $("#origen").val();
  $('#loadtable>tbody tr' ).each(function() {
    var id_prod = $(this).find("td:eq(0)").text();
    var cant = $(this).find(".cant").val();
    var cheque = $(this).find(".cheke").prop('checked');
    var existencia =$(this).find(".exis").text();
		var presentacion = $(this).find(".sel").val();
		var precio_compra = $(this).find(".precio_compra").text();
    if (cheque==true) {
      if (cant != "" && parseFloat(cant)>0 && cheque==true)
      {
        var obj = new Object();
        obj.id_prod = id_prod;
        obj.cantidad = cant;
        obj.existencia= existencia;
        obj.presentacion= presentacion;
        obj.precio_compra= precio_compra;
        //convert object to json string
        text = JSON.stringify(obj);
        array_json.push(text);

        i = i + 1;
      }
      else
      {
        error = true;
      }
    }

  });

  json_arr = '[' + array_json + ']';

  console.log(json_arr);
  console.log(i);
  console.log(error);

  if(i>0&&error==false)
  {
    $.ajax({
    	url: 'admin_movimiento_pendiente.php',
    	type: 'POST',
    	dataType: 'json',
    	data:"process=reponer"+"&id_origen="+id_origen+"&params="+json_arr,
			success: function(datax)
			{
				if(datax.typeinfo=="Success")
				{
					location.href = "admin_movimiento_pendiente.php";

				}
			}
    });
  }
  else
  {
    display_notify("Warning","Seleccione al menos un producto a reponer , cantidad de reposicion mayor a cero y existencias en ubicacion mayores a cero y menores o iguales a la cantidad de reposicion")
  }

});

function round(value, decimals)
{
  return Number(Math.round(value+'e'+decimals)+'e-'+decimals);
}
