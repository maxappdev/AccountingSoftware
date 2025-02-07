<?php

namespace Modules\Companies\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Companies\Entities\Company;
use Modules\Companies\Entities\Currency;
use Modules\Companies\Entities\Branch;
use Modules\Companies\Entities\Address;
use Modules\Companies\Entities\City;
use Modules\Companies\Entities\Country;
use Modules\Users\Entities\User;
use Modules\Users\Entities\UserHasBranch;
use Modules\Companies\Http\Requests\StoreBranchRequest;
use Modules\Companies\Http\Requests\UpdateBranchRequest;
use Modules\Warehouses\Entities\Warehouse;


use BranchesService;

class BranchesController extends Controller
{

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $user = User::where('login_id', auth('api')->user()->id)->firstOrFail();
        $branches = BranchesService::getUserBranches($user->id);

        return response()->json($branches);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('companies::branches.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(StoreBranchRequest $request)
    {
        $branch = new Branch();
        $branch = $branch->store($request);
        $address = Address::find($branch->address_id);
        $city = City::find($address->city_id);
        $branch->city_name = $city->name;
        $branch->country_name = Country::find($city->country_id)->code;
        $branch->street_name = $address->street_name;
        $branch->house_number = $address->house_number;
        $branch->postcode = $address->postcode;

        return response()->json([
             'message' => 'Successfully created!',
             'branch' => $branch
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(UpdateBranchRequest $request, $id)
    {
        $branch = Branch::find($id);
        $branch = $branch->storeUpdated($request);

        return response()->json([
            'message' => 'Successfully updated!',
            'branch' => $branch
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $branch = Branch::find($id);

        if(!BranchesService::checkThisBranchHasEmployees($branch) && !BranchesService::checkThisBranchHasCustomers($branch)){
          $warehouse = Warehouse::where('branch_id',$branch->id)->first();
          $warehouse->delete();
          $branch->delete();
        }else{
          return response()->json([
              'message' => 'You can not delete this branch(it has employees or customers)'
          ], 403);
        }
          return response()->json([
              'message' => 'Successfully deleted!'
          ], 200);
    }
}
