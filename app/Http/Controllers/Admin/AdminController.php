<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Hash;
use Auth;
use App\Models\Admin;
use App\Models\Vendor;
use App\Models\VendorsBusinessDetail;
use App\Models\VendorsBankDetail;
use App\Models\Country;
use App\Models\Barangay;
use App\Models\Section;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Brand;
use App\Models\User;
use Illuminate\Support\Facades\DB;//K
use Illuminate\Validation\Rule;//K
use Carbon\Carbon;//K
use Image;
use Session;

use Maatwebsite\Excel\Exporter;
use App\Http\Controllers\Admin\Export\ExporterController;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;


class AdminController extends Controller
{
    public function vendordashboard(Request $request){
        $data = $request->session()->all();
        Session::put('page','vendordashboard');
        // Display total number of sections
        $sectionsCount = Section::count();
        // Display total number of products
        $productsCount = Product::where('vendor_id', $data['id'])->count();

        // Total pending orders per vendor
        $orders = DB::table('orders_products as op')
        ->select(
            'item_status',
            DB::raw('count(*) as item_status_count')
        )
        ->where('vendor_id','=',$data['id'])
        ->groupby('item_status')
        ->get()
        ->toArray();
        $ordersCount = 0;
        foreach ($orders as $key => $value) {
            if($value->item_status == 'Pending' ){
                $ordersCount += $value->item_status_count;
            }
        }

        // Total sales per vendor
        $sales = DB::table('orders_products as op')
        ->select(
            'item_status',
            DB::raw('count(*) as item_status_count')
        )
        ->where('vendor_id','=',$data['id'])
        ->groupby('item_status')
        ->get()
        ->toArray();
        $salesTotal = 0;
        foreach ($orders as $key => $value) {
            if($value->item_status == 'Shipped' ){
                $salesTotal += $value->item_status_count;
            }elseif($value->item_status == 'Delivered' ){
                $salesTotal += $value->item_status_count;

            }
        }

        // Display total number of coupons
        $couponsCount = Coupon::count();
        // Display total number of brands
        $brandsCount = Brand::count();
        // Display total number of users
        $usersCount = User::count();

        // Filter by year
        $years = DB::table('orders_products')
            ->select(   
                DB::raw('YEAR(created_at) as year'),
            )
            ->where('vendor_id','=',$data['id'])
            ->groupby(DB::raw('YEAR(created_at)'))
            ->orderBy(DB::raw('YEAR(created_at)'),'desc')
            ->get()
            ->toArray();
        return view('admin.vendordashboard')->with(compact('years','sectionsCount','productsCount','ordersCount','salesTotal','couponsCount','brandsCount','usersCount'));
    }

    // Calculate overall revenue
    public function adminoverallRevenue(){
        return(DB::table('orders_products')
            ->select(   
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('sum(product_qty*product_price) as total_per_month'),
            )
            ->groupby(DB::raw('YEAR(created_at), MONTH(created_at)'))
            ->get()
            ->toArray()

            );
        return 'adminoverallRevenue';
    }

    // Calculate overall revenue  / filter by year
    public function adminoverallRevenueYear($year,$paid){
        // Filter by Paid status
        if($paid == 'true'){
            return(DB::table('orders_products as op')
            ->select(   
                DB::raw('YEAR(op.created_at) as year'),
                DB::raw('MONTH(op.created_at) as month'),
                DB::raw('sum(op.product_qty*op.product_price) as total_per_month'),
            )
            ->join('orders as o','o.id','op.order_id')
            ->where( 'o.order_status','=','Paid')
            ->where( DB::raw('YEAR(op.created_at)'),'=',$year)
            ->groupby(DB::raw('YEAR(op.created_at), MONTH(op.created_at)'))
            ->get()
            ->toArray()
            );  
        }else{
            // Overall revenue with discount applied
            return(DB::table('orders_products as op')
            ->select(   
                DB::raw('YEAR(op.created_at) as year'),
                DB::raw('MONTH(op.created_at) as month'),
                DB::raw('sum(op.product_qty*op.product_price) as total_per_month'),
                DB::raw('sum((op.product_qty*op.product_price) - (op.product_price*(p.product_discount/100))) as total_per_month_with_dc '),
            )
            ->join('products as p','p.id','op.product_id')
            ->where( DB::raw('YEAR(op.created_at)'),'=',$year)
            ->groupby(DB::raw('YEAR(op.created_at), MONTH(op.created_at)'))
            ->get()
            ->toArray()
            );  
        }
        return 'adminoverallRevenue';
    }

    // Create downloadable excel, csv, pdf reports reports for overall revenue / filter by year / filter by paid status
    public function adminoverallRevenueYearDownload($type,$year,$paid){
        if($paid == 'true'){
            $data = DB::table('orders_products as op')
            ->select(   
                DB::raw('YEAR(op.created_at) as year'),
                DB::raw('MONTH(op.created_at) as month'),
                DB::raw('sum(op.product_qty*op.product_price) as total_per_month'),
            )
            ->join('orders as o','o.id','op.order_id')
            ->where( 'o.order_status','=','Paid')
            ->where( DB::raw('YEAR(op.created_at)'),'=',$year)
            ->groupby(DB::raw('YEAR(op.created_at), MONTH(op.created_at)'))
            ->get()
            ->toArray();
        }else{
            $data = DB::table('orders_products as op')
            ->select(   
                DB::raw('YEAR(op.created_at) as year'),
                DB::raw('MONTH(op.created_at) as month'),
                DB::raw('sum(op.product_qty*op.product_price) as total_per_month'),
                DB::raw('sum((op.product_qty*op.product_price) - (op.product_price*(p.product_discount/100))) as total_per_month_with_dc '),
            )
            ->join('products as p','p.id','op.product_id')
            ->where( DB::raw('YEAR(op.created_at)'),'=',$year)
            ->groupby(DB::raw('YEAR(op.created_at), MONTH(op.created_at)'))
            ->get()
            ->toArray();
        }
        $header = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        $content = [0,0,0,0,0,0,0,0,0,0,0,0];
        foreach ($data as $key => $value) {
            $content[ $value->month-1] = $value->total_per_month;
        }
        $file_name = 'Overall Revenue ['.$year.']';

        if($type == 'EXCEL'){
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        }elseif($type == 'CSV'){
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.csv', \Maatwebsite\Excel\Excel::CSV);
        }elseif($type == 'PDF'){
            $data = [
                'title'=>$file_name,
                'header'=>$header,
                'content'=> $content
            ];
            $pdf = Pdf::loadView('admin.exportpdf.exportchartpdf',  array( 
                'title'=> $file_name,
                'header'=>$header,
                'columns'=>$header,
                'content'=> $content)
            );
            return $pdf->setPaper('a4', 'landscape')->download( $file_name.'.pdf');
        }else{
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.csv', \Maatwebsite\Excel\Excel::CSV);
        }
    }

    // Display the top 10 selling products 
    public function admintopProducts(){
        return (DB::table('orders_products as op')
            ->select(
                'p.id',
                DB::raw('p.product_name,count(*) as total_product_sales')
            )
            ->join('products as p','p.id','op.product_id')
            ->join('orders as o','o.id','op.order_id')
            ->groupby('p.id')
            ->orderBy('total_product_sales','desc')
            ->limit(10)
            ->get()
            ->toArray()
            );
        return 'admintopProducts';
    }

    // Create downloadable excel, csv, pdf reports for top-selling products / filter by year / filter by paid status
    public function admintopProductsYearDownload($type,$year,$paid,$limit){
        if($paid == 'true'){
            $data = DB::table('orders_products as op')
            ->select(
                'p.id',
                'p.product_name',
                DB::raw('(count(*) * op.product_qty) as total_product_sales')
            )
            ->join('products as p','p.id','op.product_id')
            ->join('orders as o','o.id','op.order_id')
            ->where('o.order_status','=','Paid')
            ->where( DB::raw('YEAR(op.created_at)'),'=',$year)
            ->groupby('p.id')
            ->orderBy(DB::raw('(count(*) * op.product_qty)'),'desc')
            ->limit($limit)
            ->get()
            ->toArray();
        }else{
            $data = DB::table('orders_products as op')
                ->select(
                    'p.id',
                    'p.product_name',
                    DB::raw('sum(op.product_qty) as total_product_sales')
                )
                ->join('products as p','p.id','op.product_id')
                ->join('orders as o','o.id','op.order_id')
                ->where( DB::raw('YEAR(op.created_at)'),'=',$year)
                ->groupby('p.id')
                ->orderBy(DB::raw('sum(op.product_qty)'),'desc')
                ->limit($limit)
                ->get()
                ->toArray();
        }
        $header = [];
        $content = [];
        foreach ($data as $key => $value) {
            array_push($header,$value->product_name);
            array_push($content,$value->total_product_sales);
        }
        $file_name = 'Top Products ['.$year.']';

        if($type == 'EXCEL'){
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        }elseif($type == 'CSV'){
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.csv', \Maatwebsite\Excel\Excel::CSV);
        }elseif($type == 'PDF'){
            $data = [
                'title'=>$file_name,
                'header'=>$header,
                'content'=> $content
            ];
            $pdf = Pdf::loadView('admin.exportpdf.exportchartpdf',  array( 
                'title'=> $file_name,
                'header'=>$header,
                'columns'=>$header,
                'content'=> $content)
            );
            return $pdf->setPaper('a4', 'landscape')->download( $file_name.'.pdf');
        }else{
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.csv', \Maatwebsite\Excel\Excel::CSV);
        }
    }

    // Display top-selling products per year / filter by paid status
    // Input desired number of top-selling products to be displayed
    public function admintopProductsYear($year,$paid,$limit){
        if($paid == 'true'){
            return (DB::table('orders_products as op')
            ->select(
                'p.id',
                'p.product_name',
                DB::raw('(count(*) * op.product_qty) as total_product_sales')
            )
            ->join('products as p','p.id','op.product_id')
            ->join('orders as o','o.id','op.order_id')
            ->where('o.order_status','=','Paid')
            ->where( DB::raw('YEAR(op.created_at)'),'=',$year)
            ->groupby('p.id')
            ->orderBy(DB::raw('(count(*) * op.product_qty)'),'desc')
            ->limit($limit)
            ->get()
            ->toArray()
            );
        }else{
            return (DB::table('orders_products as op')
                ->select(
                    'p.id',
                    'p.product_name',
                    DB::raw('sum(op.product_qty) as total_product_sales')
                )
                ->join('products as p','p.id','op.product_id')
                ->join('orders as o','o.id','op.order_id')
                ->where( DB::raw('YEAR(op.created_at)'),'=',$year)
                ->groupby('p.id')
                ->orderBy(DB::raw('sum(op.product_qty)'),'desc')
                ->limit($limit)
                ->get()
                ->toArray()
            );
        }
    }

    // Calculate overall revenue / filter by vendor / filter by year / filter by paid status
    public function drillAnalyticsRevenueYear($year,$vendor,$paid){
        // Total revenue for all the vendors
        if($vendor == 'All'){
            if($paid == 'true'){
                return DB::table('orders_products as op')
                ->select(
                    DB::raw('DATE_FORMAT(op.created_at, "%M %d, %Y") as date_ordered ') ,
                    DB::raw('count(*) total_ordered_per_day '),
                    DB::raw('sum(product_qty*product_price) as revenue')
                )
                ->join('orders as o','o.id','op.order_id')
                ->where( DB::raw('YEAR(op.created_at)'),'=',$year)
                ->where('o.order_status','=','Paid')
                ->groupby(DB::raw('DATE_FORMAT(op.created_at, "%Y%m%d")'))
                ->orderBy(DB::raw('DATE_FORMAT(op.created_at, "%Y%m%d")'),'ASC')
                ->get()
                ->toArray()
                ;
            }else{
                return (DB::table('orders_products as op')
                ->select(
                    DB::raw('DATE_FORMAT(op.created_at, "%M %d, %Y") as date_ordered ') ,
                    DB::raw('count(*) total_ordered_per_day '),
                    DB::raw('sum(product_qty*product_price) as revenue')
                )    
                ->join('orders as o','o.id','op.order_id')
                ->where( DB::raw('YEAR(op.created_at)'),'=',$year)
                ->groupby(DB::raw('DATE_FORMAT(op.created_at, "%Y%m%d")'))
                ->orderBy(DB::raw('DATE_FORMAT(op.created_at, "%Y%m%d")'),'ASC')
                ->get()
                ->toArray()
                );
            }
        // Total revenue per vendor
        }else{
            if($paid == 'true'){
                return DB::table('orders_products as op')
                ->select(
                    DB::raw('DATE_FORMAT(op.created_at, "%M %d, %Y") as date_ordered ') ,
                    DB::raw('count(*) total_ordered_per_day '),
                    DB::raw('sum(product_qty*product_price) as revenue')
                )
                ->join('orders as o','o.id','op.order_id')
                ->where( DB::raw('YEAR(op.created_at)'),'=',$year)
                ->where('o.order_status','=','Paid')
                ->where('op.vendor_id','=',$vendor)
                ->groupby(DB::raw('DATE_FORMAT(op.created_at, "%Y%m%d")'))
                ->orderBy(DB::raw('DATE_FORMAT(op.created_at, "%Y%m%d")'),'ASC')
                ->get()
                ->toArray()
                ;
            }else{
                return (DB::table('orders_products as op')
                ->select(
                    DB::raw('DATE_FORMAT(op.created_at, "%M %d, %Y") as date_ordered ') ,
                    DB::raw('count(*) total_ordered_per_day '),
                    DB::raw('sum(product_qty*product_price) as revenue')
                )
                ->join('orders as o','o.id','op.order_id')
                ->where( DB::raw('YEAR(op.created_at)'),'=',$year)
                ->where('op.vendor_id','=',$vendor)
                ->groupby(DB::raw('DATE_FORMAT(op.created_at, "%Y%m%d")'))
                ->orderBy(DB::raw('DATE_FORMAT(op.created_at, "%Y%m%d")'),'ASC')
                ->get()
                ->toArray()
                );
            }
        }
        return 'sadfsda';
    }

    // Create downloadable excel, csv, pdf reports reports for overall revenue / filter by vendor / filter by year / filter by paid status
    public function drillAnalyticsRevenueYearDownload($type,$year,$vendor,$paid,$chartX){
        if($vendor == 'All'){
            if($paid == 'true'){
                $data =  DB::table('orders_products as op')
                ->select(
                    DB::raw('DATE_FORMAT(op.created_at, "%M %d, %Y") as date_ordered ') ,
                    DB::raw('count(*) total_ordered_per_day '),
                    DB::raw('sum(product_qty*product_price) as revenue')
                )
                ->join('orders as o','o.id','op.order_id')
                ->where( DB::raw('YEAR(op.created_at)'),'=',$year)
                ->where('o.order_status','=','Paid')
                ->groupby(DB::raw('DATE_FORMAT(op.created_at, "%Y%m%d")'))
                ->orderBy(DB::raw('DATE_FORMAT(op.created_at, "%Y%m%d")'),'ASC')
                ->get()
                ->toArray()
                ;
            }else{
                $data =  (DB::table('orders_products as op')
                ->select(
                    DB::raw('DATE_FORMAT(op.created_at, "%M %d, %Y") as date_ordered ') ,
                    DB::raw('count(*) total_ordered_per_day '),
                    DB::raw('sum(product_qty*product_price) as revenue')
                )    
                ->join('orders as o','o.id','op.order_id')
                ->where( DB::raw('YEAR(op.created_at)'),'=',$year)
                ->groupby(DB::raw('DATE_FORMAT(op.created_at, "%Y%m%d")'))
                ->orderBy(DB::raw('DATE_FORMAT(op.created_at, "%Y%m%d")'),'ASC')
                ->get()
                ->toArray()
                );
            }
        }else{
            if($paid == 'true'){
                $data = DB::table('orders_products as op')
                ->select(
                    DB::raw('DATE_FORMAT(op.created_at, "%M %d, %Y") as date_ordered ') ,
                    DB::raw('count(*) total_ordered_per_day '),
                    DB::raw('sum(product_qty*product_price) as revenue')
                )
                ->join('orders as o','o.id','op.order_id')
                ->where( DB::raw('YEAR(op.created_at)'),'=',$year)
                ->where('o.order_status','=','Paid')
                ->where('op.vendor_id','=',$vendor)
                ->groupby(DB::raw('DATE_FORMAT(op.created_at, "%Y%m%d")'))
                ->orderBy(DB::raw('DATE_FORMAT(op.created_at, "%Y%m%d")'),'ASC')
                ->get()
                ->toArray()
                ;
            }else{
                $data = (DB::table('orders_products as op')
                ->select(
                    DB::raw('DATE_FORMAT(op.created_at, "%M %d, %Y") as date_ordered ') ,
                    DB::raw('count(*) total_ordered_per_day '),
                    DB::raw('sum(product_qty*product_price) as revenue')
                )
                ->join('orders as o','o.id','op.order_id')
                ->where( DB::raw('YEAR(op.created_at)'),'=',$year)
                ->where('op.vendor_id','=',$vendor)
                ->groupby(DB::raw('DATE_FORMAT(op.created_at, "%Y%m%d")'))
                ->orderBy(DB::raw('DATE_FORMAT(op.created_at, "%Y%m%d")'),'ASC')
                ->get()
                ->toArray()
                );
            }
        }

        if($chartX =='REVENUE'){
            $header = ['Date','Revenue'];
        }elseif($chartX =='FULFULLEDORDERS'){
            $header = ['Date','Fulfilled Orders'];
        }
       
        $content = [];
        foreach ($data as $key => $value) {
            $item = [];
            array_push($item,$value->date_ordered);
            if($chartX =='REVENUE'){
                array_push($item,$value->revenue);
            }elseif($chartX =='FULFULLEDORDERS'){
                array_push($item,$value->total_ordered_per_day);
            }
            array_push($content,$item);
        }
        $file_name = 'Drill Analytics ['.$year.']';

        if($type == 'EXCEL'){
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        }elseif($type == 'CSV'){
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.csv', \Maatwebsite\Excel\Excel::CSV);
        }elseif($type == 'PDF'){
            $data = [
                'title'=>$file_name,
                'header'=>$header,
                'content'=> $content
            ];
            $pdf = Pdf::loadView('admin.exportpdf.exportpdf',  array( 
                'title'=> $file_name,
                'header'=>$header,
                'columns'=>$header,
                'content'=> $content)
            );
            return $pdf->setPaper('a4', 'landscape')->download( $file_name.'.pdf');
        }else{
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.csv', \Maatwebsite\Excel\Excel::CSV);
        }
   
    }

    // Display vendor details
    public function getVendorDetails($year){
        return  DB::table('orders_products as op')
        ->select(   
            'v.name',
            'v.id'
        )
        ->join('vendors as v','v.id','op.vendor_id')
        ->join('orders as o','o.id','op.order_id')
        ->groupby('vendor_id')
        ->where(DB::raw('YEAR(op.created_at)'),$year)
        ->get()
        ->toArray();   ;
    }

    // Display top 10 selling sellers
    public function admintopSellers(){
        return (DB::table('orders_products as op')
            ->select(
                'vendor_id',
                'v.name',
                'v.email',
                DB::raw('count(*) as total_orders')
            )
            ->join('vendors as v','v.id','op.vendor_id')
            ->join('orders as o','o.id','op.order_id')
            ->groupby('vendor_id')
            ->orderBy(DB::raw('count(*)'),'desc')
            ->limit(10)
            ->get()
            ->toArray()
            );
        return 'admintopSellers';
    }

    // Display top 10 selling sellers / filter by year / filter paid
    public function admintopSellersYear($year,$paid){
        if($paid == 'true'){
            return (DB::table('orders_products as op')
                ->select(
                    'vendor_id',
                    'v.name',
                    'v.email',
                    DB::raw('count(*) as total_orders'),
                    DB::raw('sum(product_qty*product_price) as total_revenue')
                )
                ->join('vendors as v','v.id','op.vendor_id')
                ->join('orders as o','o.id','op.order_id')
                ->where('o.order_status','=','Paid')
                ->where( DB::raw('YEAR(op.created_at)'),'=',$year)
                ->groupby('vendor_id')
                ->orderBy(DB::raw('sum(product_qty*product_price)'),'desc')
                ->limit(10)
                ->get()
                ->toArray()
                );
        }else{
            return (DB::table('orders_products as op')
                ->select(
                    'vendor_id',
                    'v.name',
                    'v.email',
                    DB::raw('count(*) as total_orders'),
                    DB::raw('sum(product_qty*product_price) as total_revenue')
                )
                ->join('vendors as v','v.id','op.vendor_id')
                ->join('orders as o','o.id','op.order_id')
                ->where( DB::raw('YEAR(op.created_at)'),'=',$year)
                ->groupby('vendor_id')
                ->orderBy(DB::raw('sum(product_qty*product_price)'),'desc')
                ->limit(10)
                ->get()
                ->toArray()
                );
        }
        return 'admintopSellers';
    }

    // Create downloadable excel, csv, pdf reports reports for top 10 selling sellers / filter by year / filter paid
    public function admintopSellersYearDownload($type,$year,$paid){
        if($paid == 'true'){
            $data = (DB::table('orders_products as op')
                ->select(
                    'vendor_id',
                    'v.name',
                    'v.email',
                    DB::raw('count(*) as total_orders'),
                    DB::raw('sum(product_qty*product_price) as total_revenue')
                )
                ->join('vendors as v','v.id','op.vendor_id')
                ->join('orders as o','o.id','op.order_id')
                ->where('o.order_status','=','Paid')
                ->where( DB::raw('YEAR(op.created_at)'),'=',$year)
                ->groupby('vendor_id')
                ->orderBy(DB::raw('sum(product_qty*product_price)'),'desc')
                ->limit(10)
                ->get()
                ->toArray()
                );
        }else{
            $data = (DB::table('orders_products as op')
                ->select(
                    'vendor_id',
                    'v.name',
                    'v.email',
                    DB::raw('count(*) as total_orders'),
                    DB::raw('sum(product_qty*product_price) as total_revenue')
                )
                ->join('vendors as v','v.id','op.vendor_id')
                ->join('orders as o','o.id','op.order_id')
                ->where( DB::raw('YEAR(op.created_at)'),'=',$year)
                ->groupby('vendor_id')
                ->orderBy(DB::raw('sum(product_qty*product_price)'),'desc')
                ->limit(10)
                ->get()
                ->toArray()
                );
        }
        $header = [];
        $content = [];
        foreach ($data as $key => $value) {
            array_push($header,$value->name.' ('.$value->email.')');
            array_push($content,$value->total_revenue);
        }
        $file_name = 'Top Sellers ['.$year.']';

        if($type == 'EXCEL'){
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        }elseif($type == 'CSV'){
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.csv', \Maatwebsite\Excel\Excel::CSV);
        }elseif($type == 'PDF'){
            $data = [
                'title'=>$file_name,
                'header'=>$header,
                'content'=> $content
            ];
            $pdf = Pdf::loadView('admin.exportpdf.exportchartpdf',  array( 
                'title'=> $file_name,
                'header'=>$header,
                'columns'=>$header,
                'content'=> $content)
            );
            return $pdf->setPaper('a4', 'landscape')->download( $file_name.'.pdf');
        }else{
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.csv', \Maatwebsite\Excel\Excel::CSV);
        }
    }

    // Display top categories
    public function admintopCategory(){
        return (DB::table('orders_products as op')
        ->select(
            'c.id',
            'c.category_name',
            DB::raw('count(*) as total_category_count') ,
            DB::raw('sum(product_qty) as total_product_qty')
        )
        ->join('products as p','p.id','op.product_id')
        ->join('categories as c','c.id','p.category_id')
        ->join('orders as o','o.id','op.order_id')
        ->groupby('c.id')
        ->get()
        ->toArray()
        );
        
        return 'admintopCategory';
    }

    // Display top categories / filter by year / filter by paid status
    public function admintopCategoryYear($year,$paid){
        if($paid == 'true'){
            return (DB::table('orders_products as op')
            ->select(
                'c.id',
                'c.category_name',
                DB::raw('count(*) as total_category_count') ,
                DB::raw('sum(product_qty) as total_product_qty')
            )
            ->join('products as p','p.id','op.product_id')
            ->join('categories as c','c.id','p.category_id')
            ->join('orders as o','o.id','op.order_id')
            ->where('o.order_status','=','Paid')
            ->where( DB::raw('YEAR(op.created_at)'),'=',$year)
            ->orderBy(DB::raw('sum(product_qty)'),'desc')
            ->groupby('c.id')
            ->get()
            ->toArray()
            );
        }else{
            return (DB::table('orders_products as op')
                ->select(
                    'c.id',
                    'c.category_name',
                    DB::raw('count(*) as total_category_count') ,
                    DB::raw('sum(product_qty) as total_product_qty')
                )
                ->join('products as p','p.id','op.product_id')
                ->join('categories as c','c.id','p.category_id')
                ->join('orders as o','o.id','op.order_id')
                ->where( DB::raw('YEAR(op.created_at)'),'=',$year)
                ->groupby('c.id')
                ->orderBy(DB::raw('sum(product_qty)'),'desc')
                ->get()
                ->toArray()
                );
        }
        return 'admintopCategory';
    }

    // Create downloadable excel, csv, pdf reports reports for top categories / filter by year / filter by paid status
    public function admintopCategoryYearDownload($type,$year,$paid){
        if($paid == 'true'){
            $data = (DB::table('orders_products as op')
            ->select(
                'c.id',
                'c.category_name',
                DB::raw('count(*) as total_category_count') ,
                DB::raw('sum(product_qty) as total_product_qty')
            )
            ->join('products as p','p.id','op.product_id')
            ->join('categories as c','c.id','p.category_id')
            ->join('orders as o','o.id','op.order_id')
            ->where('o.order_status','=','Paid')
            ->where( DB::raw('YEAR(op.created_at)'),'=',$year)
            ->orderBy(DB::raw('sum(product_qty)'),'desc')
            ->groupby('c.id')
            ->get()
            ->toArray()
            );
        }else{
            $data = (DB::table('orders_products as op')
                ->select(
                    'c.id',
                    'c.category_name',
                    DB::raw('count(*) as total_category_count') ,
                    DB::raw('sum(product_qty) as total_product_qty')
                )
                ->join('products as p','p.id','op.product_id')
                ->join('categories as c','c.id','p.category_id')
                ->join('orders as o','o.id','op.order_id')
                ->where( DB::raw('YEAR(op.created_at)'),'=',$year)
                ->groupby('c.id')
                ->orderBy(DB::raw('sum(product_qty)'),'desc')
                ->get()
                ->toArray()
                );
        }
        $header = [];
        $content = [];
        foreach ($data as $key => $value) {
            array_push($header,$value->category_name);
            array_push($content,$value->total_product_qty);
        }
        $file_name = 'Top Category ['.$year.']';

        if($type == 'EXCEL'){
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        }elseif($type == 'CSV'){
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.csv', \Maatwebsite\Excel\Excel::CSV);
        }elseif($type == 'PDF'){
            $data = [
                'title'=>$file_name,
                'header'=>$header,
                'content'=> $content
            ];
            $pdf = Pdf::loadView('admin.exportpdf.exportchartpdf',  array( 
                'title'=> $file_name,
                'header'=>$header,
                'columns'=>$header,
                'content'=> $content)
            );
            return $pdf->setPaper('a4', 'landscape')->download( $file_name.'.pdf');
        }else{
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.csv', \Maatwebsite\Excel\Excel::CSV);
        }
    }
    public function adminfulfilledOrders(){
        return 'adminfulfilledOrders';
    }

    // Display total fulfilled orders / filter by year / filter by paid status
    public function adminfulfilledOrdersYear($year,$paid){
        if($paid == 'true'){
            return (DB::table('orders_products as op')
            ->select(
                DB::raw('DATE_FORMAT(op.created_at, "%M %d, %Y") as date_ordered ') ,
                DB::raw('count(*) total_ordered_per_day ')
            )
            ->where( DB::raw('YEAR(op.created_at)'),'=',$year)
            ->join('orders as o','o.id','op.order_id')
            ->where('o.order_status','=','Paid')
            ->groupby(DB::raw('DATE_FORMAT(op.created_at, "%Y%m%d")'))
            ->orderBy(DB::raw('DATE_FORMAT(op.created_at, "%Y%m%d")'),'ASC')
            ->get()
            ->toArray()
            );
        }else{
            return (DB::table('orders_products as op')
            ->select(
                DB::raw('DATE_FORMAT(op.created_at, "%M %d, %Y") as date_ordered ') ,
                DB::raw('count(*) total_ordered_per_day ')
            )
            ->join('orders as o','o.id','op.order_id')
            ->where( DB::raw('YEAR(op.created_at)'),'=',$year)
            ->groupby(DB::raw('DATE_FORMAT(op.created_at, "%Y%m%d")'))
            ->orderBy(DB::raw('DATE_FORMAT(op.created_at, "%Y%m%d")'),'ASC')
            ->get()
            ->toArray()
            );
        }
        return 'adminfulfilledOrdesdffrs';
    }

    // Create downloadable excel, csv, pdf reports for fulfilled orders / filter by year / filter by paid status
    public function adminfulfilledOrdersYearDownload($type,$year,$paid){
        if($paid == 'true'){
            $data = (DB::table('orders_products as op')
            ->select(
                DB::raw('DATE_FORMAT(op.created_at, "%M %d, %Y") as date_ordered ') ,
                DB::raw('count(*) total_ordered_per_day ')
            )
            ->where( DB::raw('YEAR(op.created_at)'),'=',$year)
            ->join('orders as o','o.id','op.order_id')
            ->where('o.order_status','=','Paid')
            ->groupby(DB::raw('DATE_FORMAT(op.created_at, "%Y%m%d")'))
            ->orderBy(DB::raw('DATE_FORMAT(op.created_at, "%Y%m%d")'),'ASC')
            ->get()
            ->toArray()
            );
        }else{
            $data = (DB::table('orders_products as op')
            ->select(
                DB::raw('DATE_FORMAT(op.created_at, "%M %d, %Y") as date_ordered ') ,
                DB::raw('count(*) total_ordered_per_day ')
            )
            ->join('orders as o','o.id','op.order_id')
            ->where( DB::raw('YEAR(op.created_at)'),'=',$year)
            ->groupby(DB::raw('DATE_FORMAT(op.created_at, "%Y%m%d")'))
            ->orderBy(DB::raw('DATE_FORMAT(op.created_at, "%Y%m%d")'),'ASC')
            ->get()
            ->toArray()
            );
        }
        $header = ['Date','Fulfilled Orders'];
        $content = [];
        foreach ($data as $key => $value) {
            $item = [];
            array_push($item ,$value->date_ordered);
            array_push($item ,$value->total_ordered_per_day);
            array_push($content , $item);
        }
        $file_name = 'Fulfilled Orders ['.$year.']';

        if($type == 'EXCEL'){
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        }elseif($type == 'CSV'){
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.csv', \Maatwebsite\Excel\Excel::CSV);
        }elseif($type == 'PDF'){
            $data = [
                'title'=>$file_name,
                'header'=>$header,
                'content'=> $content
            ];
            $pdf = Pdf::loadView('admin.exportpdf.exportpdf',  array( 
                'title'=> $file_name,
                'header'=>$header,
                'columns'=>$header,
                'content'=> $content)
            );
            return $pdf->setPaper('a4', 'landscape')->download( $file_name.'.pdf');
        }else{
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.csv', \Maatwebsite\Excel\Excel::CSV);
        }
    }

    // Display different order statuses
    public function adminorderStatus(){
        return (DB::table('orders_products as op')
        ->select(
            'item_status',
            DB::raw('count(*) as item_status_count')
        )
        ->join('vendors as v','v.id','op.vendor_id')
        ->groupby('item_status')
        ->get()
        ->toArray()
        );
        return 'adminorderStatus';
    }

    // Diplay different order statuses / filter by year
    public function adminorderStatusYear($year){
        return (DB::table('orders_products as op')
        ->select(
            'item_status',
            DB::raw('count(*) as item_status_count')
        )
        ->join('vendors as v','v.id','op.vendor_id')
        ->where( DB::raw('YEAR(op.created_at)'),'=',$year)
        ->groupby('item_status')
        ->orderBy('item_status_count','desc')
        ->get()
        ->toArray()
        );
        return 'adminorderStatus';
    }

    // Create downloadable excel, csv, pdf reports for different order statuses / filter by year
    public function adminorderStatusYearDownload($type,$year){
        $data = (DB::table('orders_products as op')
        ->select(
            'item_status',
            DB::raw('count(*) as item_status_count')
        )
        ->join('vendors as v','v.id','op.vendor_id')
        ->where( DB::raw('YEAR(op.created_at)'),'=',$year)
        ->groupby('item_status')
        ->orderBy('item_status_count','desc')
        ->get()
        ->toArray()
        );
        $header = [];
        $content = [];
        foreach ($data as $key => $value) {
            if($value->item_status){
                array_push($header,$value->item_status);
            }else{
                array_push($header,'NULL');
            }
            array_push($content,$value->item_status_count);
        }
        $file_name = 'Order Status ['.$year.']';

        if($type == 'EXCEL'){
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        }elseif($type == 'CSV'){
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.csv', \Maatwebsite\Excel\Excel::CSV);
        }elseif($type == 'PDF'){
            $data = [
                'title'=>$file_name,
                'header'=>$header,
                'content'=> $content
            ];
            $pdf = Pdf::loadView('admin.exportpdf.exportchartpdf',  array( 
                'title'=> $file_name,
                'header'=>$header,
                'columns'=>$header,
                'content'=> $content)
            );
            return $pdf->setPaper('a4', 'landscape')->download( $file_name.'.pdf');
        }else{
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.csv', \Maatwebsite\Excel\Excel::CSV);
        }
    }
    
    /* Vendor Side */

    // Calculate vendor total sales
    public function vendorsales(Request $request){
        $data = $request->session()->all();
        return (DB::table('orders_products as op')
        ->select(
            DB::raw('YEAR(created_at)'),
            DB::raw('MONTH(created_at)'),
            DB::raw('MONTH(created_at) as month_num'),
            DB::raw('sum(product_qty*product_price) as total_per_month') 
        )
        ->where('vendor_id','=',$data['id'])
        ->groupby(DB::raw('YEAR(created_at), MONTH(created_at)'))
        ->get()
        ->toArray()
        );
          return 'vendorsales';
    }

    // Calculate vendor average overder value
    public function vendoraverageOrderValue(Request $request){
    $data = $request->session()->all();
    return (DB::table('orders_products as op')
    ->select(
        DB::raw('YEAR(created_at) as YEAR'),
        DB::raw('MONTH(created_at) as MONTH'),
        DB::raw('MONTH(created_at) as month_num'),
        DB::raw('sum(product_qty*product_price) as total_per_month_value'),
        DB::raw('count(*) as total_order_per_month'),
        DB::raw('sum(product_qty*product_price)/count(*) as average_order_value_per_month')
    )
    ->where('vendor_id','=',$data['id'])
    ->groupby(DB::raw('YEAR(created_at), MONTH(created_at)'))
    ->get()
    ->toArray()
    );
        return 'vendoraverageOrderValue';
    }

    // Calculate vendor top-selling products
    public function vendortopSellingProducts(Request $request){
        $data = $request->session()->all();
        return (DB::table('orders_products as op')
        ->select(
            'product_id',
            'product_name',
            DB::raw('sum(product_qty) as total_quantity_orders'),
            DB::raw('count(*) as total_orders')
        )
        ->where('vendor_id','=',$data['id'])
        ->groupby('product_id')
        ->orderBy('total_quantity_orders','desc')
        ->get()
        ->toArray()
        );
          return 'vendortopSellingProducts';
    }

    // Calculate vendor different order statuses
    public function vendororderStatus(Request $request){
    $data = $request->session()->all();
    return (DB::table('orders_products as op')
    ->select(
        'item_status',
        DB::raw('count(*) as item_status_count')
    )
    ->join('vendors as v','v.id','op.vendor_id')
    ->where('vendor_id','=',$data['id'])
    ->groupby('item_status')
    ->get()
    ->toArray()
    );
        return 'vendororderStatus';
    }

    // Calculate vendor sales growth over time
    public function vendorsalesGrowthOverTime(Request $request){
    $data = $request->session()->all();

    return (DB::table('orders_products as op')
    ->select(
        DB::raw('YEAR(created_at)'),
        DB::raw('MONTH(created_at)'),
        DB::raw('sum(product_qty*product_price) as total_per_month')
    )
    ->where('vendor_id','=',$data['id'])
    ->groupby(DB::raw('YEAR(created_at), MONTH(created_at)'))
    ->get()
    ->toArray()
    );
        return 'vendorsalesGrowthOverTime';
    }

    // Calculate total vendor sales / filter by year / filter by paid status
    public function vendorsalesYear(Request $request,$year,$paid){
        $data = $request->session()->all();
        if($paid == 'true'){
            return (DB::table('orders_products as op')
            ->select(
                DB::raw('YEAR(op.created_at) as year'),
                DB::raw('MONTHNAME(op.created_at) as month'),
                DB::raw('MONTH(op.created_at) as month_num'),
                DB::raw('sum(op.product_qty*op.product_price) as total_per_month') 
            )
            ->join('orders as o','o.id','op.order_id')
            ->where('o.order_status','=','Paid')
            ->where('vendor_id','=',$data['id'])
            ->where(DB::raw('YEAR(op.created_at)'),'=',$year)
            ->groupby(DB::raw('YEAR(op.created_at), MONTH(op.created_at)'))
            ->get()
            ->toArray()
            );
        }else{
            return (DB::table('orders_products as op')
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTHNAME(created_at) as month'),
                DB::raw('MONTH(created_at) as month_num'),
                DB::raw('sum(product_qty*product_price) as total_per_month') 
            )
            ->where('vendor_id','=',$data['id'])
            ->where(DB::raw('YEAR(created_at)'),'=',$year)
            ->groupby(DB::raw('YEAR(created_at), MONTH(created_at)'))
            ->get()
            ->toArray()
            );
        }
        
        return 'vendorsales';
    }

    // Create downloadable excel, csv, pdf reports reports for overall sales / filter by year / filter by paid status
    public function vendorsalesYearDownload(Request $request,$type,$year,$paid){
        $data = $request->session()->all();
        if($paid == 'true'){
            $data = (DB::table('orders_products as op')
            ->select(
                DB::raw('YEAR(op.created_at) as year'),
                DB::raw('MONTHNAME(op.created_at) as month_name'),
                DB::raw('MONTH(op.created_at) as month'),
                DB::raw('sum(op.product_qty*op.product_price) as total_per_month') 
            )
            ->join('orders as o','o.id','op.order_id')
            ->where('o.order_status','=','Paid')
            ->where('vendor_id','=',$data['id'])
            ->where(DB::raw('YEAR(op.created_at)'),'=',$year)
            ->groupby(DB::raw('YEAR(op.created_at), MONTH(op.created_at)'))
            ->get()
            ->toArray()
            );
        }else{
            $data = (DB::table('orders_products as op')
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTHNAME(created_at) as month_name'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('sum(product_qty*product_price) as total_per_month') 
            )
            ->where('vendor_id','=',$data['id'])
            ->where(DB::raw('YEAR(created_at)'),'=',$year)
            ->groupby(DB::raw('YEAR(created_at), MONTH(created_at)'))
            ->get()
            ->toArray()
            );
        }

      
       
        $header = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        $content = [0,0,0,0,0,0,0,0,0,0,0,0];
        foreach ($data as $key => $value) {
            $content[ $value->month-1] = $value->total_per_month;
        }
        $file_name = 'Sales ['.$year.']';

        if($type == 'EXCEL'){
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        }elseif($type == 'CSV'){
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.csv', \Maatwebsite\Excel\Excel::CSV);
        }elseif($type == 'PDF'){
            $data = [
                'title'=>$file_name,
                'header'=>$header,
                'content'=> $content
            ];
            $pdf = Pdf::loadView('admin.exportpdf.exportchartpdf',  array( 
                'title'=> $file_name,
                'header'=>$header,
                'columns'=>$header,
                'content'=> $content)
            );
            return $pdf->setPaper('a4', 'landscape')->download( $file_name.'.pdf');
        }else{
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.csv', \Maatwebsite\Excel\Excel::CSV);
        }
    }
    
    // Display average overall value / filter by year / filter as paid status
    public function vendoraverageOrderValueYear(Request $request,$year,$paid){
        $data = $request->session()->all();
        if($paid == 'true'){
            return (DB::table('orders_products as op')
            ->select(
                DB::raw('YEAR(op.created_at) as year'),
                DB::raw('MONTHNAME(op.created_at) as month'),
                DB::raw('MONTH(op.created_at) as month_num'),
                DB::raw('sum(op.product_qty*op.product_price) as total_per_month_value'),
                DB::raw('count(*) as total_order_per_month'),
                DB::raw('sum(op.product_qty*op.product_price)/count(*) as average_order_value_per_month')
                )
                ->join('orders as o','o.id','op.order_id')
                ->where('o.order_status','=','Paid')
                ->where('op.vendor_id','=',$data['id'])
                ->where(DB::raw('YEAR(op.created_at)'),'=',$year)
                ->groupby(DB::raw('YEAR(op.created_at), MONTH(op.created_at)'))
                ->get()
                ->toArray()
            );
        }else{

            return (DB::table('orders_products as op')
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTHNAME(created_at) as month'),
                DB::raw('MONTH(created_at) as month_num'),
                DB::raw('sum(product_qty*product_price) as total_per_month_value'),
                DB::raw('count(*) as total_order_per_month'),
                DB::raw('sum(product_qty*product_price)/count(*) as average_order_value_per_month')
                )
                ->where('vendor_id','=',$data['id'])
                ->where(DB::raw('YEAR(created_at)'),'=',$year)
                ->groupby(DB::raw('YEAR(created_at), MONTH(created_at)'))
                ->get()
                ->toArray()
            );
        }
        return 'vendoraverageOrderValue';
    }

    // Create downloadable excel, csv, pdf reports reports for average odder value / filter by year / filter by paid status
    public function vendoraverageOrderValueYearDownload(Request $request,$type,$year,$paid){
        $data = $request->session()->all();
        if($paid == 'true'){
            $data = (DB::table('orders_products as op')
            ->select(
                DB::raw('YEAR(op.created_at) as year'),
                DB::raw('MONTHNAME(op.created_at) as month_name'),
                DB::raw('MONTH(op.created_at) as month'),
                DB::raw('sum(op.product_qty*op.product_price) as total_per_month_value'),
                DB::raw('count(*) as total_order_per_month'),
                DB::raw('sum(op.product_qty*op.product_price)/count(*) as average_order_value_per_month')
                )
                ->join('orders as o','o.id','op.order_id')
                ->where('o.order_status','=','Paid')
                ->where('op.vendor_id','=',$data['id'])
                ->where(DB::raw('YEAR(op.created_at)'),'=',$year)
                ->groupby(DB::raw('YEAR(op.created_at), MONTH(op.created_at)'))
                ->get()
                ->toArray()
            );
        }else{

            $data = (DB::table('orders_products as op')
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTHNAME(created_at) as month_name'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('sum(product_qty*product_price) as total_per_month_value'),
                DB::raw('count(*) as total_order_per_month'),
                DB::raw('sum(product_qty*product_price)/count(*) as average_order_value_per_month')
                )
                ->where('vendor_id','=',$data['id'])
                ->where(DB::raw('YEAR(created_at)'),'=',$year)
                ->groupby(DB::raw('YEAR(created_at), MONTH(created_at)'))
                ->get()
                ->toArray()
            );
        }
        $header = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        $content = [0,0,0,0,0,0,0,0,0,0,0,0];
        foreach ($data as $key => $value) {
            $content[ $value->month-1] = $value->average_order_value_per_month;
        }
        $file_name = 'Average Order Value ['.$year.']';

        if($type == 'EXCEL'){
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        }elseif($type == 'CSV'){
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.csv', \Maatwebsite\Excel\Excel::CSV);
        }elseif($type == 'PDF'){
            $data = [
                'title'=>$file_name,
                'header'=>$header,
                'content'=> $content
            ];
            $pdf = Pdf::loadView('admin.exportpdf.exportchartpdf',  array( 
                'title'=> $file_name,
                'header'=>$header,
                'columns'=>$header,
                'content'=> $content)
            );
            return $pdf->setPaper('a4', 'landscape')->download( $file_name.'.pdf');
        }else{
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.csv', \Maatwebsite\Excel\Excel::CSV);
        }
    }

    // Display top-selling products / filter by year / filter as paid status
    public function vendortopSellingProductsYear(Request $request,$year,$paid){
        $data = $request->session()->all();
        if($paid == 'true'){
            return (DB::table('orders_products as op')
            ->select(
                'product_id',
                'product_name',
                DB::raw('sum(product_qty) as total_quantity_orders'),
                DB::raw('count(*) as total_orders')
            )
            ->join('orders as o','o.id','op.order_id')
            ->where('o.order_status','=','Paid')
            ->where('vendor_id','=',$data['id'])
            ->where(DB::raw('YEAR(op.created_at)'),'=',$year)
            ->groupby('product_name')
            ->orderBy('total_quantity_orders','desc')
            ->get()
            ->toArray()
            );
        }else{
            return (DB::table('orders_products as op')
            ->select(
                'product_id',
                'product_name',
                DB::raw('sum(product_qty) as total_quantity_orders'),
                DB::raw('count(*) as total_orders')
            )
            ->where('vendor_id','=',$data['id'])
            ->where(DB::raw('YEAR(created_at)'),'=',$year)
            ->groupby('product_name')
            ->orderBy('total_quantity_orders','desc')
            ->get()
            ->toArray()
            );
        }
        return 'vendortopSellingProducts';
    }

    // Create downloadable excel, csv, pdf reports reports for top-selling products / filter by year / filter by paid status
    public function vendortopSellingProductsYearDownload(Request $request,$type,$year,$paid){
        $data = $request->session()->all();
        if($paid == 'true'){
            $data = (DB::table('orders_products as op')
            ->select(
                'product_id',
                'product_name',
                DB::raw('sum(product_qty) as total_quantity_orders'),
                DB::raw('count(*) as total_orders')
            )
            ->join('orders as o','o.id','op.order_id')
            ->where('o.order_status','=','Paid')
            ->where('vendor_id','=',$data['id'])
            ->where(DB::raw('YEAR(op.created_at)'),'=',$year)
            ->groupby('product_name')
            ->orderBy('total_quantity_orders','desc')
            ->get()
            ->toArray()
            );
        }else{
            $data = (DB::table('orders_products as op')
            ->select(
                'product_id',
                'product_name',
                DB::raw('sum(product_qty) as total_quantity_orders'),
                DB::raw('count(*) as total_orders')
            )
            ->where('vendor_id','=',$data['id'])
            ->where(DB::raw('YEAR(created_at)'),'=',$year)
            ->groupby('product_name')
            ->orderBy('total_quantity_orders','desc')
            ->get()
            ->toArray()
            );
        }
        $header = [];
        $content = [];
        foreach ($data as $key => $value) {
            array_push($header,$value->product_name);
            array_push($content,$value->total_quantity_orders);
        }
        $file_name = 'Top Selling Products ['.$year.']';


        if($type == 'EXCEL'){
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        }elseif($type == 'CSV'){
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.csv', \Maatwebsite\Excel\Excel::CSV);
        }elseif($type == 'PDF'){
            $data = [
                'title'=>$file_name,
                'header'=>$header,
                'content'=> $content
            ];
            $pdf = Pdf::loadView('admin.exportpdf.exportchartpdf',  array( 
                'title'=> $file_name,
                'header'=>$header,
                'columns'=>$header,
                'content'=> $content)
            );
            return $pdf->setPaper('a4', 'landscape')->download( $file_name.'.pdf');
        }else{
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.csv', \Maatwebsite\Excel\Excel::CSV);
        } 
    }
    
    // Display total vendor order statuses / filter by year
    public function vendororderStatusYear(Request $request,$year){
        $data = $request->session()->all();

        return (DB::table('orders_products as op')
        ->select(
            'item_status',
            DB::raw('count(*) as item_status_count')
        )
        ->where('vendor_id','=',$data['id'])
        ->where(DB::raw('YEAR(created_at)'),'=',$year)
        ->groupby('item_status')
        ->orderBy('item_status_count','desc')
        ->get()
        ->toArray()
        );
        return 'vendororderStatus';
    }

    // Create downloadable excel, csv, pdf reports reports for vendor order statuses / filter by year
    public function vendororderStatusYearDownload(Request $request,$type,$year){
        $data = $request->session()->all();

        $data = (DB::table('orders_products as op')
        ->select(
            'item_status',
            DB::raw('count(*) as item_status_count')
        )
        ->where('vendor_id','=',$data['id'])
        ->where(DB::raw('YEAR(created_at)'),'=',$year)
        ->groupby('item_status')
        ->orderBy('item_status_count','desc')
        ->get()
        ->toArray()
        );

        $header = [];
        $content = [];
        foreach ($data as $key => $value) {
            if($value->item_status){
                array_push($header,$value->item_status);
            }else{
                array_push($header,'NULL');
            }
            array_push($content,$value->item_status_count);
        }
        $file_name = 'Order Status ['.$year.']';

        if($type == 'EXCEL'){
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        }elseif($type == 'CSV'){
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.csv', \Maatwebsite\Excel\Excel::CSV);
        }elseif($type == 'PDF'){
            $data = [
                'title'=>$file_name,
                'header'=>$header,
                'content'=> $content
            ];
            $pdf = Pdf::loadView('admin.exportpdf.exportchartpdf',  array( 
                'title'=> $file_name,
                'header'=>$header,
                'columns'=>$header,
                'content'=> $content)
            );
            return $pdf->setPaper('a4', 'landscape')->download( $file_name.'.pdf');
        }else{
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.csv', \Maatwebsite\Excel\Excel::CSV);
        }
    }

    // Calculate vendor sales growth overtime
    public function vendorsalesGrowthOverTimeYearPrev(Request $request,$year){
        $data = $request->session()->all();

        return (DB::table('orders_products as op')
        ->select(
            DB::raw('YEAR(created_at) as year'), 
            DB::raw('MONTHNAME(created_at) as month'),
            DB::raw('MONTH(created_at) as month_num'),
            DB::raw('sum(product_qty*product_price) as total_per_month')
        )
        ->where('vendor_id','=',$data['id'])
        ->where(DB::raw('YEAR(created_at)'),'<',$year)
        ->groupby(DB::raw('YEAR(created_at), MONTH(created_at)'))
        ->get()
        ->toArray()
        );
        
        return 'vendorsalesGrowthOverTime';
    }

    // Calculate vendor sales growth overtime
    public function vendorsalesGrowthOverTimeYear(Request $request,$year){
        $data = $request->session()->all();

        return (DB::table('orders_products as op')
        ->select(
            DB::raw('YEAR(created_at) year'), 
            DB::raw('MONTHNAME(created_at) month'),
            DB::raw('MONTH(created_at) as month_num'),
            DB::raw('sum(product_qty*product_price) as total_per_month')
        )
        ->where('vendor_id','=',$data['id'])
        ->where(DB::raw('YEAR(created_at)'),'=',$year)
        ->groupby(DB::raw('YEAR(created_at), MONTH(created_at)'))
        ->get()
        ->toArray()
        );
        
        return 'vendorsalesGrowthOverTime';
    }


    public function getOrdersData(Request $request)
    {
        $request->validate([
            'startDate' => 'nullable|date',
            'endDate' => ['nullable', 'date', Rule::requiredIf($request->has('startDate'))],
        ]);

        $endDate = $request->input('endDate', now()->toDateString());

        // Calculate the default start date (10 days before the end date)
        $defaultStartDate = Carbon::parse($endDate)->subDays(10)->toDateString();

        $startDate = $request->input('startDate', $defaultStartDate);

        $barChartData = Order::select(
            DB::raw('DATE(updated_at) as date_updated'),
            DB::raw('SUM(grand_total) as total')
        )
        ->where(function ($query) use ($startDate, $endDate) {
            if ($startDate) {
                $query->where(DB::raw('DATE(updated_at)'), '>=', $startDate);
            }

            if ($endDate) {
                $query->where(DB::raw('DATE(updated_at)'), '<=', $endDate);
            }
        })
        ->where(function ($query) {
            $query->where('order_status', 'New')
                  ->orWhere('order_status', 'Shipped');
        })
        ->groupBy(DB::raw('DATE(updated_at)'))
        ->get();

        $lineChartData = Order::select(
            DB::raw('DATE(updated_at) as date_updated'),
            DB::raw('COUNT(id) as count')
        )
        ->where(function ($query) use ($startDate, $endDate) {
            if ($startDate) {
                $query->where(DB::raw('DATE(updated_at)'), '>=', $startDate);
            }

            if ($endDate) {
                $query->where(DB::raw('DATE(updated_at)'), '<=', $endDate);
            }
        })
        ->where(function ($query) {
            $query->where('order_status', 'New')
                  ->orWhere('order_status', 'Shipped');
        })
        ->groupBy(DB::raw('DATE(updated_at)'))
        ->get();

        return response()->json([
            'barChartData' => $barChartData,
            'lineChartData' => $lineChartData,
        ]);
    }

    // Display datas
    public function dashboard(Request $request){
        $data = $request->session()->all();
        Session::put('page','dashboard');
        $sectionsCount = Section::count();
        $categoriesCount = Category::count();
        $productsCount = Product::count();
        $ordersCount = Order::count('id');
        $salesTotal = Order::where('order_status', 'New')
                ->orWhere('order_status', 'Shipped')
                ->sum('grand_total');
        $couponsCount = Coupon::count();
        $brandsCount = Brand::count();
        $usersCount = User::count();

        $years = DB::table('orders_products')
        ->select(   
            DB::raw('YEAR(created_at) as year'),
        )
        ->groupby(DB::raw('YEAR(created_at)'))
        ->orderBy(DB::raw('YEAR(created_at)'),'desc')
        ->get()
        ->toArray();
        $vendors = [];
        if($years) {
            $vendors =DB::table('orders_products as op')
            ->select(   
                'v.name',
                'v.id'
            )
            ->join('vendors as v','v.id','op.vendor_id')
            ->join('orders as o','o.id','op.order_id')
            ->groupby('vendor_id')
            ->where(DB::raw('YEAR(op.created_at)'),$years[0]->year)
            ->get()
            ->toArray();   
         }

        return view('admin.dashboard')->with(compact('years','vendors','sectionsCount','categoriesCount','productsCount','ordersCount','salesTotal','couponsCount','brandsCount','usersCount'));
    }

    // Update admin password
    public function updateAdminPassword(Request $request){
        Session::put('page','update_admin_password');
        if($request->isMethod('post')){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/
            // Check if current password enterted by admin is correct
            if(Hash::check($data['current_password'],Auth::guard('admin')->user()->password)){
                // Check if new password is matching with confirm password
                if($data['confirm_password']==$data['new_password']){
                    Admin::where('id',Auth::guard('admin')->user()->id)->update(['password'=>bcrypt($data['new_password'])]);
                    return redirect()->back()->with('success_message','Password has been updated successfully!');
                }else{
                    return redirect()->back()->with('error_message','New Password and Confirm Password does not match!');    
                }
            }else{
                return redirect()->back()->with('error_message','Your current password is Incorrect!');
            }
        }
        $adminDetails = Admin::where('email',Auth::guard('admin')->user()->email)->first()->toArray();
        return view('admin.settings.update_admin_password')->with(compact('adminDetails'));
    }

    // Check admin password
    public function checkAdminPassword(Request $request){
        $data = $request->all();
        /*echo "<pre>"; print_r($data); die;*/
        if(Hash::check($data['current_password'],Auth::guard('admin')->user()->password)){
            return "true";
        }else{
            return "false";
        }
    }

    // Update admin details
    public function updateAdminDetails(Request $request){
        Session::put('page','update_admin_details');
        if($request->isMethod('post')){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/
            // Enter validation
            $rules = [
                'admin_name' => 'required|regex:/^[\pL\s\-]+$/u',
                'admin_mobile' => 'required|numeric',
            ];
            // Custom error messages
            $customMessages = [
                'admin_name.required' => 'Name is required',
                'admin_name.regex' => 'Valid Name is required',
                'admin_mobile.required' => 'Mobile is required',
                'admin_mobile.numeric' => 'Valid Mobile is required',
            ];

            $this->validate($request,$rules,$customMessages);

            // Upload Admin Photo
            if($request->hasFile('admin_image')){
                $image_tmp = $request->file('admin_image');
                if($image_tmp->isValid()){
                    // Get Image Extension
                    $extension = $image_tmp->getClientOriginalExtension();
                    // Generate New Image Name
                    $imageName = rand(111,99999).'.'.$extension;
                    $imagePath = 'admin/images/photos/'.$imageName;
                    // Upload the Image
                    Image::make($image_tmp)->save($imagePath);
                }
            }else if(!empty($data['current_admin_image'])){
                $imageName = $data['current_admin_image'];
            }else{
                $imageName = "";
            }

            // Update Admin Details
            Admin::where('id',Auth::guard('admin')->user()->id)
            ->update(['name'=>$data['admin_name'],
            'mobile'=>$data['admin_mobile'],
            'image'=>$imageName]);
            
            return redirect()->back()->with('success_message','Admin details updated successfully!');
        }
        return view('admin.settings.update_admin_details');
    }

    // Update vendor details
    public function updateVendorDetails($slug, Request $request){
        if($slug=="personal"){
            Session::put('page','update_personal_details');
            if($request->isMethod('post')){
                $data = $request->all();
                /*echo "<pre>"; print_r($data); die;*/
                // Enter Validation
                $rules = [
                'vendor_name' => 'required|regex:/^[\pL\s\-]+$/u',
                'vendor_city' => 'required|regex:/^[\pL\s\-]+$/u',
                'vendor_mobile' => 'required|numeric',
                ];
                // Custom erro messages
                $customMessages = [
                    'vendor_name.required' => 'Name is required',

                    'vendor_name.regex' => 'Valid Name is required',
                    'vendor_city.regex' => 'Valid City is required',
                    'vendor_mobile.required' => 'Mobile is required',
                    'vendor_mobile.numeric' => 'Valid Mobile is required',
                ];

                $this->validate($request,$rules,$customMessages);

                // Upload Admin Photo
                if($request->hasFile('vendor_image')){
                    $image_tmp = $request->file('vendor_image');
                    if($image_tmp->isValid()){
                        // Get Image Extension
                        $extension = $image_tmp->getClientOriginalExtension();
                        // Generate New Image Name
                        $imageName = rand(111,99999).'.'.$extension;
                        $imagePath = 'admin/images/photos/'.$imageName;
                        // Upload the Image
                        Image::make($image_tmp)->save($imagePath);
                    }
                }else if(!empty($data['current_vendor_image'])){
                    $imageName = $data['current_vendor_image'];
                }else{
                    $imageName = "";
                }

                // Update in admins table
                Admin::where('id',Auth::guard('admin')->user()->id)->update(['name'=>$data['vendor_name'],'mobile'=>$data['vendor_mobile'],'image'=>$imageName]);
                $commision = 0;
                // Update in vendors table
                Vendor::where('id',Auth::guard('admin')->user()->vendor_id)->update(['name'=>$data['vendor_name'],'mobile'=>$data['vendor_mobile'],'address'=>$data['vendor_address'],'city'=>$data['vendor_city'],'barangay'=>$data['vendor_barangay'], 'commission'=> $commision,'country'=>$data['vendor_country'],'pincode'=>$data['vendor_pincode']]);
                return redirect()->back()->with('success_message','Vendor details updated successfully!');
            }
            $vendorDetails = Vendor::where('id',Auth::guard('admin')->user()->vendor_id)->first()->toArray();
        }else if($slug=="business"){
            Session::put('page','update_business_details');
            if($request->isMethod('post')){
                $data = $request->all();
                /*echo "<pre>"; print_r($data); die;*/
                // Enter Validation
                $rules = [
                'shop_name' => 'required|regex:/^[\pL\s\-]+$/u',
                'shop_city' => 'required|regex:/^[\pL\s\-]+$/u',
                'shop_mobile' => 'required|numeric',
                'address_proof' => 'required',
                ];
                // Custom Erro Messages
                $customMessages = [
                    'shop_name.required' => 'Name is required',
                    'shop_name.regex' => 'Valid Name is required',
                    'shop_city.regex' => 'Valid City is required',
                    'shop_mobile.required' => 'Mobile is required',
                    'shop_mobile.numeric' => 'Valid Mobile is required',
                    'address_proof.required' => 'Image is required',
                ];

                $this->validate($request,$rules,$customMessages);

                // Upload Admin Photo
                if($request->hasFile('address_proof_image')){
                    $image_tmp = $request->file('address_proof_image');
                    if($image_tmp->isValid()){
                        // Get Image Extension
                        $extension = $image_tmp->getClientOriginalExtension();
                        // Generate New Image Name
                        $imageName = rand(111,99999).'.'.$extension;
                        $imagePath = 'admin/images/proofs/'.$imageName;
                        // Upload the Image
                        Image::make($image_tmp)->save($imagePath);
                    }
                }else if(!empty($data['current_address_proof'])){
                    $imageName = $data['current_address_proof'];
                }else{
                    $imageName = "";
                }
                // Upload Permit Proof
                if($request->hasFile('permit_proof_image')){
                    $image_tmp = $request->file('permit_proof_image');
                    if($image_tmp->isValid()){
                        // Get Image Extension
                        $extension = $image_tmp->getClientOriginalExtension();
                        // Generate New Image Name
                        $permitimageName = rand(111,99999).'.'.$extension;
                        $imagePath = 'admin/images/proofs/'.$permitimageName;
                        // Upload the Image
                        Image::make($image_tmp)->save($imagePath);
                    }
                }else if(!empty($data['current_permit_proof'])){
                    $permitimageName = $data['current_permit_proof'];
                }else{
                    $permitimageName = "";
                }
                $vendorCount = VendorsBusinessDetail::where('vendor_id',Auth::guard('admin')->user()->vendor_id)->count();
                if($vendorCount>0){
                  
                    // Update in vendors_business_details table
                VendorsBusinessDetail::where('vendor_id',Auth::guard('admin')->user()->vendor_id)->update(['shop_name'=>$data['shop_name'],'shop_mobile'=>$data['shop_mobile'],'shop_address'=>$data['shop_address'],'shop_city'=>$data['shop_city'],'shop_barangay'=>$data['shop_barangay'],'shop_country'=>$data['shop_country'],'shop_pincode'=>$data['shop_pincode'],'business_license_number'=>$data['business_license_number'],'gst_number'=>$data['gst_number'],'pan_number'=>$data['pan_number'],'address_proof'=>$data['address_proof'],'address_proof_image'=>$imageName,'permit_proof_image'=>$permitimageName]);
                }else{
                    // Update in vendors_business_details table
                    VendorsBusinessDetail::insert(['vendor_id'=>Auth::guard('admin')->user()->vendor_id,'shop_name'=>$data['shop_name'],'shop_mobile'=>$data['shop_mobile'],'shop_address'=>$data['shop_address'],'shop_city'=>$data['shop_city'],'shop_barangay'=>$data['shop_barangay'],'shop_country'=>$data['shop_country'],'shop_pincode'=>$data['shop_pincode'],'business_license_number'=>$data['business_license_number'],'gst_number'=>$data['gst_number'],'pan_number'=>$data['pan_number'],'address_proof'=>$data['address_proof'],'address_proof_image'=>$imageName,'permit_proof_image'=>$permitimageName]);    
                }
                
                return redirect()->back()->with('success_message','Vendor details updated successfully!');
            }
            $vendorCount = VendorsBusinessDetail::where('vendor_id',Auth::guard('admin')->user()->vendor_id)->count();
            if($vendorCount>0){
                $vendorDetails = VendorsBusinessDetail::where('vendor_id',Auth::guard('admin')->user()->vendor_id)->first()->toArray();    
            }else{
                $vendorDetails = array();   
            }
            
            /*dd($vendorDetails);*/
        }else if($slug=="bank"){
            Session::put('page','update_bank_details');
            if($request->isMethod('post')){
                $data = $request->all();
                /*echo "<pre>"; print_r($data); die;*/
                // Enter validation
                $rules = [
                'account_holder_name' => 'required|regex:/^[\pL\s\-]+$/u',
                'bank_name' => 'required',
                'account_number' => 'required|numeric',
                'bank_ifsc_code' => 'required',
                ];
                // Custom error messages
                $customMessages = [
                    'account_holder_name.required' => 'Account Holder Name is required',
                    'account_holder_name.regex' => 'Valid Account Holder Name is required',
                    'bank_name.required' => 'Bank Name is required',
                    'account_number.required' => 'Account Number is required',
                    'account_number.numeric' => 'Valid Account Number is required',
                    'bank_ifsc_code.required' => 'Bank IFSC Code is required',
                ];

                $this->validate($request,$rules,$customMessages);
                $vendorCount = VendorsBankDetail::where('vendor_id',Auth::guard('admin')->user()->vendor_id)->count();
                if($vendorCount>0){
                    // Update in vendors_bank_details table
                    VendorsBankDetail::where('vendor_id',Auth::guard('admin')->user()->vendor_id)->update(['account_holder_name'=>$data['account_holder_name'],'bank_name'=>$data['bank_name'],'account_number'=>$data['account_number'],'bank_ifsc_code'=>$data['bank_ifsc_code']]);
                }else{
                    VendorsBankDetail::insert(['vendor_id'=>Auth::guard('admin')->user()->vendor_id,'account_holder_name'=>$data['account_holder_name'],'bank_name'=>$data['bank_name'],'account_number'=>$data['account_number'],'bank_ifsc_code'=>$data['bank_ifsc_code']]);
                }
                return redirect()->back()->with('success_message','Vendor details updated successfully!');
            }

            $vendorCount = VendorsBankDetail::where('vendor_id',Auth::guard('admin')->user()->vendor_id)->count();
            if($vendorCount>0){
                $vendorDetails = VendorsBankDetail::where('vendor_id',Auth::guard('admin')->user()->vendor_id)->first()->toArray();    
            }else{
                $vendorDetails = array();   
            }
        }
        $countries = Country::where('status',1)->get()->toArray();
        $barangay = Barangay::all()->toArray();
        return view('admin.settings.update_vendor_details')->with(compact('slug','vendorDetails','countries','barangay'));
    }

    public function updateVendorCommission(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/
            // Update in vendors table
            Vendor::where('id',$data['vendor_id'])->update(['commission'=>$data['commission']]);
            return redirect()->back()->with('success_message','Vendor commission updated successfully!');
        }
    }

    // Login to account
    public function login(Request $request){
        // echo $password = Hash::make('123456'); die;

        if($request->isMethod('post')){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/
            // Enter Validation
            $rules = [
                'email' => 'required|email|max:255',
                'password' => 'required',
            ];
            // Custom Error Messages
            $customMessages = [
                // Add Custom Messages here
                'email.required' => 'Email is required!',
                'email.email' => 'Valid Email is required',
                'password.required' => 'Password is required',
            ];

            $this->validate($request,$rules,$customMessages);

            /*if(Auth::guard('admin')->attempt(['email'=>$data['email'],'password'=>$data['password'],'status'=>1])){
                return redirect('admin/dashboard');
            }else{
                return redirect()->back()->with('error_message','Invalid Email or Password');
            }*/

            if(Auth::guard('admin')->attempt(['email'=>$data['email'],'password'=>$data['password']])){
                $request->session()->put('id',  Auth::guard('admin')->user()->vendor_id);
                // Confirm  email
                if(Auth::guard('admin')->user()->type=="vendor" && Auth::guard('admin')->user()->confirm=="No"){
                    return redirect()->back()->with('error_message','Please confirm your email to activate your Seller Account');
                // Account not active
                }else if(Auth::guard('admin')->user()->type!="vendor" && Auth::guard('admin')->user()->status=="0"){
                    return redirect()->back()->with('error_message','Your admin account is not active');
                // Redirect vendor dashboard
                }else if(Auth::guard('admin')->user()->type=="vendor"){
                    return redirect('admin/vendordashboard');
                }
                // Redirect admin dashboard
                else{
                    return redirect('admin/dashboard');    
                }
            // Invalid email or password
            }else{
                return redirect()->back()->with('error_message','Invalid Email or Password');
            }

        }
        return view('admin.login');
    }

    // View all admins
    public function admins($type=null){
        $admins = Admin::query();
        if(!empty($type)){
            $admins = $admins->where('type',$type);   
            $title = ucfirst($type)."s";
            Session::put('page','view_'.strtolower($title));
        }else{
            $title = "All Admins/Subadmins/Vendors";
            Session::put('page','view_all');
        }
        $admins = $admins->get()->toArray();
        // dd($admins);
        return view('admin.admins.admins')->with(compact('admins','title'));
    }

    // View vendor details
    public function viewVendorDetails($id){
        $vendorDetails = Admin::with('vendorPersonal','vendorBusiness','vendorBank')->where('id',$id)->first();
        $vendorDetails = json_decode(json_encode($vendorDetails),true);
        /*dd($vendorDetails);*/
        return view('admin.admins.view_vendor_details')->with(compact('vendorDetails'));
    }

    // Update admin status
    public function updateAdminStatus(Request $request){
        if($request->ajax()){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/
            if($data['status']=="Active"){
                $status = 0;
            }else{
                $status = 1;
            }
            Admin::where('id',$data['admin_id'])->update(['status'=>$status]);
            $adminDetails = Admin::where('id',$data['admin_id'])->first()->toArray();
            if($adminDetails['type']=="vendor" && $status==1){
                Vendor::where('id',$adminDetails['vendor_id'])->update(['status'=>$status]);
                // Send Approval Email
                $email = $adminDetails['email'];
                $messageData = [
                    'email' => $adminDetails['email'],
                    'name' => $adminDetails['name'],
                    'mobile' => $adminDetails['mobile']
                ];
                // Message Content
                Mail::send('emails.vendor_approved',$messageData,function($message)use($email){
                    $message->to($email)->subject('Vendor Account is Approved');
                });
            }

            return response()->json(['status'=>$status,'admin_id'=>$data['admin_id']]);
        }
    }

    // Logout Account
    public function logout(){
        Auth::guard('admin')->logout();
        return redirect('admin/login');
    }

}
