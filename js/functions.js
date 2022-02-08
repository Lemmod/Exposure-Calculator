function calculate() {
            
    // Calculate new prices
    var current_asset = document.getElementById('current_asset').value;
    var current_price = document.getElementById('current_price').value;
    var add_asset = document.getElementById('add_asset').value;
    var add_price = document.getElementById('add_price').value;

    var current_worth =  (Number(current_asset) * Number(current_price));
    var added_worth =  (Number(add_asset) * Number(add_price));

    var total_new_worth = current_worth + added_worth;
    var total_new_assets = (Number(current_asset) + Number(add_asset));

    var new_avg_price = total_new_worth / total_new_assets;
    
    result.value = new_avg_price.toFixed(6);

    // Calculate new exposure

    var current_invested = document.getElementById('invested').value;
    var total_wallet = document.getElementById('total_wallet').value;

    document.getElementById('profit_asset').value = total_new_assets;
    document.getElementById('profit_entry').value = new_avg_price.toFixed(6);

    var new_exposure = ( Number(current_invested) + Number(added_worth) ) / Number (total_wallet);

    $( "#exposure_dca_result" ).empty().append( "Your new exposure will be approx. : <strong>" + new_exposure.toFixed(2) +"</strong>" ).show();
    
}

function calculate_profit() {
    
    // Calculate new prices
    var profit_asset = document.getElementById('profit_asset').value;
    var profit_entry = document.getElementById('profit_entry').value;
    var profit_price = document.getElementById('profit_price').value;

    var profit = ( Number(profit_price) - Number(profit_entry)) * Number(profit_asset);

    $( "#profit_result" ).empty().append( "Your profit will be aprrox. : <strong>$ " + profit.toFixed(2) +"</strong>" ).show();
    
}
