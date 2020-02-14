function categoriesDiscountLine(catID, discount) {
    let elem='<div><select name="gdCat[]">';
    for(let i in categories){
        elem+='<option value="'+i+'" '+(i==catID?" selected":"")+' class="level-'+categories[i][1]+'" >'+categories[i][0]+"</option>"
    }
    elem+='&nbsp;<input type="text" name="discount[]" value="'+discount+'"><button type="button" class="bDel">X</button></div>';
    return elem;
}
$=jQuery;

$(function() {
    if(typeof (discounts)!=='undefined') {
        for (let i in discounts) {
            $('#discounts').append(categoriesDiscountLine(discounts[i][0], discounts[i][1]))
        }
        $(document).on('click', '#bAdd', function () {
            $('#discounts').append(categoriesDiscountLine(0, 0))
        });
        $(document).on('click', '.bDel', function () {
            $(this).parent().remove();
        })
    }
})