
    <h2>Categories</h2>


    <div class="category-list">
        <div class="row">
            {{ categories }}
                   <div class="col-sm-3">
                       <div class="category-item">
                            <div class="category-image"><img class="img-responsive" src='{{url:site}}files/large/{{file_id}}'></div>
                            <a href="{{nitrocart:uri}}/categories/category/{{slug}}"><h4>{{ name }}</h4></a>
                            <div class="category-description">{{ description }}</div>
                            <div class="buy-now"><a class="btn btn-adina" href="{{nitrocart:uri}}/categories/category/{{slug}}">BUY NOW</a></div>
                       </div>
                   </div>   
            {{ /categories }}
        </div>  
    </div>   

    <div class="row">
        <div class="col-sm-12">
            {{ if pagination:links }} 
                <div class="pagination"> 
                    {{ pagination:links }}
                </div>
            {{ endif}} 
        </div>
    </div>    