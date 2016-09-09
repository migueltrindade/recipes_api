<recipes-search>
  <p>  Hello! {opts.ryne}</p>

  <section each="{item in items}">
    <h4>{item.name}</h4>
    <a target="_blank" href="../node/{item.id}">View Item</a>
    <p>{item.description}</p>
  </section>
    <style>

    </style>

    <script>

        (function($, tag) {


              var recipesEndpoint = '/api/recipes';

                console.log(tag.opts);

              tag.afterItemsRequest = function(resp) {
                tag.items = resp.data;
                tag.count = resp.count;
                tag.update();
              };



              tag.one('update', function(){
                $.ajax({
                  url: recipesEndpoint
                })
                  .done(function(resp) {
                    tag.afterItemsRequest(resp);
                  });
              });



            })(jQuery, this);
    </script>

</recipes-search>