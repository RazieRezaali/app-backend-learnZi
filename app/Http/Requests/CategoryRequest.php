<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $route = Route::current();
        if($route->getName() == 'category.store'){
            return [
                'name'       => ['required', 'string', 'min:2', 'max:30', 'unique:categories'],
                'parent_id'  => ['sometimes', 'nullable', 'integer', 'exists:categories,id'],
            ];
        } elseif(in_array($route->getName(), ['category.get.cards', 'category.get.quiz.characters'])){
            return [
                'categoryId'  => ['required', 'integer', 'exists:categories,id'],
            ];
        }
    }

    public function prepareForValidation()
    {
        if (isset($this->categoryId)){
            $this->merge([
                'categoryId' => $this->categoryId,
            ]);
        }
    }
}

/*
    public function rules(): array
    {
        $route = Route::current();
        if($route->getName() == 'event.store'){
            return [
                'business_id'            => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
                // 'name'                   => ['required', 'string', 'min:2', 'max:120', 'regex:/^[ا-یa-zA-Z0-9\-۰-۹ء-ي., \x{200C}]+$/u'],
                'name'                   => ['required', 'string', 'min:2', 'max:120'],
                'type'                   => ['sometimes', 'nullable', 'integer', 'in:' . implode(',', EventType::getAllTypesInt())],
                'start_date'             => ['required', 'integer', 'min:0', 'date_format:U', new EventDate('end_date')],
                'end_date'               => ['required', 'integer', 'min:0', 'date_format:U', 'after:start_date', new EventDate('start_date')],
                'style'                  => ['required', 'integer', 'in:' . implode(',', EventStyle::getAllStylesInt())],
                'guest_limit'            => ['required', 'integer', 'in:' . implode(',', EventGuestLimit::getAllGueslLimitsInt())],
                'repeat'                 => ['required', 'integer', 'in:' . implode(',', EventRepeat::getAllRepeatsInt())],
                'discount_percentage'    => ['sometimes', 'nullable', 'integer', 'between:0,100'],
                // 'description'            => ['sometimes', 'nullable', 'string', 'min:10', 'max:1500', 'regex:/^[ا-یa-zA-Z0-9\-۰-۹ء-ي.,«»><\/؛;\n\r&?؟،\s \x{200C}]+$/u'],
                'description'            => ['sometimes', 'nullable', 'string', 'min:10', 'max:1500'],                'options'                => ['required', 'array'], 
                'options.*'              => ['required', new Option()],
                'tasks'                  => ['required', 'array'], 
                'tasks.*'                => ['string', new Task()],
                'images'                 => ['required', 'array', 'min:1', 'max:3'], 
                'images.*'               => ['required', 'string', new Image()],
                'use_custom_address'     => ['required', 'boolean'],
                'province_id'            => ['sometimes', 'nullable', 'required_if:use_custom_address,true', 'integer','min:1', 'max:10000', 'exists:provinces,id'],
                'city_id'                => ['sometimes', 'nullable', 'required_if:use_custom_address,true', 'integer', 'min:1', 'max:1000000', 'exists:cities,id'],
                // 'district'               => ['sometimes', 'nullable', 'string', 'max:100', 'regex:/^[ا-یa-zA-Z0-9\-۰-۹ء-ي\s \/\x{200C}]+$/u'],
                'district'               => ['sometimes', 'nullable', 'string', 'max:100'],
                // 'exact_address'          => ['sometimes', 'nullable', 'required_if:use_custom_address,true', 'string', 'max:255', 'regex:/^[ا-یa-zA-Z0-9\-۰-۹ء-ي\s،؛.,.; \x{200C}]+$/u'],
                'exact_address'          => ['sometimes', 'nullable', 'required_if:use_custom_address,true', 'string', 'max:255'],
                'postal_code'            => ['sometimes', 'nullable', new PostalCode()],
                // 'location_name'          => ['sometimes', 'nullable', 'required_if:use_custom_address,true', 'string', 'regex:/^[\p{L}0-9\s,.-]+$/u'],
                'location_name'          => ['sometimes', 'nullable', 'required_if:use_custom_address,true', 'string', 'max:20'],
                'latitude'               => ['sometimes', 'nullable', 'required_if:use_custom_address,true', 'numeric', 'between:-90,90'],
                'longitude'              => ['sometimes', 'nullable', 'required_if:use_custom_address,true', 'numeric', 'between:-180,180'],
                'gender_restriction'     => ['sometimes' , 'nullable', 'integer', Rule::in([UserGender::MALE, UserGender::FEMALE,UserGender::BOTH])],
                'men_number'             => ['sometimes', 'nullable', 'integer', 'in:' . implode(',', EventInfluencerNumber::getAllNumbersInt())],
                'women_number'           => ['sometimes', 'nullable', 'integer', 'in:' . implode(',', EventInfluencerNumber::getAllNumbersInt())],
                'follower_limitation'    => ['required', 'integer', 'in:' . implode(',', FollowerCount::getAllNumbersInt())],
                'exclude_influencers'    => ['required', 'boolean'],
            ];
        } elseif($route->getName() == 'event.get.all.by-filter'){
            return [
                'cities'      => ['sometimes', 'array'],
                'cities.*'    => ['required_with:cities', 'integer', 'exists:cities,id'],
                'province'    => ['sometimes',  'integer', 'exists:provinces,id'],
                'hashtags'    => ['sometimes', 'string', new Hashtag()],  
                'sort'        => ['sometimes', 'integer', Rule::in([EventSort::FUTURE, EventSort::NEW])],
                'date'        => ['sometimes', 'string', 'min:0', 'date_format:U'],
                'page'        => ['sometimes', 'integer', 'min:1'],
                'location_name '    => ['sometimes','string','nullable'],
                'location_name.*'   => ['required_with:location_name',' string'],
                'date_filter'       => ['sometimes','integer', Rule::in([EventSort::TODAY, EventSort::TOMORROW, EventSort::THIS_WEEK])],
                'category_id'       => ['sometimes','nullable', 'integer', 'exists:business_categories,id'],
            ];
        } elseif($route->getName() == 'event.get.business-events'){
            return [
                'status'    => ['sometimes', 'integer', 'in:' . implode(',', EventStatus::getAllEventStatusInt())],
                'page'      => ['sometimes', 'integer', 'min:1'],
            ];
        } elseif($route->getName() == 'event.confirm'){
            return [
                'event_id' => ['required', 'integer', 'exists:events,id'],
            ];
        }elseif(in_array($route->getName(), ['update.event', 'admin.update.event'])){
            $event=$this->route('event');
            $isFake = $event->status  === EventStatus::FAKE;
            return [
                'name'                   => ['required', 'string', 'min:2', 'max:120'],
                'type'                   => ['sometimes', 'nullable', 'integer', 'in:' . implode(',', EventType::getAllTypesInt())],
                'start_date'             => ['required', 'integer', 'min:0', 'date_format:U', new EventDate('end_date')],
                'end_date'               => ['required', 'integer', 'min:0', 'date_format:U', 'after:start_date', new EventDate('start_date')],
                'style'                  => ['required', 'integer', 'in:' . implode(',', EventStyle::getAllStylesInt())],
                'guest_limit'            => ['required', 'integer', 'in:' . implode(',', EventGuestLimit::getAllGueslLimitsInt())],
                'discount_percentage'    => ['sometimes', 'nullable', 'integer', 'between:0,100'],
                'description'            => ['sometimes', 'nullable', 'string', 'min:10', 'max:1500'],
                'options'                => ['required', 'array'], 
                'options.*'              => ['required', new Option()],
                'tasks'                  => ['required', 'array'], 
                'tasks.*'                => ['string', new Task()],
                'province_id'            => ['sometimes', 'nullable', 'required_if:use_custom_address,true', 'integer','min:1', 'max:10000', 'exists:provinces,id'],
                'city_id'                => ['sometimes', 'nullable', 'required_if:use_custom_address,true', 'integer', 'min:1', 'max:1000000', 'exists:cities,id'],
                'district'               => ['sometimes', 'nullable', 'string', 'max:100'],
                'exact_address'          => ['sometimes', 'nullable', 'required_if:use_custom_address,true', 'string', 'max:255'],
                'postal_code'           =>  ['sometimes', 'nullable', !$isFake ? new PostalCode(): null],
                // 'location_name'          => ['sometimes', 'nullable', 'required_if:use_custom_address,true', 'string', 'regex:/^[\p{L}0-9\s,.-]+$/u'],
                'location_name'          => ['sometimes', 'nullable', 'required_if:use_custom_address,true', 'string', 'max:20'],
                'latitude'               => ['sometimes', 'nullable', 'required_if:use_custom_address,true', 'numeric', 'between:-90,90'],
                'longitude'              => ['sometimes', 'nullable', 'required_if:use_custom_address,true', 'numeric', 'between:-180,180'],
            ];
        } elseif( in_array($route->getName(), ['event.get.my.tasks','event.get.spesial.by-status','event.get.awaiting.confirmation',
            'event.get.drafts', 'event.get.archive.events', 'get.business.event.uploaded-tasks', 'event.get.category.events',
            'get.events.waiting-scan'])){
            return [
                'page'      => ['sometimes', 'integer', 'min:1'],
            ];
        } elseif($route->getName() == 'event.cancel'){
            $eventInfluencer = $this->route('event')->influencer_id;
            return [
                'business_id' => ['sometimes', 'nullable', 'integer', 'exists:users,id', new BusinessRole()],
                'cancellation_reason' => [Rule::requiredIf(!is_null($eventInfluencer)), 'nullable', 'string', 'min:2', 'max:1000'],
            ];
        } elseif($route->getName() == 'event.search'){
            return [
                'search_query' => ['sometimes', 'string', 'max:20', 'nullable'], 
                'page'         => ['sometimes', 'integer', 'min:1'],
            ];
        } elseif(in_array($route->getName(), ['event.get.confirmed','event.get.receive.barcode','event.get.awaiting.confirmation'])){
            return [
                'page'  => ['sometimes', 'integer', 'min:1'], 
                'date'  => ['sometimes', 'string', 'min:0', 'date_format:U'],
                'city'  => ['sometimes', 'integer', 'exists:cities,id'],
                'sort'  => ['sometimes', 'integer', 'in:'.implode(',', EventSort::getAllEventSortInt())],                
            ];
        } elseif($route->getName() == 'admin.get.events'){
            return [
                'status'         => ['sometimes', 'integer', 'in:' . implode(',', EventStatus::getAllEventStatusInt())],
                'search_query'   => ['sometimes', 'min:2', 'max:120', 'regex:/^[ا-یa-zA-Z0-9\-۰-۹ء-ي., \x{200C}]+$/u'],
                'date'           => ['sometimes', 'string', 'min:0', 'date_format:U'], 
                'page'           => ['sometimes', 'integer', 'min:1'],              
                'gender_restriction'     => ['sometimes' , 'nullable', 'integer', Rule::in([UserGender::MALE, UserGender::FEMALE,UserGender::BOTH])],

            ];
        } elseif($route->getName() == 'event.get.future'){
            return [
                'hashtags'    => ['sometimes', 'string', new Hashtag()],  
            ];
        } elseif ($route->getName() == 'event.store.task'){
            return [
                'influencer_id'  => ['sometimes', 'nullable', 'integer', 'exists:users,id', new InfluencerRole()],
                'tasks'          => ['required', 'array', 'min:1', 'max:10'], 
                'tasks.*'        => ['required', 'string', new Image()], 
                'score_ids'      => ['required', 'array', 'min:1', 'max:10'], 
                'score_ids.*'    => ['required', 'integer', new Score('score_ids')], 
                'score_values'   => ['required', 'array', 'min:1', 'max:10'],             
                'score_values.*' => ['required', 'integer', new Score('score_values')],             
            ];
        } elseif ($route->getName() == 'get.event.with-tasks'){
            return [
                'search_query'   => ['sometimes', 'nullable', 'min:2', 'max:120', 'regex:/^[ا-یa-zA-Z0-9\-۰-۹ء-ي., \x{200C}]+$/u'],
                'status'         => ['sometimes', 'nullable', 'integer', 'in:' . implode(',', EventTaskStatus::getAllEventStatusInt())], 
                'page'           => ['sometimes', 'integer', 'min:1'],                
            ];
        } elseif ($route->getName() == 'confirm.event.tasks'){
            return [
                'business_id'        => ['sometimes', 'nullable', 'integer', 'exists:users,id', new BusinessRole()],
                'is_confirmed'       => ['required', 'boolean'],
                'unconfirm_reason'   => ['required_if:is_confirmed,0', 'sometimes', 'nullable', 'min:2', 'max:255'],
                // 'unconfirm_reason'   => ['required_if:is_confirmed,0', 'sometimes', 'nullable', 'min:2', 'max:255', 'regex:/^[ا-یa-zA-Z۰-۹0-9\.,!?؟،۹ء-ي.,\/؛;\\s\x{200C}\\\\]+$/u'],            
            ];
        } elseif ($route->getName() == 'event.get.business.events.history'){
            return [
                'page'           => ['sometimes', 'integer', 'min:1'],                
            ];
        }elseif ($route->getName() == 'get.active.events') {
            return [
                'page' => ['sometimes', 'integer', 'min:1'],
            ];
        } elseif ($route->getName() == 'get.events.waiting-confirm') {
            return [
                'search_query'   => ['sometimes', 'min:2', 'max:120', 'regex:/^[ا-یa-zA-Z0-9\-۰-۹ء-ي., \x{200C}]+$/u'],
                'date'           => ['sometimes', 'string', 'min:0', 'date_format:U'], 
                'page'           => ['sometimes', 'integer', 'min:1'],
                'gender_restriction' => ['sometimes', 'nullable', 'integer', Rule::in([UserGender::MALE, UserGender::FEMALE, UserGender::BOTH])],
            ];
        } elseif ($route->getName() == 'confirm.event') {
            return [
                'is_confirmed'       => ['required', 'boolean'],
            ];
        } elseif (in_array($route->getName(), ['event.influencer', 'event.confirm.entry-code'])) {
            return [
                'entry_confirmation_code'  => ['required', 'integer', 'digits:4'],
            ];
        } elseif (in_array($route->getName(), ['copy.event'])) {
            return [
               'event_id'           => ['required', 'integer', 'exists:events,id'],
               'start_date'         => ['required', 'integer', 'min:0', 'date_format:U', new EventDate('end_date')],
               'end_date'           => ['required', 'integer', 'min:0', 'date_format:U', 'after:start_date', new EventDate('start_date')],
            ];  
        } elseif ($route->getName() == 'event.admin.active.task') {
            return [
                'influencer_id'  => ['sometimes', 'nullable', 'integer', 'exists:users,id', new InfluencerRole()],
            ];
        } elseif ($route->getName() == 'admin.event.unscored') {
            return [
                'date'               => ['sometimes', 'string', 'min:0', 'date_format:U'], 
                'page'               => ['sometimes', 'integer', 'min:1'],  
                'search_query'       => ['sometimes', 'min:2', 'max:100', 'regex:/^[ا-یa-zA-Z0-9\-۰-۹ء-ي., \x{200C}]+$/u'],  
                'influencer_name'    => ['sometimes', 'min:2', 'max:100', 'regex:/^[ا-یa-zA-Z0-9\-۰-۹ء-ي., \x{200C}]+$/u'],          
                'gender_restriction' => ['sometimes', 'nullable', 'integer', Rule::in([UserGender::MALE, UserGender::FEMALE, UserGender::BOTH])],

            ];  
        } 
    } */