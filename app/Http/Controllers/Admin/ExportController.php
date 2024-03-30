<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Exporter;
use App\Http\Controllers\Admin\Export\ExporterController;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ExportController extends Controller
{
    // Export vendors
    public function adminSeller(Request $request,$type,$columns,$column_names){
        // Fetch vendor datas
        $data = $request->session()->all();
        $columns = json_decode($columns);
        $column_names = json_decode($column_names);
        if($data['id'] == 0){
            $temp_content = DB::table('admins')
                ->select($columns)
                ->where('type','=','vendor')
                ->get()
                ->toArray();
        }else{
            $temp_content = DB::table('admins')
            ->select($columns)
            ->where('type','=','vendor')
            ->where('vendor_id','=',$data['id'])
            ->get()
            ->toArray();
        }
        $content = [];
        foreach ($temp_content as $key => $value) {
            $item = [];
            foreach ($columns as $column_key => $column_value) {
                array_push($item,$value->{$column_value} );
            }
            array_push($content,$item);
        }
        $header = $column_names;
        $file_name = 'Sellers';
        // Export as excel
        if($type == 'EXCEL'){
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        // Export as csv
        }elseif($type == 'CSV'){
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.csv', \Maatwebsite\Excel\Excel::CSV);
        // Export as PDF
        }elseif($type == 'PDF'){
            // Call necessary datas to be seen inside the file
            $data = [
                'title'=>$file_name,
                'header'=>$header,
                'content'=> $content
            ];
            $pdf = Pdf::loadView('admin.exportpdf.exportpdf',  array( 
                'title'=> $file_name,
                'header'=>$header,
                'columns'=>$columns,
                'content'=> $content)
            );
            return $pdf->setPaper('a4', 'landscape')->download( $file_name.'.pdf');
        }else{
            return Excel::download($export, $file_name.'.csv', \Maatwebsite\Excel\Excel::CSV);
        }
    }

    // Export products
    public function adminProducts(Request $request,$type,$columns,$column_names){
        // Fetch product datas
        $data = $request->session()->all();
        $columns = json_decode($columns);
        $column_names = json_decode($column_names);
        if($data['id'] == 0){
            $temp_content = DB::table('products as p')
            ->select($columns)
            ->join('sections as s','p.section_id','s.id')
            ->join('categories as c','p.category_id','c.id')
            ->join('vendors as v','p.vendor_id','v.id')
            ->get()
            ->toArray();
        }else{
            $temp_content = DB::table('products as p')
            ->select($columns)
            ->join('sections as s','p.section_id','s.id')
            ->join('categories as c','p.category_id','c.id')
            ->join('vendors as v','p.vendor_id','v.id')
            ->where('vendor_id','=',$data['id'])
            ->get()
            ->toArray();
        }
        $content = [];
        foreach ($temp_content as $key => $value) {
            $item = [];
            foreach ($columns as $column_key => $column_value) {
                $pos =( strpos($column_value, '.'));
                if($pos){
                    $pos  +=1;
                    $string  = substr($column_value,$pos);
                }else{
                    $string  = $column_value;
                }
                array_push($item,$value->{$string} );
            }
            array_push($content,$item);
        }
        $header = $column_names;
        $file_name = 'Products';
        // Export as excel
        if($type == 'EXCEL'){
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        // Export as csv
        }elseif($type == 'CSV'){
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.csv', \Maatwebsite\Excel\Excel::CSV);
        // Export pdf
        }elseif($type == 'PDF'){
            $data = [
                'title'=>$file_name,
                'header'=>$header,
                'content'=> $content
            ];
            $pdf = Pdf::loadView('admin.exportpdf.exportpdf',  array( 
                'title'=> $file_name,
                'header'=>$header,
                'columns'=>$columns,
                'content'=> $content)
            );
            return $pdf->setPaper('a4', 'landscape')->download( $file_name.'.pdf');
        }else{
            return Excel::download($export, $file_name.'.csv', \Maatwebsite\Excel\Excel::CSV);
        }
    }

    // Export customers
    public function adminCustomers(Request $request,$type,$columns,$column_names){
        // Fetch customers datas
        $data = $request->session()->all();
        $columns = json_decode($columns);
        $column_names = json_decode($column_names);
        if($data['id'] == 0){
            $temp_content = DB::table('users as u')
            ->select($columns)
            ->get()
            ->toArray();
        }else{
            $temp_content = DB::table('users as u')
            ->select($columns)
            ->where('vendor_id','=',$data['id'])
            ->get()
            ->toArray();
        }
        $content = [];
        foreach ($temp_content as $key => $value) {
            $item = [];
            foreach ($columns as $column_key => $column_value) {
                $pos =( strpos($column_value, '.'));
                if($pos){
                    $pos  +=1;
                    $string  = substr($column_value,$pos);
                }else{
                    $string  = $column_value;
                }
                array_push($item,$value->{$string} );
            }
            array_push($content,$item);
        }
        $header = $column_names;
        $file_name = 'Customers';
        // Export as excel
        if($type == 'EXCEL'){
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        // Export as csv
        }elseif($type == 'CSV'){
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.csv', \Maatwebsite\Excel\Excel::CSV);
        // Export as pdf
        }elseif($type == 'PDF'){
            $data = [
                'title'=>$file_name,
                'header'=>$header,
                'content'=> $content
            ];
            $pdf = Pdf::loadView('admin.exportpdf.exportpdf',  array(
                'title'=> $file_name,
                'header'=>$header,
                'columns'=>$columns,
                'content'=> $content)
            );
            return $pdf->setPaper('a4', 'landscape')->download( $file_name.'.pdf');
        }else{
            return Excel::download($export, $file_name.'.csv', \Maatwebsite\Excel\Excel::CSV);
        }
    }

    // Export orders
    public function adminOrders(Request $request,$type,$columns,$column_names){
        // Fetch order datas
        $data = $request->session()->all();
        $columns = json_decode($columns);
        $column_names = json_decode($column_names);
        if($data['id'] == 0){
            $temp_content = DB::table('orders as o')
            ->select($columns)
            ->join('users as u','o.user_id','u.id')
            ->rightjoin('orders_products as od','od.order_id','o.id')
            ->get()
            ->toArray();
        }else{
            $temp_content = DB::table('orders as o')
            ->select($columns)
            ->join('users as u','o.user_id','u.id')
            ->rightjoin('orders_products as od','od.order_id','o.id')
            ->where('vendor_id','=',$data['id'])
            ->get()
            ->toArray();
        }
        $content = [];
        foreach ($temp_content as $key => $value) {
            $item = [];
            foreach ($columns as $column_key => $column_value) {
                $pos =( strpos($column_value, '.'));
                if($pos){
                    $pos  +=1;
                    $string  = substr($column_value,$pos);
                }else{
                    $string  = $column_value;
                }
                array_push($item,$value->{$string} );
            }
            array_push($content,$item);
        }
        $header = $column_names;
        $file_name = 'Orders';
        // Export as excel
        if($type == 'EXCEL'){
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        // Export as csv
        }elseif($type == 'CSV'){
            $export = new ExporterController([
                $header,
                $content
            ]);
            return Excel::download($export, $file_name.'.csv', \Maatwebsite\Excel\Excel::CSV);
        // Export as pdf
        }elseif($type == 'PDF'){
            $data = [
                'title'=>$file_name,
                'header'=>$header,
                'content'=> $content
            ];
            $pdf = Pdf::loadView('admin.exportpdf.exportpdf',  array( 
                'title'=> $file_name,
                'header'=>$header,
                'columns'=>$columns,
                'content'=> $content)
            );
            return $pdf->setPaper('a4', 'landscape')->download( $file_name.'.pdf');
        }else{
            return Excel::download($export, $file_name.'.csv', \Maatwebsite\Excel\Excel::CSV);
        }
    }
    
}
