<?php
// Load all logic in to this template. I know not the pretiest way but as long it works I am happy ;)
include (__DIR__.'/loader.php'); 
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">

  <title>Exposure - <?php echo $selected_account_name; ?></title>

  <!-- Datatables -->
  <link rel="stylesheet" type="text/css"
    href="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.11.3/datatables.min.css" />
  <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.11.4/r-2.2.9/datatables.min.js">
  </script>


  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
  </script>

  <!-- Chart -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"
    integrity="sha512-TW5s0IT/IppJtu76UbysrBH9Hy/5X41OTAbQuffZFU6lQ1rdcLHzpU5BzVvr/YFykoiMYZVWlr/PX1mDcfM9Qg=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

  <!-- Icons -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">

  <!-- Custom CSS / JS -->
  <link rel="stylesheet" type="text/css" href="css/style.css" />
  <script type="text/javascript" src="js/functions.js"></script>

</head>

<body class="loggedin">
  <script type="text/javascript" src="js/tables.js"></script>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Exposure Calculator</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown"
              aria-expanded="false">
              Accounts
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
              <?php
              $i = 1;
              $total = count($accounts);
              foreach ($accounts as $account) {                    
                  if ($account['account_id'] == $selected_account) {
                      echo '<li><a class="dropdown-item" href="#"><strong>'.$account['account_name'].'</strong></a></li>';
                  } else {
                    echo '<li><a class="dropdown-item" href="index.php?account='.$account['account_id'].'&lookback='.$lookback_period.'">'.$account['account_name'].'</a></li>';
                  }
              }
            ?>
            </ul>
          </li>
      </div>
    </div>
  </nav>

  <div class="content">

    <!-- Account info -->
    <div class="workspace">
      <h2>Account : <?php echo $selected_account_name; ?></h2>
    </div>

    <!-- KPI -->
    <div class="workspace">
      <h2>KPI</h2>
      <div class="row row-cols-9 row-cols-md-9 g-10">
        <div class="col">
          <div class="card text-white <?php echo $kpi_exposure_color; ?> bg-dark mb-3">
            <div class="card-header">Exposure</div>
            <div class="card-body">
              <h2><?php echo $exposure; ?></h2>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card text-white bg-dark mb-3">
            <div class="card-header">Wallet</div>
            <div class="card-body">
              <h2><?php echo '$ '. number_format( $total_wallet , 2); ?></h2>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card text-white <?php echo $kpi_pnl_color; ?> bg-dark mb-3">
            <div class="card-header">Unrealized PnL</div>
            <div class="card-body ">
              <h2><?php echo '$ '. number_format( $total_unrealized , 2) ?></h2>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card text-white bg-dark mb-3">
            <div class="card-header">Total margin balance</div>
            <div class="card-body">
              <h2><?php echo '$ '.number_format( $total_margin_balance , 2); ?></h2>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card text-white bg-dark mb-3">
            <div class="card-header">Invested</div>
            <div class="card-body">
              <h2><?php echo '$ '.number_format( $invested , 2); ?></h2>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card text-white bg-dark mb-3">
            <div class="card-header">Current Worth</div>
            <div class="card-body">
              <h2><?php echo '$ '.number_format( $current_worth , 2); ?></h2>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card text-white bg-dark mb-3">
            <div class="card-header">Margin Ratio</div>
            <div class="card-body">
              <h2><?php echo $margin_ratio; ?></h2>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card text-white bg-dark mb-3">
            <div class="card-header">Max Drop <span class="text-white " data-bs-toggle="tooltip"
                data-bs-placement="bottom" title="* Room till liquidation based on the (Total margin balance - Maintainance margin) / Current Worth. All based on
        your current maintainance margin. In other words when your current worth drops <?php echo $max_drop.'%'; ?> you
        are liquidated. Use this as an indicator , exchanges may have different rules for liquidation. This KPI should
        give you some insights on your risk">
                <i class="fas fa-info-circle"></i>
              </span></div>
            <div class="card-body">
              <h2><?php echo $max_drop; ?></h2>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card text-white bg-dark mb-3">
            <div class="card-header">Maintainance margin</div>
            <div class="card-body">
              <h2><?php echo '$ '.number_format( $total_maintainance_margin , 2); ?></h2>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Open trades -->
    <div class="workspace">
      <h2>Open trades</h2>

      <?php
  echo $trades_table->getTable();
  ?>
    </div>

    <!-- Calculators -->
    <div class="workspace">
      <h2>Calculators</h2>
      <div class="row row-cols-2 row-cols-md-2 g-2">
        <div class="col">
          <div class="card text-white bg-dark mb-3">
            <div class="card-header">DCA Calculator <span class="text-white " data-bs-toggle="tooltip"
                data-bs-placement="bottom" title="Calculate the new avg DCA price. Select an row from the trades table to insert
                values. Add the new assets and price to calculate the new average price">
                <i class="fas fa-info-circle"></i>
              </span></div>
            <div class="card-body">

              <input size="8" type="hidden" id="invested"
                value="<?php echo ($invested + $total_maintainance_margin); ?>">
              <input size="8" type="hidden" id="total_wallet" value="<?php echo $total_wallet; ?>">
              <table id="dca_calc" class="table table-hover table-striped table-bordered table-dark"
                style="width:99.8%">
                <thead>
                  <tr>
                    <th>...</th>
                    <th>Assets</th>
                    <th>Price</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td> Current </td>
                    <td> <input size="8" type="text" id="current_asset"> </td>
                    <td> <input size="8" type="text" id="current_price"> </td>
                  </tr>
                  <tr>
                    <td> DCA </td>
                    <td> <input size="8" type="text" id="add_asset"> </td>
                    <td> <input size="8" type="text" id="add_price"> </td>
                  </tr>
                  <tr>
                    <td> Avg. Price </td>
                    <td> <input type="button" onclick="calculate()" value="Calculate" /> </td>
                    <td> <input size="8" type="text" id="result"> </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card text-white bg-dark mb-3">
            <div class="card-header">Profit Calculator <span class="text-white " data-bs-toggle="tooltip"
                data-bs-placement="bottom" title="Calculate the profit by setting the total assets , entry price and the take profit
                price. Profits are calculated without fees.">
                <i class="fas fa-info-circle"></i>
              </span></div>
            <div class="card-body">
              <table id="profit_calc" class="table table-hover table-striped table-bordered table-dark"
                style="width:99.8%">
                <thead>
                  <tr>
                    <th>...</th>
                    <th>Value</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td> Current assets </td>
                    <td> <input size="8" type="text" id="profit_asset"> </td>
                  </tr>
                  <tr>
                    <td> Entry price </td>
                    <td> <input size="8" type="text" id="profit_entry"> </td>
                  </tr>
                  <tr>
                    <td> Profit price </td>
                    <td> <input size="8" type="text" id="profit_price"> <input type="button"
                        onclick="calculate_profit()" value="Calculate" /></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div id="exposure_dca_result" style="display : none;"> Your new exposure will be approx. :</div>
        <div id="profit_result" style="display : none;"></div>
      </div>
    </div>

    <!-- Daily PnL -->
    <div class="workspace">
      <h2>Daily PnL</h2>
      <?php
      echo $daily_table->getTable();
      ?>
    </div>

    <!-- Charts -->
    <div class="workspace">
      <h2>Charts</h2>




      <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="graph_unrealized-tab" data-bs-toggle="tab"
            data-bs-target="#graph_unrealized" type="button" role="tab" aria-controls="graph_unrealized"
            aria-selected="true">Unrealized PnL</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="graph_expsoure-tab" data-bs-toggle="tab" data-bs-target="#graph_expsoure"
            type="button" role="tab" aria-controls="graph_expsoure" aria-selected="false">Exposure</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="graph_wallet-tab" data-bs-toggle="tab" data-bs-target="#graph_wallet"
            type="button" role="tab" aria-controls="graph_wallet" aria-selected="false">Wallet Balance</button>
        </li>
        <li>
          <form>
            <input type="hidden" name="account" value="<?php echo $selected_account; ?>" />
            Days to look back :
            <select onchange="this.form.submit()" name="lookback">
              <?php 
            
              for ($x = 0; $x <= 30; $x++) {
                  $selected = $lookback_period == $x ? 'selected' : '';
                  echo '<option '.$selected.' value="'.$x.'">'.$x.'</option>';
              }
              ?>
            </select>
          </form>
        </li>
      </ul>
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="graph_unrealized" role="tabpanel"
          aria-labelledby="graph_unrealized-tab">
          <div class="chart-container">
            <canvas id="chart_unrealized"></canvas>
          </div>
        </div>
        <div class="tab-pane fade" id="graph_expsoure" role="tabpanel" aria-labelledby="graph_expsoure-tab">
          <div class="chart-container">
            <canvas id="chart_exposure"></canvas>
          </div>
        </div>
        <div class="tab-pane fade" id="graph_wallet" role="tabpanel" aria-labelledby="graph_wallet-tab">
          <div class="chart-container">
            <canvas id="chart_wallet"></canvas>
          </div>
        </div>
      </div>

      <!--  -->

    </div>

    <!-- TV Embed -->
    <div class="workspace">
      <h2>TradingView</h2>

      <div class="tradingview-widget-container">
        <div id="tradingview_1d470" style="height : 600px;  width: 99.8%"></div>

        <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
        <script type="text/javascript">
          new TradingView.widget({
            "autosize": true,
            "symbol": "BINANCE:ETHUSDTPERP",
            "interval": "5",
            "timezone": "Etc/UTC",
            "theme": "dark",
            "style": "1",
            "locale": "en",
            "toolbar_bg": "#f1f3f6",
            "enable_publishing": false,
            "allow_symbol_change": true,
            "container_id": "tradingview_1d470"
          });
        </script>
      </div>
    </div>

  </div>

  <script>
    // General chart options
    var chart_options = {
      maintainAspectRatio: false,
      scales: {
        y: {
          stacked: true,
          grid: {
            display: true,
            color: "rgba(255,255,255,0.2)"
          } 
        },
        x: {
          grid: {
            display: false
          }
        }
      } 
    };

    var data_options = [{

    }]

    // Unrealized PnL chart
    var unrealized_data = {
      labels: <?php echo json_encode($x_axis_unrealized); ?> ,
      datasets : [{
        label: "Unrealized PnL",
        fill: true,
        backgroundColor: "rgba(255,255,255,0.2)",
        borderColor: "rgba(255,255,255,1)",
        borderWidth: 1,
        hoverBackgroundColor: "rgba(255,1,1,0.4)",
        hoverBorderColor: "rgba(255,1,1,1)",
        tension: 0.4,
        data: <?php echo json_encode($y_axis_unrealized); ?> ,
      }]
    };

    new Chart('chart_unrealized', {
      type: 'line',
      options: chart_options,
      data: unrealized_data
    });

    // Exposure chart
    var exposure_data = {
      labels: <?php echo json_encode($x_axis_exposure); ?> ,
      datasets : [{
        label: "Exposure",
        fill: true,
        backgroundColor: "rgba(255,255,255,0.2)",
        borderColor: "rgba(255,255,255,1)",
        borderWidth: 1,
        hoverBackgroundColor: "rgba(255,1,1,0.4)",
        hoverBorderColor: "rgba(255,1,1,1)",
        tension: 0.4,
        data: <?php echo json_encode($y_axis_exposure); ?> ,
      }]
    };

    new Chart('chart_exposure', {
      type: 'line',
      options: chart_options,
      data: exposure_data
    });

    // Wallet chart
    var wallet_data = {
      labels: <?php echo json_encode($x_axis_wallet); ?> ,
      datasets : [{
        label: "Wallet USD",
        fill: true,
        backgroundColor: "rgba(255,255,255,0.2)",
        borderColor: "rgba(255,255,255,1)",
        borderWidth: 1,
        hoverBackgroundColor: "rgba(255,1,1,0.4)",
        hoverBorderColor: "rgba(255,1,1,1)",
        tension: 0.4,
        data: <?php echo json_encode($y_axis_wallet); ?> ,
      }]
    };

    new Chart('chart_wallet', {
      type: 'line',
      options: chart_options,
      data: wallet_data
    });
  </script>
  <!-- Tooltip trigger -->
  <script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    });
  </script>
</body>

</html>