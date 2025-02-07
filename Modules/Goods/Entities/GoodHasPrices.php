<?php

namespace Modules\Goods\Entities;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Eloquent\Model;
use Log;
use Modules\Goods\Entities\BranchHasGood;

class GoodHasPrices extends Model
{
    protected $fillable = ['good_id','branch_id','supplier_id','retail_price','wholesale_price'];

    public static function updateRetailPrice($good,$branch_id,$supplier_id)
    {

        Log::info("out of loop");
        
        if(isset($good['retail_price'])){
            $good_has_price = self::where('good_id',$good['good_id'])
                ->where('branch_id',$branch_id)
                ->where('supplier_id',$supplier_id)
                ->first();
            
            if(!$good_has_price){
                $good_has_price = self::where('good_id',$good['good_id'])
                ->where('branch_id',$branch_id)
                ->where('supplier_id',null)
                ->first();
            }
                        
            Log::info("in the loop");

            if(!$good_has_price){
                $good_has_price = new GoodHasPrices();
                $good_has_price->good_id = $good['good_id'];
                $good_has_price->branch_id = $branch_id;
            }

            $good_has_price->retail_price = $good['retail_price'];
            $good_has_price->supplier_id = $supplier_id;
            $good_has_price->save();

            //throw new \Exception("good_has_price:" . $good_has_price);

        }
    }

    public function store(FormRequest $request){
      $this->good_id = $request->good_id;
      $this->branch_id = $request->branch_id;
      $this->supplier_id = $request->supplier_id;
      $this->retail_price = $request->retail_price;
      $this->wholesale_price = $request->wholesale_price;
      $this->save();
      return $this;
    }

    public function edit(FormRequest $request){
      $this->retail_price = $request->retail_price;
      $this->wholesale_price = $request->wholesale_price;
      $this->save();
      return $this;
    }
}
