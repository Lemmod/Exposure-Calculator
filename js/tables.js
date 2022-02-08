$(document).ready(function () {
    $('#trades').DataTable({
      responsive: true,
      columnDefs: [{
          responsivePriority: 1,
          targets: 0
        },
        {
          responsivePriority: 10001,
          targets: 4
        },
        {
          responsivePriority: 2,
          targets: -2
        }
      ],
      "searching": false,
      "paging": false,
      "ordering": true,
      "info": false,
      order: [ 
          ( [ 8, 'desc' ] ) 
      ] 
    });
    $('#realized_pnl_daily').DataTable({
      responsive: true,
      columnDefs: [{
          responsivePriority: 1,
          targets: 0
        },
        {
          responsivePriority: 10001,
          targets: 4
        },
        {
          responsivePriority: 2,
          targets: -2
        }
      ],
      "searching": false,
      "paging": true,
      "ordering": true,
      "info": false,
      order: [ 
          ( [ 0, 'desc' ] ) 
      ]
    });

    var table_trades = $('#trades').DataTable();  
    table_trades.on('click', 'tr', function () {
      var data = table_trades.row( this ).data();

      document.getElementById('current_asset').value = data[1];
      document.getElementById('current_price').value = data[3];
      document.getElementById('add_price').value = data[4];

      document.getElementById('profit_asset').value = data[1];
      document.getElementById('profit_entry').value = data[3];
      document.getElementById('profit_price').value = data[4];
      
  } );
  });