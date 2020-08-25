function func_autocomplete(field_id,display_field_id,source_file){
  $("#"+display_field_id).autocomplete({
    source:"ajax/"+source_file,
    autoFocus:true,
    minLength:2,
    create:function (){
      $(this).data('ui-autocomplete')._renderItem = function (ul, item) {
      return $('<li>')
      .append('<a>' + item.desc + '</a>' )
      .appendTo(ul);
      };
    },
    select:function(event,ui){
      $("#"+field_id).val(ui.item.no);
      $("#"+display_field_id).val(ui.item.label);
      if(ui.item.no=="0" || ui.item.no==""){
        $("#"+display_field_id).val("");
      }
      return true;
    }
  }).focus(function(){
    $("#"+field_id).val('');
    $(this).data("ui-autocomplete").search($(this).val());
  });
}

function func_customer_autocomplete(field_id,display_field_id){
  $("#"+display_field_id).autocomplete({
    source:"ajax/get_debtor_list.php",
    autoFocus:true,
    minLength:2,
    create:function (){
      $(this).data('ui-autocomplete')._renderItem = function (ul, item) {
      return $('<li>')
      .append('<a>' + item.desc + '</a>' )
      .appendTo(ul);
      };
    },
    select:function(event,ui){
      $("#"+field_id).val(ui.item.no);
      $("#"+display_field_id).val(ui.item.label);
      if(ui.item.no=="0" || ui.item.no==""){
        $("#"+display_field_id).val("");
      }
      return true;
    }
  }).focus(function(){
    $("#"+field_id).val('');
    $(this).data("ui-autocomplete").search($(this).val());
  });
}

function func_store_autocomplete(field_id,display_field_id){
  $("#"+display_field_id).autocomplete({
    source:"ajax/get_store_list.php",
    autoFocus:true,
    minLength:2,
    create:function (){
      $(this).data('ui-autocomplete')._renderItem = function (ul, item) {
      return $('<li>')
      .append('<a>' + item.desc + '</a>' )
      .appendTo(ul);
      };
    },
    select:function(event,ui){
      $("#"+field_id).val(ui.item.no);
      $("#"+display_field_id).val(ui.item.label);
      if(ui.item.no=="0" || ui.item.no==""){
        $("#"+display_field_id).val("");
      }
      return true;
    }
  }).focus(function(){
    $("#"+field_id).val('');
    $(this).data("ui-autocomplete").search($(this).val());
  });
}

function func_customer_autocomplete(field_id,display_field_id){
  $("#"+display_field_id).autocomplete({
    source:"ajax/get_product_list.php",
    autoFocus:true,
    minLength:2,
    create:function (){
      $(this).data('ui-autocomplete')._renderItem = function (ul, item) {
      return $('<li>')
      .append('<a>' + item.desc + '</a>' )
      .appendTo(ul);
      };
    },
    select:function(event,ui){
      $("#"+field_id).val(ui.item.no);
      $("#"+display_field_id).val(ui.item.label);
      if(ui.item.no=="0" || ui.item.no==""){
        $("#"+display_field_id).val("");
      }
      return true;
    }
  }).focus(function(){
    $("#"+field_id).val('');
    $(this).data("ui-autocomplete").search($(this).val());
  });
}