function _am(placeholder, container, closestItemOrContainer)
{
  //console.log(placeholder);
}

/**
 * What we want to do is loop through the table and assign an order 1-> n.
 * The upload the values to the controller->model to assign these values.
 *
 * @param  {[type]} $item     [description]
 * @param  {[type]} container [description]
 * @param  {[type]} _super    [description]
 * @param  {[type]} event     [description]
 * @return {[type]}           [description]
 */
 // set item relative to cursor position
var adjustment;

function _onDrag(item, position)
{
      item.css({
        left: position.left - adjustment.left,
        top: position.top - adjustment.top
      });

}

function _onDrop(item, container, _super, event)
{
    // Must implement to update css
    //item.removeClass("dragged").removeAttr("style");
    //$("body").removeClass("dragging");

    var clonedItem = $('<tr/>').css({height: 0});
    item.before(clonedItem);
    clonedItem.animate({'height': item.height()});

    item.animate(clonedItem.position(), function  () {
      clonedItem.detach()
      _super(item)
    });


    //Custom code to upload data to server
    var cat_list = new Array();
    var cat_list2 = new Array();
    var first = true;


    var table = $("#sortable_list");
    table.find('tr').each(function (i, el)
    {
        var tds = $(this).find('td');
        var cat_id = tds.eq(1).text();
        var cat_name = tds.eq(2).text();

      //Adding to the array
      console.log("Name: " + cat_name + ", ID: " + cat_id);

      if(first)
        cat_list2 += "" + cat_id + "";
      else
        cat_list2 += "," + cat_id + "";

      first = false;

    });


    domypost(cat_list2);

}

 // set item relative to cursor position
function _onDragStart(item, container, _super)
{
    var offset = item.offset(),
    pointer = container.rootGroup.pointer;

    adjustment = {
      left: pointer.left - offset.left,
      top: pointer.top - offset.top
    };

    _super(item, container);
}

function domypost(datain)
{

    var url = 'admin/nitrocart_categories/categories/reorder';

    var senddata = {cat_list:datain};

  $.post(url,senddata).done(function(data)
  {

      var obj = jQuery.parseJSON(data);

      if(obj.status == 'success')
      {
          console.log(obj.message);
      }
      else
      {
        alert(obj.message);
      }

  });

}