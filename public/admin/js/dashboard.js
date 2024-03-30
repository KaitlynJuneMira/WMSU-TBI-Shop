

var OverallRevenueChart = document.getElementById('barchart');
var OverallRevenueChartVar ;
function renderSales(){
    var year = $('#overallRevenue').val()
    var paid = $('#Salespaid').is(":checked")
    var monthdata = [0,0,0,0,0,0,0,0,0,0,0,0]
    var monthlabel = ['January','February','March','April','May','June','July','August','September','October','November','December']
    var bgcolor = []
    const randomBetween = (min, max) => min + Math.floor(Math.random() * (max - min + 1));
    $.ajax({url: 'dashboard-adminoverallRevenue/'+year+'/'+paid, 
      success: function(result){
        result.forEach(element  => {
          monthdata[element.month-1] = element.total_per_month
          monthlabel[element.month-1] = monthlabel[element.month-1]+' ('+element.total_per_month+')'
          bgcolor.push('rgb('+(randomBetween(0, 255))+','+(randomBetween(0, 255))+','+(randomBetween(0, 255))+')')
        });
        if(OverallRevenueChartVar){
          OverallRevenueChartVar.destroy();
        }
        OverallRevenueChartVar = new Chart(OverallRevenueChart, {
          type: 'bar',
          data: {
            labels: monthlabel,
              datasets: [{
                label: 'Overall Revenue',
                data: monthdata,
                backgroundColor: bgcolor,
                borderWidth: 1,
              }]
            },
          options: {
            scales: {
              y: {
                beginAtZero: true
              }
            }
          }
        });
      }
    });
}
renderSales()


var TopProducts = document.getElementById('topProdChart');  
var TopProductsVar ;
function renderTopProducts(){
  // console.log($('#TopProductspaid').is(":checked"));
  var year = $('#topProducts').val()
  var paid = $('#TopProductspaid').is(":checked")
  var limit = $('#topProductLimit').val()
  var labels = []
  var data = []
  var color = [];
  const randomBetween = (min, max) => min + Math.floor(Math.random() * (max - min + 1));
  $.ajax({url: 'dashboard-admintopProducts/'+year+'/'+paid+'/'+limit, 
    success: function(result){
      // console.log(result);
      result.forEach(element  => {
        labels.push(element.product_name+' ('+element.total_product_sales+')')
        data.push(element.total_product_sales)
        color.push('rgb('+(randomBetween(0, 255))+','+(randomBetween(0, 255))+','+(randomBetween(0, 255))+')')
      });
        if(TopProductsVar){
          TopProductsVar.destroy();
        }
        TopProductsVar = new Chart(TopProducts, {
          type: 'bar',
          data: {
              labels: labels,
                datasets: [{
                  label: 'Top Products',
                  data: data,
                  backgroundColor: color,
                  borderWidth: 1,
                }]
          },
          options: {
            indexAxis: 'y',
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
                text: 'Chart.js Horizontal Bar Chart'
              }
            }
          },
       
      });
    }
  });
      
 

}
renderTopProducts()

var topSellers = document.getElementById('topSeller');    
var topSellersvar;
function renderTopSeller(){
  var sellerLabels = [];
  var sellerTotolOrders = [];
  var color = [];
  var year = $('#topSellers').val()
  var paid = $('#TopSellerpaid').is(":checked")
  const randomBetween = (min, max) => min + Math.floor(Math.random() * (max - min + 1));
  $.ajax({url: 'dashboard-admintopSellers/'+year+'/'+paid, 
    success: function(result){
      result.forEach(element  => {
        sellerLabels.push(element.name+' ('+element.total_revenue+')')
        sellerTotolOrders.push(element.total_revenue)
        color.push('rgb('+(randomBetween(0, 255))+','+(randomBetween(0, 255))+','+(randomBetween(0, 255))+')')
      });
      if(topSellersvar){
        topSellersvar.destroy();
      }
      topSellersvar = new Chart(topSellers, {
      type: 'bar',
      data: {
        labels: sellerLabels,
          datasets: [{
            axis: 'y',
            label: 'Overall Revenue',
            data: sellerTotolOrders,
            fill: false,
            backgroundColor: color,
            borderWidth: 1,
          }]
        },
        options: {
          indexAxis: 'y',
        }
      });
    }
  });
}
renderTopSeller()



var topCategory = document.getElementById('category');
var topCategoryvar;
function renderCategory(){
  var labels =[];
  var data = [];
  var colors = [];
  var year = $('#topCategory').val()
  var paid = $('#Categorypaid').is(":checked")
  const randomBetween = (min, max) => min + Math.floor(Math.random() * (max - min + 1));
  $.ajax({url: 'dashboard-admintopCategory/'+year+'/'+paid, 
    success: function(result){
      result.forEach(element  => {
        labels.push(element.category_name+' ('+element.total_product_qty+')')
        data.push(element.total_product_qty)
        colors.push('rgb('+(randomBetween(0, 255))+','+(randomBetween(0, 255))+','+(randomBetween(0, 255))+')')
      });
      if(topCategoryvar){
        topCategoryvar.destroy();
      }
      topCategoryvar = new Chart(topCategory, {
        type: 'bar',
        data: {
          labels:labels,
            datasets: [{
              axis: 'y',
              label: 'Top Category',
              data: data,
              backgroundColor: colors,
              borderWidth: 1,
            }]
      },
        options: {
          indexAxis: 'y',
        }
    });
    }   
  });
}
renderCategory()

var FulfilledOrders = document.getElementById('barangay');    
var FulfilledOrdersVar;
function renderFulfilledOrders(){
  var year = $('#FulfilledOrders').val()
  var paid = $('#OrderStatuspaid').is(":checked")
  var data = []
  var label = []
  var color = []
  const randomBetween = (min, max) => min + Math.floor(Math.random() * (max - min + 1));
  $.ajax({url: 'dashboard-adminfulfilledOrders/'+year+'/'+paid, 
    success: function(result){
      result.forEach(element  => {
        label.push(element.date_ordered+' ('+element.total_ordered_per_day+')')
        data.push(element.total_ordered_per_day
          )
        color.push('rgb('+(randomBetween(0, 255))+','+(randomBetween(0, 255))+','+(randomBetween(0, 255))+')')
      });
      if(FulfilledOrdersVar){
        FulfilledOrdersVar.destroy();
      }

      FulfilledOrdersVar = new Chart(FulfilledOrders, {
        type: 'bar',
        data: {
            labels: label,
              datasets: [{
                axis: 'y',
                label: 'Orders per day',
                data: data,
                fill: false,
                backgroundColor: color,
                borderWidth: 1,
              }]
        },
        options: {
          indexAxis: 'x',
        }
      });
    }
  });
}
renderFulfilledOrders()

var orderStatus = document.getElementById('sellTrough');
var orderStatusVar ;
function renderStatus(){
  var labels =[];
  var data = [];
  var colors = [];
  var year = $('#orderStatus').val()
  const randomBetween = (min, max) => min + Math.floor(Math.random() * (max - min + 1));
  $.ajax({url: 'dashboard-adminorderStatus/'+year, 
    success: function(result){
      result.forEach(element  => {
        labels.push(element.item_status+' ('+element.item_status_count+') ')
        data.push(element.item_status_count)
        colors.push('rgb('+(randomBetween(0, 255))+','+(randomBetween(0, 255))+','+(randomBetween(0, 255))+')')
      });
      if(orderStatusVar){
        orderStatusVar.destroy();
      }
      orderStatusVar = new Chart(orderStatus,{
        type: 'doughnut',
        data: { 
          labels: labels,
        datasets: [{
          label: 'Order Status',
          data:data,
          backgroundColor:colors,
          hoverOffset: 4
        }]},
      });

    }
  });  
}
renderStatus()


var DrillAnalyticsRevenue = document.getElementById('DrillAnalyticschart');
var DrillAnalyticsRevenueVar ;

function DrillAnalyticsRevenueChangeYear(){
  var year = $('#DrillAnalyticsRevenueYear').val()
  var vendor = $('#DrillAnalyticsRevenueVendor').val('All');
  $.ajax({url: 'dashboard-getVendorDetails/'+year,
    success: function(result){
    // console.log(result)
    $('#DrillAnalyticsRevenueVendor').html('<option value="All">All Vendor</option>')
    result.forEach(element  => {
      $('#DrillAnalyticsRevenueVendor').append('<option value="'+element.id+'">'+element.name+'</option>')
    });
    renderDrillAnalyticsRevenue()
    }
  });
}

function renderDrillAnalyticsRevenue(){
  var labels =[];
  var data = [];
  var colors = [];
  var year = $('#DrillAnalyticsRevenueYear').val()
  var vendor = $('#DrillAnalyticsRevenueVendor').val()
  var paid = $('#DrillAnalyticsRevenueVendorpaid').is(":checked")
  // console.log(vendor);
  const randomBetween = (min, max) => min + Math.floor(Math.random() * (max - min + 1));
  $.ajax({url: 'dashboard-drillAnalyticsRevenue/'+year+'/'+vendor+'/'+paid, 
    success: function(result){
      result.forEach(element  => {
        var chatX = $('#chatX').val();
        if( chatX == 'REVENUE'){
          labels.push(element.date_ordered+' ('+element.revenue+') ')
          data.push(element.revenue)
        }else if(chatX == 'FULFULLEDORDERS'){
          labels.push(element.date_ordered+' ('+element.total_ordered_per_day+') ')
          data.push(element.total_ordered_per_day)
        }
        colors.push('rgb('+(randomBetween(0, 255))+','+(randomBetween(0, 255))+','+(randomBetween(0, 255))+')')
      });
      if(DrillAnalyticsRevenueVar){
        DrillAnalyticsRevenueVar.destroy();
      }
      DrillAnalyticsRevenueVar= new Chart(DrillAnalyticsRevenue, {
        type: 'bar',
        data: {
            labels: labels,
              datasets: [{
                axis: 'y',
                label: 'Date',
                data: data,
                fill: false,
                backgroundColor: colors,
                borderWidth: 1,
              }]
        },
        options: {
          indexAxis: 'x',
        }
      });
    }
  });  
}
renderDrillAnalyticsRevenue()
