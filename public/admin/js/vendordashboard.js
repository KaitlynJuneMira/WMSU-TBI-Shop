var vendorsales = document.getElementById('vendorsales');
var vendorsalesVar;
function rendervendorsales() {
  var year = $('#vendorYear').val()
  var data = [0,0,0,0,0,0,0,0,0,0,0,0];
  var labels = ['January','February','March','April','May','June','July','August','September','October','November','December']
  var color = []
  var paid = $('#vendorsalesPaid').is(":checked")
  const randomBetween = (min, max) => min + Math.floor(Math.random() * (max - min + 1));
  $.ajax({url: 'dashboard-vendorsales/'+year+'/'+paid, 
    success: function(result){
      result.forEach(element  => {
        data[element.month_num-1] = element.total_per_month;
        labels[element.month_num-1] = labels[element.month_num-1]+' ('+element.total_per_month+')'
        color.push('rgb('+(randomBetween(0, 255))+','+(randomBetween(0, 255))+','+(randomBetween(0, 255))+')')
      });
      if(vendorsalesVar){
        vendorsalesVar.destroy();
      }
      vendorsalesVar = new Chart(vendorsales, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Revenue',
            data: data,
            backgroundColor: color,
            borderWidth: 1,
          }],
        },
      });
    }
  });
}
rendervendorsales();




var vendorave = document.getElementById('vendorave');
var vendoraveVar;
function rendervendorAve(){
  var year = $('#AverageOrderValue').val()
  var data = [0,0,0,0,0,0,0,0,0,0,0,0];
  var labels = ['January','February','March','April','May','June','July','August','September','October','November','December']
  var color = []
  var paid = $('#vendorAvePaid').is(":checked")
  const randomBetween = (min, max) => min + Math.floor(Math.random() * (max - min + 1));
  $.ajax({url: 'dashboard-vendoraverageOrderValue/'+year+'/'+paid, 
    success: function(result){
      result.forEach(element  => {
        data[element.month_num-1] = element.average_order_value_per_month;
        labels[element.month_num-1] = labels[element.month_num-1]+' ('+element.average_order_value_per_month+')'
        color.push('rgb('+(randomBetween(0, 255))+','+(randomBetween(0, 255))+','+(randomBetween(0, 255))+')')
      });
      if(vendoraveVar){
        vendoraveVar.destroy();
      }
      vendoraveVar = new Chart(vendorave, {
        type: 'bar',
        data: {
            labels: labels,
              datasets: [{
                label: 'Average Order Value',
                data: data,
                backgroundColor: color,
                borderWidth: 1,
              }]
        }
      });
    }
  });

  

}
rendervendorAve()

var TopSellingProducts = document.getElementById('topselling');
var TopSellingProductsVar;
function rendervendorTop() {
  var year = $('#TopSellingProducts').val()
  var paid = $('#vendorTopPaid').is(":checked")
  var data = [];
  var labels = [];
  var color = []
  const randomBetween = (min, max) => min + Math.floor(Math.random() * (max - min + 1));
  $.ajax({url: 'dashboard-vendortopSellingProducts/'+year+'/'+paid, 
    success: function(result){
      console.log(result)
      result.forEach(element  => {
        labels.push(element.product_name+' ('+element.total_quantity_orders+')')
        data.push(element.total_quantity_orders)
        color.push('rgb('+(randomBetween(0, 255))+','+(randomBetween(0, 255))+','+(randomBetween(0, 255))+')')
      });
      if(TopSellingProductsVar){
        TopSellingProductsVar.destroy();
      }
      
      TopSellingProductsVar = new Chart(TopSellingProducts, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Top Products By Sales',
                data: data,
                backgroundColor: color,
                borderWidth: 1,
            }]
        },
        options: {
            indexAxis: 'y',  // Set the index axis to 'y' for horizontal bar chart
            elements: {
                bar: {
                    borderWidth: 2,
                }
            },
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                },
                title: {
                    display: true,
                    text: 'Horizontal Bar Chart'
                }
            }
        },
      });
    }
  });
}
rendervendorTop();

function renderTurnOver(){
  const ctx = document.getElementById('inventory');

  new Chart(ctx,{
    type: 'line',
    data: {
      labels: [
        'Jannuary',
        'February',
        'April',
        'May'
      ],
      datasets: [{
        label: '',
        data: [65, 59, 80, 81, 56, 55, 40],
        fill: false,
        borderColor: 'rgb(75, 192, 192)',
        tension: 0.1,
        borderWidth: 1,
      }]
    },
  });
}
renderTurnOver()

var OrderStatus = document.getElementById('cancelation');
var orderStatusVar;
function renderCancelation(){
  var year = $('#OrderStatusYear').val()
  var data = [];
  var labels = [];
  var color = []
  const randomBetween = (min, max) => min + Math.floor(Math.random() * (max - min + 1));
  $.ajax({url: 'dashboard-vendororderStatus/'+year, 
    success: function(result){
      result.forEach(element  => {
        labels.push(element.item_status+' ('+element.item_status_count+') ')
        data.push(element.item_status_count)
        color.push('rgb('+(randomBetween(0, 255))+','+(randomBetween(0, 255))+','+(randomBetween(0, 255))+')')
      });
      if(orderStatusVar){
        orderStatusVar.destroy();
      }
      orderStatusVar = new Chart(OrderStatus,{
        type: 'pie',
        data: { 
          labels: labels,
          datasets: [{
            data: data,
            backgroundColor:color,
            hoverOffset: 4,
            borderWidth: 0.08,
            borderColor: ('#111112')
          }
        ]},
        }
      );
    }
  });
}
renderCancelation()


var salesgrowth = document.getElementById('salesgrowth');
var salesgrowthVar;
function renderSalesGrownth(){
  var year = $('#SalesGrowthOverTime').val()
  var data = [0,0,0,0,0,0,0,0,0,0,0,0];
  var labels = ['January','February','March','April','May','June','July','August','September','October','November','December']
  var color = []
  let prev_year_month = 0;
  const randomBetween = (min, max) => min + Math.floor(Math.random() * (max - min + 1));
  for (let index = 0; index < labels.length; index++) {
    color.push('rgb('+(randomBetween(0, 255))+','+(randomBetween(0, 255))+','+(randomBetween(0, 255))+')')
  }
  $.ajax({url: 'dashboard-vendorsalesGrowthOverTimePrev/'+year, 
  success: function(result){
    result.forEach(element  => {
      if(element.month == 'December'){
        prev_year_month = element.total_per_month
      }
    });
    $.ajax({url: 'dashboard-vendorsalesGrowthOverTime/'+year, 
    success: function(result){
      result.forEach(element  => {
        labels.push(element.product_name)
        data[element.month_num-1] = element.total_per_month;
       
      });
      if(salesgrowthVar){
        salesgrowthVar.destroy();
      }
      prev_year_month = data[0];
      for (let index = 0; index < data.length; index++) {
        var value;
        if(data[index] == 0){
          value = 0
        }else{
          if(prev_year_month == 0){
            value =  data[index];
          }else{
            value = ((data[index] - prev_year_month) / prev_year_month) *100;
          }
        }
        prev_year_month = data[index]
        data[index] = value
        if(data[index]>0){
          labels[index] = labels[index]+' ('+data[index]+')'
        }
      }
      salesgrowthVar = new Chart(salesgrowth,{
        type: 'line',
        data: {
          labels:labels,
          datasets: [{
            label: '',
            data: data,
            fill: true,
            borderColor: color,
            tension: 0.1,
            borderWidth: 1,
          }]
        },
      });
    }
  });
    }
  });

 
}
renderSalesGrownth()