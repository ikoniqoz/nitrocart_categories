    {{if product_count==0}}
        <div class="no-products-line">
            <h2>There are no products available.</h2>
        </div>
    </div>  <!-- end of initial template col -->
</div>  <!-- end of initial template row -->
    
{{else}}
        <h2>
            {{if view_title=='Products'}}
                Our           
            {{endif}}
            {{view_title}} 
        </h2>
    </div>  <!-- end of initial template col -->
</div>  <!-- end of initial template row -->

    
    {{ products }}

        <div class="row the-product" itemscope itemtype="http://schema.org/Product">
            <div class="col-sm-2">
                <div class="product-image">  
                    {{shop_images:cover product_id="{{id}}"}}
                        <a href="{{path}}" class="fancybox-product" rel="product-image" data-src="{{path}}" title="{{ name }}"> 
                            <img class="img-responsive" src="{{src}}/200" />
                        </a>    
                    {{/shop_images:cover}}
                </div>     
            </div>
            <div class="col-sm-6">
                <h3><a itemprop="url" href="{{x:uri}}/products/product/{{ slug }}">{{name}}</a></h3>

                <div class="item-description">{{ description }}</div>
                
                <div class="more-details">
                      
                    {{shop_adina:if_description id='{{id}}'}}
                         <a href="{{x:uri}}/products/product/{{ slug }}"><button class="btn btn-default btn-sm"> MORE INFO </button></a>
                    {{/shop_adina:if_description}}
                    
                </div>

            </div>
            <div class="col-sm-4">
                <form action="{{x:uri}}/cart/add"  id="form_{{id}}" name="form_{{id}}" method="post" class="item-variances">

                    <div class="product-price">
                        {{nitrocart:fromprice product="{{id}}" x="" fromtext="<span>from</span>" na="N/A" }}
                    </div>

                    {{nitrocart:htmlvariances product='{{id}}' x='SELECT,PRICE' }}

                    <div class="item-action" >
                        <div>
                            <input class="item-qty" type='text' name='qty' placeholder='1'>
                        </div>

                        <button type='submit' class="btn btn-danger btn-sm">
                            ADD TO CART &nbsp; <i class="fa fa-shopping-cart fa-lg"></i>
                        </button>
    
                    </div>
                </form>


            </div> <!-- / col -->
        </div> <!-- /row -->    

    {{ /products }}

{{ endif }} 




{{ if pagination:links }} 
    <div class="pagination"> 
        {{ pagination:links }}
    </div>
{{ endif}} 




    <!--  Always display title, even if most of the links do not display below, the static  'all categories' link will -->
    <h2>More...</h2>

    <!--  Only display link if the current category has a parent category -->
    {{if category:parent_id > 0 }}
        {{nitrocart_categories:category id="{{category:parent_id}}" }}
            <a class='btn btn-sm'  href="{{nitrocart:uri}}/categories/category/{{slug}}">&larr; back to  {{name}}</a>  |
        {{/nitrocart_categories:category}}
    {{endif}}

    
    <!--  Static link for consistency -->        
    <a  class='btn btn-sm'  href="{{nitrocart:uri}}/categories/">all categories</a>     


    <!--  Helpful resource lin kto cart -->
    {{ nitrocart:cart }}
        {{if item_count > 0}}
            | <a  class='btn btn-sm'  href="{{nitrocart:uri}}/cart/">view cart</a>    
        {{ endif }}
    {{ /nitrocart:cart }}



        <!--  Displays link if the current catgeory has more than 3 products -->
        {{ if category:product_count > 3 }}
            | <a class='btn btn-sm' href="{{nitrocart:uri}}/categories/products/{{category:slug}}">discover all {{category:product_count}} products in this category &rarr;</a>  
        {{ endif }}
