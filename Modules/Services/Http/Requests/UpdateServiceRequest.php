<?php

namespace Modules\Services\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Services\Entities\Service;
use Modules\Services\Entities\ServicesTranslation;
use Modules\Services\Entities\CompanyHasService;

class UpdateServiceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   

        return [
            'name' => 'required'
        ];
    }

    public function nameUnique(){

        //check if name is unique (only for 1 company)
        $company = auth('api')->user()->getCompany();
        $services_of_company_ids = CompanyHasService::where('company_id', $company->id)->pluck('service_id')->toArray();
        $services_ids = Service::whereIn('id', $services_of_company_ids)->OrWhere('is_custom', 0)->pluck('id')->toArray();

        $chosen_service = Service::find($this->route('service_id'));

        $names = ServicesTranslation::whereIn('service_id', $services_ids)->pluck('name')->toArray();
        $names = array_filter($names, function ($x) use($chosen_service) { return $x != $chosen_service->getTranslatedName(1);});

        return !in_array($this->name, $names);

    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}