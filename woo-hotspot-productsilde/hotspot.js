jQuery(function($){
    let products = window.hotspotProducts || [];
    let current = 0;

    function loadProduct(id){
        $.post(hotspot_ajax.ajax_url,{
            action:'hotspot_load_product',
            product_id:id
        },function(res){
            $("#product-output").html(res);
        });
    }

    $(document).on("click",".hotspot-dot",function(){
        let pid=$(this).data("product");
        current = products.indexOf(pid);
        loadProduct(pid);
    });

    $(document).on("click",".hotspot-next",function(){
        current = (current+1) % products.length;
        loadProduct(products[current]);
    });

    $(document).on("click",".hotspot-prev",function(){
        current = (current-1+products.length) % products.length;
        loadProduct(products[current]);
    });

    loadProduct(products[0]);
});