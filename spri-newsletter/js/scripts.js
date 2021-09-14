jQuery(".select-help").change(function() {
  jQuery("#"+jQuery(this).attr("id").replace("help", "help-url")).val(jQuery(this).val());
  jQuery("#"+jQuery(this).attr("id").replace("help", "help-title")+"").val(jQuery(this).children("option:selected").text());
});

jQuery(".newsdelete, .helpsdelete, .bannersdelete").click(function(e) {
  e.preventDefault();
  jQuery("#"+jQuery(this).data("delete")).remove();
  if(jQuery(this).hasClass("newsdelete")) {
    jQuery("select[name=news]").val(jQuery("select[name=news]").val() - 1);
    jQuery("#noticias > .noticia_drag").each(function(index) {
      jQuery(this).attr("order", index);
    });
  }
  if(jQuery(this).hasClass("helpsdelete")) {
    jQuery("select[name=helps]").val(jQuery("select[name=helps]").val() - 1);
    jQuery("#ayudas > .ayuda_drag").each(function(index) {
      jQuery(this).attr("order", index);
    });    
  }
  if(jQuery(this).hasClass("bannersdelete")) jQuery("select[name=banners]").val(jQuery("select[name=banners]").val() - 1);
});

jQuery(".select-post").change(function() {
  var post_id = jQuery(this).val();
  var new_post = posts[post_id];
  var intereses = new_post.intereses;

  if(new_post.texto_especial) {
    jQuery("#post_"+jQuery(this).data("post-id")+" textarea").empty();
    jQuery("#post_"+jQuery(this).data("post-id")+" textarea").append(new_post.texto_especial);
  } else {
    jQuery("#post_"+jQuery(this).data("post-id")+" textarea").empty();
  }

  if(new_post.formato) {
    var label = "#post_"+jQuery(this).data("post-id")+" select.formato";
    jQuery("#post_"+jQuery(this).data("post-id")+" select.formato").val(new_post.formato);
    if(new_post.formato == 'big') {
      jQuery(label).parent().find("textarea").removeClass("hidden");
      jQuery(label).parent().find("input.imagen").removeClass("hidden");
      jQuery(label).parent().find(".imagen_preview").removeClass("hidden");
    } else if(new_post.formato == 'medium') {
      jQuery(label).parent().find("textarea").addClass("hidden");
      jQuery(label).parent().find("input.imagen").removeClass("hidden");
      jQuery(label).parent().find(".imagen_preview").removeClass("hidden");
    } else if(new_post.formato == 'small') {
      jQuery(label).parent().find("textarea").addClass("hidden");
      jQuery(label).parent().find("input.imagen").addClass("hidden");
      jQuery(label).parent().find(".imagen_preview").addClass("hidden");
    } 
  } else {
    jQuery("#post_"+jQuery(this).data("post-id")+" select.formato").val("");             
  }

  if(new_post.imagen) {
    jQuery("#post_"+jQuery(this).data("post-id")+" .imagen").val(new_post.imagen);
    jQuery("#post_"+jQuery(this).data("post-id")+" .imagen_preview").empty();
    jQuery("#post_"+jQuery(this).data("post-id")+" .imagen_preview").append("<img src='"+new_post.imagen+"' />");
  } else {
    jQuery("#post_"+jQuery(this).data("post-id")+" .imagen").val("");
    jQuery("#post_"+jQuery(this).data("post-id")+" .imagen_preview").empty();             
  }

  jQuery("#post_"+jQuery(this).data("post-id")+" input[type=checkbox]").each(function() {
    if(Array.isArray(intereses) && intereses.includes(jQuery(this).val())) jQuery(this).prop("checked", true);
    else jQuery(this).removeAttr('checked');
  });
});

jQuery(".pop-up-open, .pop-up-close").click(function(e) {
  e.preventDefault();
  jQuery(".pop-up-bg").toggleClass("opened");
});

jQuery(".newsdown").click(function(e) {
  e.preventDefault();
  var order = parseInt(jQuery(this).parents(".noticia_drag").attr("order"));
  jQuery(this).parents(".noticia_drag").insertAfter(".noticia_drag[order="+(order+1)+"]");
  jQuery("#noticias > .noticia_drag").each(function(index) {
    jQuery(this).attr("order", index);
  });
});

jQuery(".newsup").click(function(e) {
  console.log("SUBIR");
  e.preventDefault();
  var order = parseInt(jQuery(this).parents(".noticia_drag").attr("order"));
  jQuery(this).parents(".noticia_drag").insertBefore(".noticia_drag[order="+(order-1)+"]");
  jQuery("#noticias > .noticia_drag").each(function(index) {
    jQuery(this).attr("order", index);
  });
});

jQuery("select.formato").on('change', function() {
  if(jQuery(this).val() == 'big') {
    jQuery(this).parent().find("textarea").removeClass("hidden");
    jQuery(this).parent().find("input.imagen").removeClass("hidden");
    jQuery(this).parent().find(".imagen_preview").removeClass("hidden");
  } else if(jQuery(this).val() == 'medium') {
    jQuery(this).parent().find("textarea").addClass("hidden");
    jQuery(this).parent().find("input.imagen").removeClass("hidden");
    jQuery(this).parent().find(".imagen_preview").removeClass("hidden");
  } else if(jQuery(this).val() == 'small') {
    jQuery(this).parent().find("textarea").addClass("hidden");
    jQuery(this).parent().find("input.imagen").addClass("hidden");
    jQuery(this).parent().find(".imagen_preview").addClass("hidden");
  } 
});


jQuery(".helpsdown").click(function(e) {
  console.log("BAJAR");
  e.preventDefault();
  var order = parseInt(jQuery(this).parents(".ayuda_drag").attr("order"));
  jQuery(this).parents(".ayuda_drag").insertAfter(".ayuda_drag[order="+(order+1)+"]");
  jQuery("#ayudas > .ayuda_drag").each(function(index) {
    jQuery(this).attr("order", index);
  });
});

jQuery(".helpsup").click(function(e) {
  console.log("SUBIR");
  e.preventDefault();
  var order = parseInt(jQuery(this).parents(".ayuda_drag").attr("order"));
  jQuery(this).parents(".ayuda_drag").insertBefore(".ayuda_drag[order="+(order-1)+"]");
  jQuery("#ayudas > .ayuda_drag").each(function(index) {
    jQuery(this).attr("order", index);
  });
});
